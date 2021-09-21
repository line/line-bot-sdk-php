<?php

/**
 * Copyright 2021 LINE Corporation
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

class RichMenuAliasTest extends TestCase
{
    public function testCreateRichMenuAlias()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/alias', $url);
            $testRunner->assertEquals([
                'richMenuAliasId' => 'richmenu-alias-a',
                'richMenuId' => 'richmenu-862e6ad6c267d2ddf3f42bc78554f6a4'
            ], $data);

            return [];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createRichMenuAlias('richmenu-alias-a', 'richmenu-862e6ad6c267d2ddf3f42bc78554f6a4');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
    }

    public function testDeleteRichMenuAlias()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('DELETE', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/alias/richmenu-alias-a', $url);

            return [];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->deleteRichMenuAlias('richmenu-alias-a');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
    }

    public function testUpdateRichMenuAlias()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/alias/richmenu-alias-a', $url);
            $testRunner->assertEquals([
                'richMenuId' => 'richmenu-862e6ad6c267d2ddf3f42bc78554f6a4'
            ], $data);

            return [];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->updateRichMenuAlias('richmenu-alias-a', 'richmenu-862e6ad6c267d2ddf3f42bc78554f6a4');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
    }

    public function testGetRichMenuAlias()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/alias/richmenu-alias-a', $url);

            return [
                'richMenuAliasId' => 'richmenu-alias-a',
                'richMenuId' => 'richmenu-88c05ef6921ae53f8b58a25f3a65faf7'
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getRichMenuAlias('richmenu-alias-a');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('richmenu-alias-a', $data['richMenuAliasId']);
        $this->assertEquals('richmenu-88c05ef6921ae53f8b58a25f3a65faf7', $data['richMenuId']);
    }

    public function testGetRichMenuAliasList()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/alias/list', $url);

            return [
                'aliases' => [
                    [
                        'richMenuAliasId' => 'richmenu-alias-a',
                        'richMenuId' => 'richmenu-88c05ef6921ae53f8b58a25f3a65faf7'
                    ], [
                        'richMenuAliasId' => 'richmenu-alias-b',
                        'richMenuId' => 'richmenu-88c05ef6921ae53f8b58a25f3a65faf7'
                    ]
                ]
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getRichMenuAliasList('richmenu-alias-a');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(2, count($data['aliases']));
        $this->assertEquals('richmenu-alias-a', $data['aliases'][0]['richMenuAliasId']);
        $this->assertEquals('richmenu-88c05ef6921ae53f8b58a25f3a65faf7', $data['aliases'][0]['richMenuId']);
        $this->assertEquals('richmenu-alias-b', $data['aliases'][1]['richMenuAliasId']);
        $this->assertEquals('richmenu-88c05ef6921ae53f8b58a25f3a65faf7', $data['aliases'][1]['richMenuId']);
    }
}
