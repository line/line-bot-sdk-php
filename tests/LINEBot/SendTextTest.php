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
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\Tests\LINEBot\Util\DummyHttpClient;

class SendTextTest extends \PHPUnit_Framework_TestCase
{
    public function testReplySingleText()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/reply', $url);

            $testRunner->assertEquals('REPLY-TOKEN', $data['replyToken']);
            $testRunner->assertEquals(1, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text', $data['messages'][0]['text']);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->replyText('REPLY-TOKEN', 'test text');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testReplyMultiTexts()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/reply', $url);

            $testRunner->assertEquals('REPLY-TOKEN', $data['replyToken']);
            $testRunner->assertEquals(3, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('test text2', $data['messages'][1]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][2]['type']);
            $testRunner->assertEquals('test text3', $data['messages'][2]['text']);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->replyText('REPLY-TOKEN', 'test text1', 'test text2', 'test text3');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testReplyMessageWithSingleText()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/reply', $url);

            $testRunner->assertEquals('REPLY-TOKEN', $data['replyToken']);
            $testRunner->assertEquals(1, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text', $data['messages'][0]['text']);

            return ['status' => 200];
        };

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->replyMessage('REPLY-TOKEN', new TextMessageBuilder('test text'));

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testReplyMessageWithMultiTexts()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/reply', $url);

            $testRunner->assertEquals('REPLY-TOKEN', $data['replyToken']);
            $testRunner->assertEquals(3, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('test text2', $data['messages'][1]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][2]['type']);
            $testRunner->assertEquals('test text3', $data['messages'][2]['text']);

            return ['status' => 200];
        };

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->replyMessage('REPLY-TOKEN', new TextMessageBuilder('test text1', 'test text2', 'test text3'));

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testPushTextMessage()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(3, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('test text2', $data['messages'][1]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][2]['type']);
            $testRunner->assertEquals('test text3', $data['messages'][2]['text']);

            return ['status' => 200];
        };

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage('DESTINATION', new TextMessageBuilder('test text1', 'test text2', 'test text3'));

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }
}
