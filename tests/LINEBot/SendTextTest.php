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
use LINE\LINEBot\MessageBuilder\Text\EmojiTextBuilder;
use LINE\LINEBot\MessageBuilder\Text\EmojiBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\SenderBuilder\SenderMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class SendTextTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testReplySingleText()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
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

    /**
     * @throws \ReflectionException
     */
    public function testReplyMultiTexts()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
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
            /** @var \PHPUnit\Framework\TestCase $testRunner */
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
            /** @var \PHPUnit\Framework\TestCase $testRunner */
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

    public function testReplyMessageWithMultiTextsContainsEmoji()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/reply', $url);

            $testRunner->assertEquals('REPLY-TOKEN', $data['replyToken']);
            $testRunner->assertEquals(3, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('$ test$ text2', $data['messages'][1]['text']);
            $testRunner->assertEquals([
                [
                    'index' => 0,
                    'productId' => '5ac1bfd5040ab15980c9b435',
                    'emojiId' => '001',
                ], [
                    'index' => 6,
                    'productId' => '5ac1bfd5040ab15980c9b435',
                    'emojiId' => '001',
                ],
            ], $data['messages'][1]['emojis']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][2]['type']);
            $testRunner->assertEquals('test text3', $data['messages'][2]['text']);

            return ['status' => 200];
        };

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->replyMessage('REPLY-TOKEN', new TextMessageBuilder(
            'test text1',
            new EmojiTextBuilder(
                '$ test$ text2',
                new EmojiBuilder(0, '5ac1bfd5040ab15980c9b435', '001'),
                new EmojiBuilder(6, '5ac1bfd5040ab15980c9b435', '001')
            ),
            'test text3'
        ));

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testPushTextMessage()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $headers) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
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
            $testRunner->assertTrue(\in_array('X-Line-Retry-Key: 123e4567-e89b-12d3-a456-426614174000', $headers));
            $testRunner->assertEquals('promotion_a', $data['customAggregationUnits'][0]);

            return ['status' => 200];
        };

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage(
            'DESTINATION',
            new TextMessageBuilder('test text1', 'test text2', 'test text3'),
            false,
            '123e4567-e89b-12d3-a456-426614174000',
            ['promotion_a']
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testTextMessageWithQuickReply()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(2, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('test text2', $data['messages'][1]['text']);
            $testRunner->assertEquals([
                'items' => [
                    [
                        'type' => 'action',
                        'imageUrl' => 'https://foo.bar',
                        'action' => ['type' => 'message', 'label' => 'LabelText', 'text' => 'Text66'],
                    ],
                ],
            ], $data['messages'][1]['quickReply']);

            return ['status' => 200];
        };

        $quickReply = new QuickReplyMessageBuilder([
            new QuickReplyButtonBuilder(new MessageTemplateActionBuilder('LabelText', 'Text66'), 'https://foo.bar'),
        ]);

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage('DESTINATION', new TextMessageBuilder('test text1', 'test text2', $quickReply));

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testTextMessageWithQuickReplyContainsEmoji()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(2, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('$ test$ text2', $data['messages'][1]['text']);
            $testRunner->assertEquals([
                [
                    'index' => 0,
                    'productId' => '5ac1bfd5040ab15980c9b435',
                    'emojiId' => '001',
                ], [
                    'index' => 6,
                    'productId' => '5ac1bfd5040ab15980c9b435',
                    'emojiId' => '001',
                ],
            ], $data['messages'][1]['emojis']);
            $testRunner->assertEquals([
                'items' => [
                    [
                        'type' => 'action',
                        'imageUrl' => 'https://foo.bar',
                        'action' => ['type' => 'message', 'label' => 'LabelText', 'text' => 'Text66'],
                    ],
                ],
            ], $data['messages'][1]['quickReply']);

            return ['status' => 200];
        };

        $quickReply = new QuickReplyMessageBuilder([
            new QuickReplyButtonBuilder(new MessageTemplateActionBuilder('LabelText', 'Text66'), 'https://foo.bar'),
        ]);

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage('DESTINATION', new TextMessageBuilder(
            'test text1',
            new EmojiTextBuilder(
                '$ test$ text2',
                new EmojiBuilder(0, '5ac1bfd5040ab15980c9b435', '001'),
                new EmojiBuilder(6, '5ac1bfd5040ab15980c9b435', '001')
            ),
            $quickReply
        ));

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testTextMessageWithSender()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(2, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('test text2', $data['messages'][1]['text']);
            $testRunner->assertEquals([
                'name' => 'test1',
                'iconUrl' => 'https://example.com/test2',
            ], $data['messages'][0]['sender']);
            $testRunner->assertEquals([
                'name' => 'test1',
                'iconUrl' => 'https://example.com/test2',
            ], $data['messages'][1]['sender']);

            return ['status' => 200];
        };

        $sender = new SenderMessageBuilder("test1", "https://example.com/test2");

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage('DESTINATION', new TextMessageBuilder('test text1', 'test text2', $sender));

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testTextMessageWithSenderAndQuickReply()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(2, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text1', $data['messages'][0]['text']);
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][1]['type']);
            $testRunner->assertEquals('test text2', $data['messages'][1]['text']);
            $testRunner->assertEquals([
                'name' => 'test1',
                'iconUrl' => 'https://example.com/test2',
            ], $data['messages'][0]['sender']);
            $testRunner->assertEquals([
                'name' => 'test1',
                'iconUrl' => 'https://example.com/test2',
            ], $data['messages'][1]['sender']);
            $testRunner->assertEquals([
                'items' => [
                    [
                        'type' => 'action',
                        'imageUrl' => 'https://foo.bar',
                        'action' => ['type' => 'message', 'label' => 'LabelText', 'text' => 'Text66'],
                    ],
                ],
            ], $data['messages'][1]['quickReply']);

            return ['status' => 200];
        };

        $quickReply = new QuickReplyMessageBuilder([
            new QuickReplyButtonBuilder(new MessageTemplateActionBuilder('LabelText', 'Text66'), 'https://foo.bar'),
        ]);

        $sender = new SenderMessageBuilder("test1", "https://example.com/test2");

        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage(
            'DESTINATION',
            new TextMessageBuilder('test text1', 'test text2', $sender, $quickReply)
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }
}
