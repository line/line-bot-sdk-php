<?php

/**
 * Copyright 2017 LINE Corporation
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
use LINE\LINEBot\RichMenuBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuSizeBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBoundsBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class RichMenuTest extends TestCase
{
    public function testGetRichMenu()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/123', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getRichMenu(123);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testCreateRichMenu()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu', $url);

            $testRunner->assertEquals(2500, $data['size']['width']);
            $testRunner->assertEquals(1686, $data['size']['height']);
            $testRunner->assertEquals(true, $data['selected']);
            $testRunner->assertEquals('Nice richmenu', $data['name']);
            $testRunner->assertEquals('Tap to open', $data['chatBarText']);

            $areas = $data['areas'];
            $testRunner->assertEquals(2, count($areas));

            $testRunner->assertEquals(0, $areas[0]['bounds']['x']);
            $testRunner->assertEquals(10, $areas[0]['bounds']['y']);
            $testRunner->assertEquals(1250, $areas[0]['bounds']['width']);
            $testRunner->assertEquals(1676, $areas[0]['bounds']['height']);
            $testRunner->assertEquals(ActionType::MESSAGE, $areas[0]['action']['type']);
            $testRunner->assertEquals('test message', $areas[0]['action']['text']);

            $testRunner->assertEquals(1250, $areas[1]['bounds']['x']);
            $testRunner->assertEquals(0, $areas[1]['bounds']['y']);
            $testRunner->assertEquals(1240, $areas[1]['bounds']['width']);
            $testRunner->assertEquals(1686, $areas[1]['bounds']['height']);
            $testRunner->assertEquals(ActionType::MESSAGE, $areas[1]['action']['type']);
            $testRunner->assertEquals('test message 2', $areas[1]['action']['text']);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createRichMenu(
            new RichMenuBuilder(
                RichMenuSizeBuilder::getFull(),
                true,
                'Nice richmenu',
                'Tap to open',
                [
                    new RichMenuAreaBuilder(
                        new RichMenuAreaBoundsBuilder(0, 10, 1250, 1676),
                        new MessageTemplateActionBuilder('message label', 'test message')
                    ),
                    new RichMenuAreaBuilder(
                        new RichMenuAreaBoundsBuilder(1250, 0, 1240, 1686),
                        new MessageTemplateActionBuilder('message label 2', 'test message 2')
                    )
                ]
            )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testDeleteRichMenu()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('DELETE', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/123', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->deleteRichMenu(123);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testGetRichMenuId()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/user/123/richmenu', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getRichMenuId(123);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testSetDefaultRichMenuId()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/user/all/richmenu/123', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->setDefaultRichMenuId(123);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testGetDefaultRichMenuId()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/user/all/richmenu', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getDefaultRichMenuId();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testCancelDefaultRichMenuId()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('DELETE', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/user/all/richmenu', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->cancelDefaultRichMenuId();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testLinkRichMenu()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/user/123/richmenu/567', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->linkRichMenu(123, 567);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testBulkLinkRichMenu()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/bulk/link', $url);
            $testRunner->assertEquals([
                'userIds' => ['123'],
                'richMenuId' => '567',
            ], $data);
            return ['status' => 202];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->bulkLinkRichMenu([123], 567);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(202, $res->getJSONDecodedBody()['status']);
    }

    public function testUnlinkRichMenu()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('DELETE', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/user/123/richmenu', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->unlinkRichMenu(123);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testBulkUnlinkRichMenu()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/bulk/unlink', $url);
            $testRunner->assertEquals([
                'userIds' => ['123']
            ], $data);
            return ['status' => 202];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->bulkUnlinkRichMenu([123]);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(202, $res->getJSONDecodedBody()['status']);
    }

    public function testDownloadRichMenuImage()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api-data.line.me/v2/bot/richmenu/123/content', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->downloadRichMenuImage(123);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testUploadRichMenuImage()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $headers) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api-data.line.me/v2/bot/richmenu/123/content', $url);

            $testRunner->assertEquals(1, count($headers));
            $testRunner->assertEquals('Content-Type: image/png', $headers[0]);

            $testRunner->assertEquals('/path/to/image.png', $data['__file']);
            $testRunner->assertEquals('image/png', $data['__type']);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->uploadRichMenuImage(123, '/path/to/image.png', 'image/png');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testGetRichMenuList()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/list', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getRichMenuList();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }
}
