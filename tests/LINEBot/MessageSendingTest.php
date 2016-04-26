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

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\LINEBot;
use LINE\LINEBot\Constant\ContentType;
use LINE\LINEBot\Constant\RecipientType;
use LINE\LINEBot\HTTPClient\GuzzleHTTPClient;

class MessageSendingTest extends \PHPUnit_Framework_TestCase
{
    private static $config = [
        'channelId' => '1000000000',
        'channelSecret' => 'testsecret',
        'channelMid' => 'TEST_MID',
    ];

    public function testSendText()
    {
        $mock = new MockHandler([
            function (Request $req) {
                $this->assertEquals($req->getMethod(), 'POST');
                $this->assertEquals($req->getUri(), 'https://trialbot-api.line.me/v1/events');

                $data = json_decode($req->getBody(), true);
                $this->assertEquals($data['eventType'], 138311608800106203);
                $this->assertEquals($data['to'], ['DUMMY_MID']);
                $this->assertEquals($data['content']['text'], 'hello!');
                $this->assertEquals($data['content']['contentType'], ContentType::TEXT);
                $this->assertEquals($data['content']['toType'], RecipientType::USER);

                $channelIdHeader = $req->getHeader('X-Line-ChannelID');
                $this->assertEquals(sizeof($channelIdHeader), 1);
                $this->assertEquals($channelIdHeader[0], '1000000000');

                $channelSecretHeader = $req->getHeader('X-Line-ChannelSecret');
                $this->assertEquals(sizeof($channelSecretHeader), 1);
                $this->assertEquals($channelSecretHeader[0], 'testsecret');

                $channelMidHeader = $req->getHeader('X-Line-Trusted-User-With-ACL');
                $this->assertEquals(sizeof($channelMidHeader), 1);
                $this->assertEquals($channelMidHeader[0], 'TEST_MID');

                return new Response(
                    200,
                    [],
                    '{"failed":[],"messageId":"1460826285060","timestamp":1460826285060,"version":1}'
                );
            },
            new Response(400, [], '{"statusCode":"422","statusMessage":"invalid users"}'),
            new Response(
                500,
                [],
                '{"statusCode":"500","statusMessage":"unexpected error found at call bot api sendMessage"}'
            ),
        ]);
        $mockHandler = HandlerStack::create($mock);

        $sdk = new LINEBot(
            $this::$config,
            new GuzzleHTTPClient(array_merge($this::$config, ['handler' => $mockHandler]))
        );

        $res = $sdk->sendText(['DUMMY_MID'], 'hello!');
        $this->assertInstanceOf('\LINE\LINEBot\Response\SucceededResponse', $res);
        /** @var \LINE\LINEBot\Response\SucceededResponse $res */
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertEmpty($res->getFailed());
        $this->assertEquals('1460826285060', $res->getMessageId());
        $this->assertEquals(1460826285060, $res->getTimestamp());
        $this->assertEquals(1, $res->getVersion());

        $res = $sdk->sendText(['INVALID_MID'], 'hello!');
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(400, $res->getHTTPStatus());
        $this->assertEquals('422', $res->getStatusCode());
        $this->assertEquals('invalid users', $res->getStatusMessage());

        $res = $sdk->sendText(['DUMMY_MID'], 'SOMETHING WRONG PAYLOAD');
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(500, $res->getHTTPStatus());
        $this->assertEquals('500', $res->getStatusCode());
        $this->assertEquals('unexpected error found at call bot api sendMessage', $res->getStatusMessage());
    }

