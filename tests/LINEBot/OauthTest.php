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

class OauthTest extends TestCase
{
    public function testCreateChannelAccessToken()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $header) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/oauth/accessToken', $url);
            $testRunner->assertEquals([
                'grant_type' => 'client_credentials',
                'client_id' => 'CHANNEL-ID',
                'client_secret' => 'CHANNEL-SECRET',
            ], $data);
            $testRunner->assertEquals(['Content-Type: application/x-www-form-urlencoded'], $header);

            return [
                'access_token' => 'W1TeHCgfH2Liwa.....',
                'expires_in' => 2592000,
                'token_type' => 'Bearer'
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->createChannelAccessToken('CHANNEL-ID');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('W1TeHCgfH2Liwa.....', $data['access_token']);
        $this->assertEquals(2592000, $data['expires_in']);
        $this->assertEquals('Bearer', $data['token_type']);
    }

    public function testRevokeChannelAccessToken()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $header) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/oauth/revoke', $url);
            $testRunner->assertEquals(['access_token' => 'CHANNEL-ACCESS-TOKEN'], $data);
            $testRunner->assertEquals(['Content-Type: application/x-www-form-urlencoded'], $header);

            return [];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->revokeChannelAccessToken('CHANNEL-ACCESS-TOKEN');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals([], $res->getJSONDecodedBody());
    }
}
