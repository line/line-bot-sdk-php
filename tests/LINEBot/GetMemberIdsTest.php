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

class GetMemberIdsTest extends TestCase
{
    public function testGetGroupMemberIds()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/group/GROUP_ID/members/ids', $url);

            if (!$data) {
                return [
                    'memberIds' => ['Uxxxxxxxxxxxxxx1', 'Uxxxxxxxxxxxxxx2', 'Uxxxxxxxxxxxxxx3'],
                    'next' => 'testContinuationToken'
                ];
            } else {
                $testRunner->assertEquals(['start' => 'testContinuationToken'], $data);
                return ['memberIds' => ['Uxxxxxxxxxxxxxx4', 'Uxxxxxxxxxxxxxx5']];
            }
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        // First time call
        $res = $bot->getGroupMemberIds('GROUP_ID');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(['Uxxxxxxxxxxxxxx1', 'Uxxxxxxxxxxxxxx2', 'Uxxxxxxxxxxxxxx3'], $data['memberIds']);
        $this->assertEquals('testContinuationToken', $data['next']);

        // Second time call
        $res = $bot->getGroupMemberIds('GROUP_ID', 'testContinuationToken');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(['Uxxxxxxxxxxxxxx4', 'Uxxxxxxxxxxxxxx5'], $data['memberIds']);
        $this->assertFalse(array_key_exists('next', $data));

        // test getAllGroupMemberIds()
        $memberIds = $bot->getAllGroupMemberIds('GROUP_ID');
        $this->assertEquals(
            ['Uxxxxxxxxxxxxxx1', 'Uxxxxxxxxxxxxxx2', 'Uxxxxxxxxxxxxxx3', 'Uxxxxxxxxxxxxxx4', 'Uxxxxxxxxxxxxxx5'],
            $memberIds
        );
    }

    public function testGetRoomMemberIds()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/room/ROOM_ID/members/ids', $url);

            if (!$data) {
                return [
                    'memberIds' => ['Uxxxxxxxxxxxxxx1', 'Uxxxxxxxxxxxxxx2', 'Uxxxxxxxxxxxxxx3'],
                    'next' => 'testContinuationToken'
                ];
            } else {
                $testRunner->assertEquals(['start' => 'testContinuationToken'], $data);
                return ['memberIds' => ['Uxxxxxxxxxxxxxx4', 'Uxxxxxxxxxxxxxx5']];
            }
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        // First time call
        $res = $bot->getRoomMemberIds('ROOM_ID');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(['Uxxxxxxxxxxxxxx1', 'Uxxxxxxxxxxxxxx2', 'Uxxxxxxxxxxxxxx3'], $data['memberIds']);
        $this->assertEquals('testContinuationToken', $data['next']);

        // Second time call
        $res = $bot->getRoomMemberIds('ROOM_ID', 'testContinuationToken');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(['Uxxxxxxxxxxxxxx4', 'Uxxxxxxxxxxxxxx5'], $data['memberIds']);
        $this->assertFalse(array_key_exists('next', $data));

        // test getAllGroupMemberIds()
        $memberIds = $bot->getAllRoomMemberIds('ROOM_ID');
        $this->assertEquals(
            ['Uxxxxxxxxxxxxxx1', 'Uxxxxxxxxxxxxxx2', 'Uxxxxxxxxxxxxxx3', 'Uxxxxxxxxxxxxxx4', 'Uxxxxxxxxxxxxxx5'],
            $memberIds
        );
    }
}