    public function testSendImage()
    {
        $mock = new MockHandler([
            function (Request $req) {
                $this->assertEquals($req->getMethod(), 'POST');
                $this->assertEquals($req->getUri(), 'https://trialbot-api.line.me/v1/events');

                $data = json_decode($req->getBody(), true);
                $this->assertEquals($data['eventType'], 138311608800106203);
                $this->assertEquals($data['to'], ['DUMMY_MID']);
                $this->assertEquals($data['content']['originalContentUrl'], 'http://example.com/image.jpg');
                $this->assertEquals($data['content']['previewImageUrl'], 'http://example.com/preview.jpg');
                $this->assertEquals($data['content']['contentType'], ContentType::IMAGE);
                $this->assertEquals($data['content']['toType'], RecipientType::USER);

                $channelIdHeader = $req->getHeader('X-Line-ChannelID');
                $this->assertEquals(sizeof($channelIdHeader), 1);
                $this->assertEquals($channelIdHeader[0], '1000000000');

                $channelSecretHeader = $req->getHeader('X-Line-ChannelSecret');
                $this->assertEquals(sizeof($channelSecretHeader), 1);
                $this->assertEquals($channelSecretHeader[0], 'testsecret');

                $channelMidHeader = $req->getHeader('X-Line-Trusted-User-With-ACL');
                $this->assertEquals(sizeof($channelMidHeader), 1);
                $this->assertEquals($channelMidHeader[0], 'TEST_MID');

                return new Response(
                    200,
                    [],
                    '{"failed":[],"messageId":"1460826285060","timestamp":1460826285060,"version":1}'
                );
            },
            new Response(400, [], '{"statusCode":"422","statusMessage":"invalid users"}'),
            new Response(
                500,
                [],
                '{"statusCode":"500","statusMessage":"unexpected error found at call bot api sendMessage"}'
            ),
        ]);
        $mockHandler = HandlerStack::create($mock);

        $sdk = new LINEBot(
            $this::$config,
            new GuzzleHTTPClient(array_merge($this::$config, ['handler' => $mockHandler]))
        );

        $res = $sdk->sendImage(['DUMMY_MID'], 'http://example.com/image.jpg', 'http://example.com/preview.jpg');
        $this->assertInstanceOf('\LINE\LINEBot\Response\SucceededResponse', $res);
        /** @var \LINE\LINEBot\Response\SucceededResponse $res */
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertEmpty($res->getFailed());
        $this->assertEquals('1460826285060', $res->getMessageId());
        $this->assertEquals(1460826285060, $res->getTimestamp());
        $this->assertEquals(1, $res->getVersion());

        $res = $sdk->sendImage(['INVALID_MID'], 'http://example.com/image.jpg', 'http://example.com/preview.jpg');
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(400, $res->getHTTPStatus());
        $this->assertEquals('422', $res->getStatusCode());
        $this->assertEquals('invalid users', $res->getStatusMessage());

        $res = $sdk->sendImage(['DUMMY_MID'], 'http://example.com/image.jpg', 'http://example.com/preview.jpg');
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(500, $res->getHTTPStatus());
        $this->assertEquals('500', $res->getStatusCode());
        $this->assertEquals('unexpected error found at call bot api sendMessage', $res->getStatusMessage());
    }

