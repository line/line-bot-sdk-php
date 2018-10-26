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
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class SendMultiMessageTest extends TestCase
{
    public function testReplyMultiMessage()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/reply', $url);

            $testRunner->assertEquals('REPLY-TOKEN', $data['replyToken']);
            $testRunner->assertEquals(3, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('text2', $data['messages'][1]['text']);
            $testRunner->assertEquals(MessageType::AUDIO, $data['messages'][2]['type']);

            return ['status' => 200];
        };

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->replyMessage(
            'REPLY-TOKEN',
            (new LINEBot\MessageBuilder\MultiMessageBuilder())->add(new TextMessageBuilder('text1', 'text2'))
                ->add(new AudioMessageBuilder('https://example.com/audio.mp4', 1000))
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testPushMultiMessage()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(3, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('text2', $data['messages'][1]['text']);
            $testRunner->assertEquals(MessageType::AUDIO, $data['messages'][2]['type']);

            return ['status' => 200];
        };

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage(
            'DESTINATION',
            (new LINEBot\MessageBuilder\MultiMessageBuilder())->add(new TextMessageBuilder('text1', 'text2'))
                ->add(new AudioMessageBuilder('https://example.com/audio.mp4', 1000))
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }
}
