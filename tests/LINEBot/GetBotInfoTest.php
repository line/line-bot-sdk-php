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

class GetBotInfoTest extends TestCase
{
    public function testGetBotInfo()
    {
        $mock = function ($testRunner, $httpMethod, $url) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/info', $url);

            return [
                'userId' => 'Ub9952f8...',
                'basicId' => '@216ru..',
                'displayName' => 'Example name',
                'pictureUrl' => 'https://obs.line-apps.com/...',
                'chatMode' => 'chat',
                'markAsReadMode' => 'manual',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getBotInfo();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('Ub9952f8...', $data['userId']);
        $this->assertEquals('@216ru..', $data['basicId']);
        $this->assertEquals('Example name', $data['displayName']);
        $this->assertEquals('https://obs.line-apps.com/...', $data['pictureUrl']);
        $this->assertEquals('chat', $data['chatMode']);
        $this->assertEquals('manual', $data['markAsReadMode']);
    }
}