    public function testSendVideo()
    {
        $mock = new MockHandler([
            function (Request $req) {
                $this->assertEquals($req->getMethod(), 'POST');
                $this->assertEquals($req->getUri(), 'https://trialbot-api.line.me/v1/events');

                $data = json_decode($req->getBody(), true);
                $this->assertEquals($data['eventType'], 138311608800106203);
                $this->assertEquals($data['to'], ['DUMMY_MID']);
                $this->assertEquals($data['content']['originalContentUrl'], 'http://example.com/video.mp4');
                $this->assertEquals($data['content']['previewImageUrl'], 'http://example.com/preview.jpg');
                $this->assertEquals($data['content']['contentType'], ContentType::VIDEO);
                $this->assertEquals($data['content']['toType'], RecipientType::USER);

                $channelIdHeader = $req->getHeader('X-Line-ChannelID');
                $this->assertEquals(sizeof($channelIdHeader), 1);
                $this->assertEquals($channelIdHeader[0], '1000000000');

                $channelSecretHeader = $req->getHeader('X-Line-ChannelSecret');
                $this->assertEquals(sizeof($channelSecretHeader), 1);
                $this->assertEquals($channelSecretHeader[0], 'testsecret');

                $channelMidHeader = $req->getHeader('X-Line-Trusted-User-With-ACL');
                $this->assertEquals(sizeof($channelMidHeader), 1);
                $this->assertEquals($channelMidHeader[0], 'TEST_MID');

                return new Response(
                    200,
                    [],
                    '{"failed":[],"messageId":"1460867315795","timestamp":1460867315795,"version":1}'
                );
            },
            new Response(400, [], '{"statusCode":"422","statusMessage":"invalid users"}'),
            new Response(
                500,
                [],
                '{"statusCode":"500","statusMessage":"unexpected error found at call bot api sendMessage"}'
            ),
        ]);
        $mockHandler = HandlerStack::create($mock);

        $sdk = new LINEBot(
            $this::$config,
            new GuzzleHTTPClient(array_merge($this::$config, ['handler' => $mockHandler]))
        );

        $res = $sdk->sendVideo(['DUMMY_MID'], 'http://example.com/video.mp4', 'http://example.com/preview.jpg');
        $this->assertInstanceOf('\LINE\LINEBot\Response\SucceededResponse', $res);
        /** @var \LINE\LINEBot\Response\SucceededResponse $res */
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertEmpty($res->getFailed());
        $this->assertEquals('1460867315795', $res->getMessageId());
        $this->assertEquals(1460867315795, $res->getTimestamp());
        $this->assertEquals(1, $res->getVersion());

        $res = $sdk->sendVideo(['INVALID_MID'], 'http://example.com/video.mp4', 'http://example.com/preview.jpg');
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(400, $res->getHTTPStatus());
        $this->assertEquals('422', $res->getStatusCode());
        $this->assertEquals('invalid users', $res->getStatusMessage());

        $res = $sdk->sendVideo(['DUMMY_MID'], 'http://example.com/video.mp4', 'http://example.com/preview.jpg');
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(500, $res->getHTTPStatus());
        $this->assertEquals('500', $res->getStatusCode());
        $this->assertEquals('unexpected error found at call bot api sendMessage', $res->getStatusMessage());
    }

    public function testSendAudio()
    {
        $mock = new MockHandler([
            function (Request $req) {
                $this->assertEquals($req->getMethod(), 'POST');
                $this->assertEquals($req->getUri(), 'https://trialbot-api.line.me/v1/events');

                $data = json_decode($req->getBody(), true);
                $this->assertEquals($data['eventType'], 138311608800106203);
                $this->assertEquals($data['to'], ['DUMMY_MID']);
                $this->assertEquals($data['content']['originalContentUrl'], 'http://example.com/sound.m4a');
                $this->assertEquals($data['content']['contentMetadata']['AUDLEN'], '5000');
                $this->assertEquals($data['content']['contentType'], ContentType::AUDIO);
                $this->assertEquals($data['content']['toType'], RecipientType::USER);

                $channelIdHeader = $req->getHeader('X-Line-ChannelID');
                $this->assertEquals(sizeof($channelIdHeader), 1);
                $this->assertEquals($channelIdHeader[0], '1000000000');

                $channelSecretHeader = $req->getHeader('X-Line-ChannelSecret');
                $this->assertEquals(sizeof($channelSecretHeader), 1);
                $this->assertEquals($channelSecretHeader[0], 'testsecret');

                $channelMidHeader = $req->getHeader('X-Line-Trusted-User-With-ACL');
                $this->assertEquals(sizeof($channelMidHeader), 1);
                $this->assertEquals($channelMidHeader[0], 'TEST_MID');

                return new Response(
                    200,
                    [],
                    '{"failed":[],"messageId":"1460867315795","timestamp":1460867315795,"version":1}'
                );
            },
            new Response(400, [], '{"statusCode":"422","statusMessage":"invalid users"}'),
            new Response(
                500,
                [],
                '{"statusCode":"500","statusMessage":"unexpected error found at call bot api sendMessage"}'
            ),
        ]);
        $mockHandler = HandlerStack::create($mock);

        $sdk = new LINEBot(
            $this::$config,
            new GuzzleHTTPClient(array_merge($this::$config, ['handler' => $mockHandler]))
        );

        $res = $sdk->sendAudio(['DUMMY_MID'], 'http://example.com/sound.m4a', 5000);
        $this->assertInstanceOf('\LINE\LINEBot\Response\SucceededResponse', $res);
        /** @var \LINE\LINEBot\Response\SucceededResponse $res */
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertEmpty($res->getFailed());
        $this->assertEquals('1460867315795', $res->getMessageId());
        $this->assertEquals(1460867315795, $res->getTimestamp());
        $this->assertEquals(1, $res->getVersion());

        $res = $sdk->sendAudio(['INVALID_MID'], 'http://example.com/sound.m4a', 5000);
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(400, $res->getHTTPStatus());
        $this->assertEquals('422', $res->getStatusCode());
        $this->assertEquals('invalid users', $res->getStatusMessage());

        $res = $sdk->sendAudio(['DUMMY_MID'], 'http://example.com/sound.m4a', 5000);
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(500, $res->getHTTPStatus());
        $this->assertEquals('500', $res->getStatusCode());
        $this->assertEquals('unexpected error found at call bot api sendMessage', $res->getStatusMessage());
    }

