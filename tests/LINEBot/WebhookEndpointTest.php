<?php

/**
 * Copyright 2020 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the 'License'); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an 'AS IS' BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace LINE\Tests\LINEBot;

use LINE\LINEBot;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class WebhookEndpointTest extends TestCase
{
    public function testGetWebhookEndpointInfo()
    {
        $mock = function ($testRunner, $httpMethod, $url) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/channel/webhook/endpoint', $url);

            return [
                'endpoint' => 'https://example.herokuapp.com/test',
                'active' => 'true',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getWebhookEndpointInfo();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('https://example.herokuapp.com/test', $data['endpoint']);
        $this->assertEquals('true', $data['active']);
    }

    public function testSetWebhookEndpoint()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/channel/webhook/endpoint', $url);
            $testRunner->assertEquals('https://example.herokuapp.com/test', $data['endpoint']);

            return [];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->setWebhookEndpoint('https://example.herokuapp.com/test');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $this->assertEmpty($res->getJSONDecodedBody());
    }

    public function testTestWebhookEndpoint()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/channel/webhook/test', $url);
            $testRunner->assertEquals('https://example.herokuapp.com/test', $data['endpoint']);

            return [
                'success' => 'true',
                'timestamp' => '2020-09-30T05:38:20.031Z',
                'statusCode' => 200,
                'reason' => 'OK',
                'detail' => '200',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->testWebhookEndpoint('https://example.herokuapp.com/test');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('true', $data['success']);
        $this->assertEquals('2020-09-30T05:38:20.031Z', $data['timestamp']);
        $this->assertEquals(200, $data['statusCode']);
        $this->assertEquals('OK', $data['reason']);
        $this->assertEquals('200', $data['detail']);
    }
}
