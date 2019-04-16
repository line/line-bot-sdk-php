<?php

/**
 * Copyright 2019 LINE Corporation
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
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;
use DateTime;
use DateTimeZone;

class GetNumberOfMessagesSentTest extends TestCase
{
    public function testGetNumberOfSentReplyMessages()
    {
        $date = new DateTime();
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($date) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/delivery/reply', $url);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $testRunner->assertEquals([
                'date' => $date->format('Ymd')
            ], $data);
            return [
                'status' => 'ready',
                'success' => 10000
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNumberOfSentReplyMessages($date);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ready', $data['status']);
        $this->assertEquals(10000, $data['success']);
    }

    public function testGetNumberOfSentPushMessages()
    {
        $date = new DateTime();
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($date) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/delivery/push', $url);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $testRunner->assertEquals([
                'date' => $date->format('Ymd')
            ], $data);
            return [
                'status' => 'ready',
                'success' => 10000
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNumberOfSentPushMessages($date);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ready', $data['status']);
        $this->assertEquals(10000, $data['success']);
    }

    public function testGetNumberOfSentMulticastMessages()
    {
        $date = new DateTime();
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($date) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/delivery/multicast', $url);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $testRunner->assertEquals([
                'date' => $date->format('Ymd')
            ], $data);
            return [
                'status' => 'ready',
                'success' => 10000
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNumberOfSentMulticastMessages($date);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ready', $data['status']);
        $this->assertEquals(10000, $data['success']);
    }
}