    public function testSendLocation()
    {
        $mock = new MockHandler([
            function (Request $req) {
                $this->assertEquals($req->getMethod(), 'POST');
                $this->assertEquals($req->getUri(), 'https://trialbot-api.line.me/v1/events');

                $data = json_decode($req->getBody(), true);
                $this->assertEquals($data['eventType'], 138311608800106203);
                $this->assertEquals($data['to'], ['DUMMY_MID']);

                $content = $data['content'];
                $location = $content['location'];

                $this->assertEquals($content['contentType'], ContentType::LOCATION);
                $this->assertEquals($content['text'], '2 Chome-21-1 Shibuya Tokyo 150-0002, Japan');
                $this->assertEquals($location['title'], $content['text']);
                $this->assertEquals($location['latitude'], 35.658240);
                $this->assertEquals($location['longitude'], 139.703478);

                $channelIdHeader = $req->getHeader('X-Line-ChannelID');
                $this->assertEquals(sizeof($channelIdHeader), 1);
                $this->assertEquals($channelIdHeader[0], '1000000000');

                $channelSecretHeader = $req->getHeader('X-Line-ChannelSecret');
                $this->assertEquals(sizeof($channelSecretHeader), 1);
                $this->assertEquals($channelSecretHeader[0], 'testsecret');

                $channelMidHeader = $req->getHeader('X-Line-Trusted-User-With-ACL');
                $this->assertEquals(sizeof($channelMidHeader), 1);
                $this->assertEquals($channelMidHeader[0], 'TEST_MID');

                return new Response(
                    200,
                    [],
                    '{"failed":[],"messageId":"1460867315795","timestamp":1460867315795,"version":1}'
                );
            },
            new Response(400, [], '{"statusCode":"422","statusMessage":"invalid users"}'),
            new Response(
                500,
                [],
                '{"statusCode":"500","statusMessage":"unexpected error found at call bot api sendMessage"}'
            ),
        ]);
        $mockHandler = HandlerStack::create($mock);

        $sdk = new LINEBot(
            $this::$config,
            new GuzzleHTTPClient(array_merge($this::$config, ['handler' => $mockHandler]))
        );

        $res = $sdk->sendLocation(['DUMMY_MID'], '2 Chome-21-1 Shibuya Tokyo 150-0002, Japan', 35.658240, 139.703478);
        $this->assertInstanceOf('\LINE\LINEBot\Response\SucceededResponse', $res);
        /** @var \LINE\LINEBot\Response\SucceededResponse $res */
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertEmpty($res->getFailed());
        $this->assertEquals('1460867315795', $res->getMessageId());
        $this->assertEquals(1460867315795, $res->getTimestamp());
        $this->assertEquals(1, $res->getVersion());

        $res = $sdk->sendLocation(['INVALID_MID'], '2 Chome-21-1 Shibuya Tokyo 150-0002, Japan', 35.658240, 139.703478);
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(400, $res->getHTTPStatus());
        $this->assertEquals('422', $res->getStatusCode());
        $this->assertEquals('invalid users', $res->getStatusMessage());

        $res = $sdk->sendLocation(['DUMMY_MID'], '2 Chome-21-1 Shibuya Tokyo 150-0002, Japan', 35.658240, 139.703478);
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(500, $res->getHTTPStatus());
        $this->assertEquals('500', $res->getStatusCode());
        $this->assertEquals('unexpected error found at call bot api sendMessage', $res->getStatusMessage());
    }

