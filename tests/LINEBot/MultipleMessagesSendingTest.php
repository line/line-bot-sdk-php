<?php
/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
namespace LINE\Tests\LINEBot;

use GuzzleHttp\Event\Emitter;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use LINE\LINEBot;
use LINE\LINEBot\Constant\ContentType;
use LINE\LINEBot\HTTPClient\GuzzleHTTPClient;
use LINE\LINEBot\Message\MultipleMessages;

class MultipleMessagesSendingTest extends \PHPUnit_Framework_TestCase
{
    public function testMultipleMessages()
    {
        $mock = new Mock([
            new Response(
                200,
                [],
                Stream::factory('{"failed":[],"messageId":"1460867315795","timestamp":1460867315795,"version":1}')
            ),
            new Response(
                400,
                [],
                Stream::factory('{"statusCode":"422","statusMessage":"invalid users"}')
            ),
            new Response(
                500,
                [],
                Stream::factory(
                    '{"statusCode":"500","statusMessage":"unexpected error found at call bot api sendMessage"}'
                )
            ),
        ]);

        $config = [
            'channelId' => '1000000000',
            'channelSecret' => 'testsecret',
            'channelMid' => 'TEST_MID',
        ];

        $histories = new History();
        $emitter = new Emitter();
        $emitter->attach($mock);
        $emitter->attach($histories);

        $sdk = new LINEBot($config, new GuzzleHTTPClient(array_merge($config, ['emitter' => $emitter])));

        $multipleMessages = (new MultipleMessages())
            ->addText('hello!')
            ->addImage('http://example.com/image.jpg', 'http://example.com/preview.jpg')
            ->addAudio('http://example.com/audio.m4a', 6000)
            ->addVideo('http://example.com/video.mp4', 'http://example.com/video_preview.jpg')
            ->addLocation('2 Chome-21-1 Shibuya Tokyo 150-0002, Japan', 35.658240, 139.703478)
            ->addSticker(1, 2, 100);

        $res = $sdk->sendMultipleMessages(['DUMMY_MID'], $multipleMessages);
        $this->assertInstanceOf('\LINE\LINEBot\Response\SucceededResponse', $res);
        /** @var \LINE\LINEBot\Response\SucceededResponse $res */
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertEmpty($res->getFailed());
        $this->assertEquals('1460867315795', $res->getMessageId());
        $this->assertEquals(1460867315795, $res->getTimestamp());
        $this->assertEquals(1, $res->getVersion());

        $res = $sdk->sendMultipleMessages(['INVALID_MID'], $multipleMessages);
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(400, $res->getHTTPStatus());
        $this->assertEquals('422', $res->getStatusCode());
        $this->assertEquals('invalid users', $res->getStatusMessage());

        $res = $sdk->sendMultipleMessages(['DUMMY_MID'], $multipleMessages);
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(500, $res->getHTTPStatus());
        $this->assertEquals('500', $res->getStatusCode());
        $this->assertEquals('unexpected error found at call bot api sendMessage', $res->getStatusMessage());

        $history = $histories->getIterator()[0];
        /** @var Request $req */
        $req = $history['request'];
        $this->assertEquals($req->getMethod(), 'POST');
        $this->assertEquals($req->getUrl(), 'https://trialbot-api.line.me/v1/events');

        $data = json_decode($req->getBody(), true);
        $this->assertEquals($data['eventType'], 140177271400161403);
        $this->assertEquals($data['to'], ['DUMMY_MID']);

        $this->assertEquals(sizeof($data['content']['messages']), 6);

        {
            $content = $data['content']['messages'][0];
            $this->assertEquals($content['text'], 'hello!');
            $this->assertEquals($content['contentType'], ContentType::TEXT);
        }
        {
            $content = $data['content']['messages'][1];
            $this->assertEquals($content['originalContentUrl'], 'http://example.com/image.jpg');
            $this->assertEquals($content['previewImageUrl'], 'http://example.com/preview.jpg');
            $this->assertEquals($content['contentType'], ContentType::IMAGE);
        }
        {
            $content = $data['content']['messages'][2];
            $this->assertEquals($content['originalContentUrl'], 'http://example.com/audio.m4a');
            $this->assertEquals($content['contentMetadata']['AUDLEN'], '6000');
            $this->assertEquals($content['contentType'], ContentType::AUDIO);
        }
        {
            $content = $data['content']['messages'][3];
            $this->assertEquals($content['originalContentUrl'], 'http://example.com/video.mp4');
            $this->assertEquals($content['previewImageUrl'], 'http://example.com/video_preview.jpg');
            $this->assertEquals($content['contentType'], ContentType::VIDEO);
        }
        {
            $content = $data['content']['messages'][4];
            $location = $content['location'];
            $this->assertEquals($content['text'], '2 Chome-21-1 Shibuya Tokyo 150-0002, Japan');
            $this->assertEquals($location['title'], $content['text']);
            $this->assertEquals($location['latitude'], 35.658240);
            $this->assertEquals($location['longitude'], 139.703478);
            $this->assertEquals($content['contentType'], ContentType::LOCATION);
        }
        {
            $content = $data['content']['messages'][5];
            $this->assertEquals($content['contentType'], ContentType::STICKER);
            $this->assertEquals($content['contentMetadata']['STKID'], '1');
            $this->assertEquals($content['contentMetadata']['STKPKGID'], '2');
            $this->assertEquals($content['contentMetadata']['STKVER'], '100');
        }

        $channelIdHeader = $req->getHeaderAsArray('X-Line-ChannelID');
        $this->assertEquals(sizeof($channelIdHeader), 1);
        $this->assertEquals($channelIdHeader[0], '1000000000');

        $channelSecretHeader = $req->getHeaderAsArray('X-Line-ChannelSecret');
        $this->assertEquals(sizeof($channelSecretHeader), 1);
        $this->assertEquals($channelSecretHeader[0], 'testsecret');

        $channelMidHeader = $req->getHeaderAsArray('X-Line-Trusted-User-With-ACL');
        $this->assertEquals(sizeof($channelMidHeader), 1);
        $this->assertEquals($channelMidHeader[0], 'TEST_MID');
    }
}