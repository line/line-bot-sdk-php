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
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class GetMemberProfileTest extends TestCase
{
    public function testGetGroupMemberIds()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/group/GROUP_ID/member/MEMBER_ID', $url);
            return [
                'displayName' => 'LINE taro',
                'userId' => 'Uxxxxxxxxxxxxxx1',
                'pictureUrl' => 'https://example.com/profile.png',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getGroupMemberProfile('GROUP_ID', 'MEMBER_ID');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('LINE taro', $data['displayName']);
        $this->assertEquals('Uxxxxxxxxxxxxxx1', $data['userId']);
        $this->assertEquals('https://example.com/profile.png', $data['pictureUrl']);
    }

    public function testGetRoomMemberIds()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/room/ROOM_ID/member/MEMBER_ID', $url);
            return [
                'displayName' => 'LINE taro',
                'userId' => 'Uxxxxxxxxxxxxxx1',
                'pictureUrl' => 'https://example.com/profile.png',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getRoomMemberProfile('ROOM_ID', 'MEMBER_ID');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('LINE taro', $data['displayName']);
        $this->assertEquals('Uxxxxxxxxxxxxxx1', $data['userId']);
        $this->assertEquals('https://example.com/profile.png', $data['pictureUrl']);
    }
}
