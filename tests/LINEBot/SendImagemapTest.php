<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace LINE\Tests\LINEBot;

use LINE\LINEBot;
use LINE\LINEBot\Constant\ActionType;
use LINE\LINEBot\Constant\MessageType;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class SendImagemapTest extends TestCase
{
    public function testReplyImagemap()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/reply', $url);

            $testRunner->assertEquals('REPLY-TOKEN', $data['replyToken']);
            $testRunner->assertEquals(1, count($data['messages']));

            $message = $data['messages'][0];
            $testRunner->assertEquals(MessageType::IMAGEMAP, $message['type']);
            $testRunner->assertEquals('https://example.com/imagemap_base', $message['baseUrl']);
            $testRunner->assertEquals('alt test', $message['altText']);
            $testRunner->assertEquals(1040, $message['baseSize']['width']);
            $testRunner->assertEquals(1040, $message['baseSize']['height']);

            $testRunner->assertEquals(2, count($message['actions']));
            $testRunner->assertEquals(ActionType::URI, $message['actions'][0]['type']);
            $testRunner->assertEquals(0, $message['actions'][0]['area']['x']);
            $testRunner->assertEquals(0, $message['actions'][0]['area']['y']);
            $testRunner->assertEquals(1040, $message['actions'][0]['area']['width']);
            $testRunner->assertEquals(520, $message['actions'][0]['area']['height']);

            $testRunner->assertEquals(ActionType::MESSAGE, $message['actions'][1]['type']);
            $testRunner->assertEquals(0, $message['actions'][1]['area']['x']);
            $testRunner->assertEquals(520, $message['actions'][1]['area']['y']);
            $testRunner->assertEquals(1040, $message['actions'][1]['area']['width']);
            $testRunner->assertEquals(520, $message['actions'][1]['area']['height']);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->replyMessage(
            'REPLY-TOKEN',
            new ImagemapMessageBuilder(
                'https://example.com/imagemap_base',
                'alt test',
                new BaseSizeBuilder(1040, 1040),
                [
                    new ImagemapUriActionBuilder(
                        'https://example.com/foo/bar',
                        new AreaBuilder(0, 0, 1040, 520)
                    ),
                    new ImagemapMessageActionBuilder(
                        'Fortune',
                        new AreaBuilder(0, 520, 1040, 520)
                    ),
                ]
            )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testPushImagemap()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(1, count($data['messages']));

            $message = $data['messages'][0];
            $testRunner->assertEquals(MessageType::IMAGEMAP, $message['type']);
            $testRunner->assertEquals('https://example.com/imagemap_base', $message['baseUrl']);
            $testRunner->assertEquals('alt test', $message['altText']);
            $testRunner->assertEquals(1040, $message['baseSize']['width']);
            $testRunner->assertEquals(1040, $message['baseSize']['height']);

            $testRunner->assertEquals(2, count($message['actions']));
            $testRunner->assertEquals(ActionType::URI, $message['actions'][0]['type']);
            $testRunner->assertEquals(0, $message['actions'][0]['area']['x']);
            $testRunner->assertEquals(0, $message['actions'][0]['area']['y']);
            $testRunner->assertEquals(1040, $message['actions'][0]['area']['width']);
            $testRunner->assertEquals(520, $message['actions'][0]['area']['height']);

            $testRunner->assertEquals(ActionType::MESSAGE, $message['actions'][1]['type']);
            $testRunner->assertEquals(0, $message['actions'][1]['area']['x']);
            $testRunner->assertEquals(520, $message['actions'][1]['area']['y']);
            $testRunner->assertEquals(1040, $message['actions'][1]['area']['width']);
            $testRunner->assertEquals(520, $message['actions'][1]['area']['height']);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage(
            'DESTINATION',
            new ImagemapMessageBuilder(
                'https://example.com/imagemap_base',
                'alt test',
                new BaseSizeBuilder(1040, 1040),
                [
                    new ImagemapUriActionBuilder(
                        'https://example.com/foo/bar',
                        new AreaBuilder(0, 0, 1040, 520)
                    ),
                    new ImagemapMessageActionBuilder(
                        'Fortune',
                        new AreaBuilder(0, 520, 1040, 520)
                    ),
                ]
            )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }
}
