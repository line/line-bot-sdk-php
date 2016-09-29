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
use LINE\LINEBot\Constant\MessageType;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\Tests\LINEBot\Util\DummyHttpClient;

class SendLocationTest extends \PHPUnit_Framework_TestCase
{
    public function testReplyLocation()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/reply', $url);

            $testRunner->assertEquals('REPLY-TOKEN', $data['replyToken']);
            $testRunner->assertEquals(1, count($data['messages']));
            $testRunner->assertEquals(MessageType::LOCATION, $data['messages'][0]['type']);
            $testRunner->assertEquals('Location test', $data['messages'][0]['title']);
            $testRunner->assertEquals('Tokyo Shibuya', $data['messages'][0]['address']);
            $testRunner->assertEquals(35.6566285, $data['messages'][0]['latitude']);
            $testRunner->assertEquals(139.6999638, $data['messages'][0]['longitude']);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->replyMessage(
            'REPLY-TOKEN',
            new LocationMessageBuilder('Location test', 'Tokyo Shibuya', 35.6566285, 139.6999638)
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testPushLocation()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(1, count($data['messages']));
            $testRunner->assertEquals(MessageType::LOCATION, $data['messages'][0]['type']);
            $testRunner->assertEquals('Location test', $data['messages'][0]['title']);
            $testRunner->assertEquals('Tokyo Shibuya', $data['messages'][0]['address']);
            $testRunner->assertEquals(35.6566285, $data['messages'][0]['latitude']);
            $testRunner->assertEquals(139.6999638, $data['messages'][0]['longitude']);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage(
            'DESTINATION',
            new LocationMessageBuilder('Location test', 'Tokyo Shibuya', 35.6566285, 139.6999638)
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }
}