    public function testSendSticker()
    {
        $mock = new MockHandler([
            function (Request $req) {
                $this->assertEquals($req->getMethod(), 'POST');
                $this->assertEquals($req->getUri(), 'https://trialbot-api.line.me/v1/events');

                $data = json_decode($req->getBody(), true);
                $this->assertEquals($data['eventType'], 138311608800106203);
                $this->assertEquals($data['to'], ['DUMMY_MID']);

                $this->assertEquals($data['content']['contentType'], ContentType::STICKER);
                $this->assertEquals($data['content']['contentMetadata']['STKID'], '1');
                $this->assertEquals($data['content']['contentMetadata']['STKPKGID'], '2');
                $this->assertEquals($data['content']['contentMetadata']['STKVER'], '100');

                $channelIdHeader = $req->getHeader('X-Line-ChannelID');
                $this->assertEquals(sizeof($channelIdHeader), 1);
                $this->assertEquals($channelIdHeader[0], '1000000000');

                $channelSecretHeader = $req->getHeader('X-Line-ChannelSecret');
                $this->assertEquals(sizeof($channelSecretHeader), 1);
                $this->assertEquals($channelSecretHeader[0], 'testsecret');

                $channelMidHeader = $req->getHeader('X-Line-Trusted-User-With-ACL');
                $this->assertEquals(sizeof($channelMidHeader), 1);
                $this->assertEquals($channelMidHeader[0], 'TEST_MID');

                return new Response(
                    200,
                    [],
                    '{"failed":[],"messageId":"1460867315795","timestamp":1460867315795,"version":1}'
                );
            },
            new Response(400, [], '{"statusCode":"422","statusMessage":"invalid users"}'),
            new Response(
                500,
                [],
                '{"statusCode":"500","statusMessage":"unexpected error found at call bot api sendMessage"}'
            ),
        ]);
        $mockHandler = HandlerStack::create($mock);

        $sdk = new LINEBot(
            $this::$config,
            new GuzzleHTTPClient(array_merge($this::$config, ['handler' => $mockHandler]))
        );

        $res = $sdk->sendSticker(['DUMMY_MID'], 1, 2, 100);
        $this->assertInstanceOf('\LINE\LINEBot\Response\SucceededResponse', $res);
        /** @var \LINE\LINEBot\Response\SucceededResponse $res */
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertEmpty($res->getFailed());
        $this->assertEquals('1460867315795', $res->getMessageId());
        $this->assertEquals(1460867315795, $res->getTimestamp());
        $this->assertEquals(1, $res->getVersion());

        $res = $sdk->sendSticker(['INVALID_MID'], 1, 1, 100);
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(400, $res->getHTTPStatus());
        $this->assertEquals('422', $res->getStatusCode());
        $this->assertEquals('invalid users', $res->getStatusMessage());

        $res = $sdk->sendSticker(['DUMMY_MID'], 1, 1, 100);
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(500, $res->getHTTPStatus());
        $this->assertEquals('500', $res->getStatusCode());
        $this->assertEquals('unexpected error found at call bot api sendMessage', $res->getStatusMessage());
    }
}