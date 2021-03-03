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

class GetFollowerIdsTest extends TestCase
{
    public function testGetFollowerIds()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/followers/ids', $url);

            if (!$data) {
                return [
                    'userIds' => ['Uxxxxxxxxxxxxxx1', 'Uxxxxxxxxxxxxxx2', 'Uxxxxxxxxxxxxxx3'],
                    'next' => 'testContinuationToken'
                ];
            } else {
                $testRunner->assertEquals(['start' => 'testContinuationToken'], $data);
                return ['userIds' => ['Uxxxxxxxxxxxxxx4', 'Uxxxxxxxxxxxxxx5']];
            }
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        // First time call
        $res = $bot->getFollowerIds();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(['Uxxxxxxxxxxxxxx1', 'Uxxxxxxxxxxxxxx2', 'Uxxxxxxxxxxxxxx3'], $data['userIds']);
        $this->assertEquals('testContinuationToken', $data['next']);

        // Second time call
        $res = $bot->getFollowerIds('testContinuationToken');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(['Uxxxxxxxxxxxxxx4', 'Uxxxxxxxxxxxxxx5'], $data['userIds']);
        $this->assertFalse(isset($data['next']));

        // test getAllFollowerIds()
        $userIds = $bot->getAllFollowerIds();
        $this->assertEquals(
            ['Uxxxxxxxxxxxxxx1', 'Uxxxxxxxxxxxxxx2', 'Uxxxxxxxxxxxxxx3', 'Uxxxxxxxxxxxxxx4', 'Uxxxxxxxxxxxxxx5'],
            $userIds
        );
    }
}
