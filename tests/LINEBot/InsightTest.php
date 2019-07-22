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

class InsightTest extends TestCase
{
    public function testGetNumberOfMessageDeliveries()
    {
        $date = new DateTime();

        // Test: status is ready
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($date) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/message/delivery', $url);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $testRunner->assertEquals([
                'date' => $date->format('Ymd')
            ], $data);

            return [
                'status' => 'ready',
                'broadcast' => 5385,
                'targeting' => 522,
                'autoResponse' => 1200,
                'welcomeResponse' => 1201,
                'chat' => 1202,
                'apiBroadcast' => 1203,
                'apiPush' => 1204,
                'apiMulticast' => 1205,
                'apiReply' => 1206
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNumberOfMessageDeliveries($date);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ready', $data['status']);
        $this->assertEquals(5385, $data['broadcast']);
        $this->assertEquals(522, $data['targeting']);
        $this->assertEquals(1200, $data['autoResponse']);
        $this->assertEquals(1201, $data['welcomeResponse']);
        $this->assertEquals(1202, $data['chat']);
        $this->assertEquals(1203, $data['apiBroadcast']);
        $this->assertEquals(1204, $data['apiPush']);
        $this->assertEquals(1205, $data['apiMulticast']);
        $this->assertEquals(1206, $data['apiReply']);

        // Test: status is unready
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($date) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/message/delivery', $url);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $testRunner->assertEquals([
                'date' => $date->format('Ymd')
            ], $data);

            return [
                'status' => 'unready',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNumberOfMessageDeliveries($date);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('unready', $data['status']);

        // Test: status is out_of_service
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($date) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/message/delivery', $url);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $testRunner->assertEquals([
                'date' => $date->format('Ymd')
            ], $data);

            return [
                'status' => 'out_of_service',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNumberOfMessageDeliveries($date);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('out_of_service', $data['status']);
    }
    
    public function testGetNumberOfFollowers()
    {
        $date = new DateTime();

        // Test: status is ready
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($date) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/followers', $url);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $testRunner->assertEquals([
                'date' => $date->format('Ymd')
            ], $data);

            return [
                'status' => 'ready',
                'followers' => 7620,
                'targetedReaches' => 5848,
                'blocks' => 237
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNumberOfFollowers($date);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ready', $data['status']);
        $this->assertEquals(7620, $data['followers']);
        $this->assertEquals(5848, $data['targetedReaches']);
        $this->assertEquals(237, $data['blocks']);

        // Test: status is unready
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($date) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/followers', $url);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $testRunner->assertEquals([
                'date' => $date->format('Ymd')
            ], $data);

            return [
                'status' => 'unready',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNumberOfFollowers($date);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('unready', $data['status']);

        // Test: status is out_of_service
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($date) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/followers', $url);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $testRunner->assertEquals([
                'date' => $date->format('Ymd')
            ], $data);

            return [
                'status' => 'out_of_service',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNumberOfFollowers($date);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('out_of_service', $data['status']);
    }
    
    public function testGetFriendDemographics()
    {
        // Test: available = true
        $responseJson = [
            'available' => true,
            'genders' => [
                [
                    'gender' => 'unknown',
                    'percentage' => 37.6
                ], [
                    'gender' => 'male',
                    'percentage' => 31.8
                ], [
                    'gender' => 'female',
                    'percentage' => 30.6
                ]
            ],
            'ages' => [
                [
                    'age' => 'unknown',
                    'percentage' => 37.6
                ], [
                    'age' => 'from50',
                    'percentage' => 17.3
                ],
            ],
            'areas' => [
                [
                    'area' => 'unknown',
                    'percentage' => 42.9
                ],
                [
                    'area' => '徳島',
                    'percentage' => 2.9
                ],
            ],
            'appTypes' => [
                [
                    'appType' => 'ios',
                    'percentage' => 62.4
                ],
                [
                    'appType' => 'android',
                    'percentage' => 27.7
                ],
                [
                    'appType' => 'others',
                    'percentage' => 9.9
                ]
            ],
            'subscriptionPeriods' => [
                [
                    'subscriptionPeriod' => 'over365days',
                    'percentage' => 96.4
                ],
                [
                    'subscriptionPeriod' => 'within365days',
                    'percentage' => 1.9
                ],
                [
                    'subscriptionPeriod' => 'within180days',
                    'percentage' => 1.2
                ],
                [
                    'subscriptionPeriod' => 'within90days',
                    'percentage' => 0.5
                ],
                [
                    'subscriptionPeriod' => 'within30days',
                    'percentage' => 0.1
                ],
                [
                    'subscriptionPeriod' => 'within7days',
                    'percentage' => 0
                ]
            ]
        ];
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($responseJson) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/demographic', $url);

            return $responseJson;
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getFriendDemographics();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals($responseJson, $data);

        // Test: available = false
        $responseJson = [
            'available' => false,
            'genders' => [],
            'ages' => [],
            'areas' => [],
            'appTypes' => [],
            'subscriptionPeriods' => []
        ];
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($responseJson) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/demographic', $url);

            return $responseJson;
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getFriendDemographics();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals($responseJson, $data);
    }
}
