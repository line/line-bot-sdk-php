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
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class GetMemberCountTest extends TestCase
{
    public function testGetGroupMembersCount()
    {
        $mock = function ($testRunner, $httpMethod, $url) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/group/GROUP_ID/members/count', $url);

            return [
                'count' => 3,
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getGroupMembersCount('GROUP_ID');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(3, $data['count']);
    }

    public function testGetRoomMembersCount()
    {
        $mock = function ($testRunner, $httpMethod, $url) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/room/ROOM_ID/members/count', $url);

            return [
                'count' => 3,
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getRoomMembersCount('ROOM_ID');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(3, $data['count']);
    }
}
