<?php

/**
 * Copyright 2020 LINE Corporation
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
use PHPUnit\Framework\TestCase;

class BroadcastTest extends TestCase
{
    public function testBroadcast()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $headers) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/broadcast', $url);

            $testRunner->assertEquals(1, count($data['messages']));
            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text', $data['messages'][0]['text']);
            $testRunner->assertTrue(\in_array('X-Line-Retry-Key: 123e4567-e89b-12d3-a456-426614174000', $headers));

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->broadcast(
            new TextMessageBuilder("test text"),
            false,
            '123e4567-e89b-12d3-a456-426614174000'
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }
}
