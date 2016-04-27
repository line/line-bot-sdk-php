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

use GuzzleHttp\Client;
use GuzzleHttp\Event\Emitter;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\GuzzleHTTPClient;
use LINE\LINEBot\Message\RichMessage\Markup;

class RichMessageSendingTest extends \PHPUnit_Framework_TestCase
{
    public function testRichMessage()
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

        $markup = (new Markup(1040))
            ->setAction('SOMETHING', 'something', 'https://line.me')
            ->addListener('SOMETHING', 0, 0, 520, 520);

        $res = $sdk->sendRichMessage(["DUMMY_MID"], 'http://example.com/image.jpg', "Alt text", $markup);
        $this->assertInstanceOf('\LINE\LINEBot\Response\SucceededResponse', $res);
        /** @var \LINE\LINEBot\Response\SucceededResponse $res */
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertEmpty($res->getFailed());
        $this->assertEquals('1460867315795', $res->getMessageId());
        $this->assertEquals(1460867315795, $res->getTimestamp());
        $this->assertEquals(1, $res->getVersion());

        $res = $sdk->sendRichMessage(["INVALID_MID"], 'http://example.com/image.jpg', "Alt text", $markup);
        $this->assertInstanceOf('\LINE\LINEBot\Response\FailedResponse', $res);
        /** @var \LINE\LINEBot\Response\FailedResponse $res */
        $this->assertFalse($res->isSucceeded());
        $this->assertEquals(400, $res->getHTTPStatus());
        $this->assertEquals('422', $res->getStatusCode());
        $this->assertEquals('invalid users', $res->getStatusMessage());

        $res = $sdk->sendRichMessage(["DUMMY_MID"], 'http://example.com/image.jpg', "Alt text", $markup);
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
        $this->assertEquals($data['eventType'], 138311608800106203);
        $this->assertEquals($data['to'], ['DUMMY_MID']);
        $this->assertEquals($data['content']['contentMetadata']['ALT_TEXT'], 'Alt text');
        $this->assertEquals(
            $data['content']['contentMetadata']['DOWNLOAD_URL'],
            'http://example.com/image.jpg'
        );

        $json = $data['content']['contentMetadata']['MARKUP_JSON'];
        $this->assertEquals(
            json_decode($json, true),
            [
                'scenes' => [
                    'scene1' => [
                        'listeners' => [
                            [
                                'params' => [
                                    0,
                                    0,
                                    520,
                                    520,
                                ],
                                'type' => 'touch',
                                'action' => 'SOMETHING',
                            ],
                        ],
                        'draws' => [
                            [
                                'image' => 'image1',
                                'x' => 0,
                                'y' => 0,
                                'w' => 1040,
                                'h' => 1040,
                            ],
                        ],
                    ],
                ],
                'images' => [
                    'image1' => [
                        'x' => 0,
                        'y' => 0,
                        'w' => 1040,
                        'h' => 1040,
                    ],
                ],
                'actions' => [
                    'SOMETHING' => [
                        'text' => 'something',
                        'params' => [
                            'linkUri' => 'https://line.me',
                        ],
                        'type' => 'web',
                    ],
                ],
                'canvas' => [
                    'initialScene' => 'scene1',
                    'width' => 1040,
                    'height' => 1040,
                ],
            ]
        );

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