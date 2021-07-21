<?php

/**
 * Copyright 2021 LINE Corporation
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

class AggregationUnitTest extends TestCase
{
    public function testGetStaticsPerUnit()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/message/event/aggregation', $url);
            $testRunner->assertEquals([
                'customAggregationUnit' => 'promotion_a',
                'from' => '20210301',
                'to' => '20210302'
            ], $data);
            return [
                'overview' => [
                    'uniqueImpression' => 40,
                    'uniqueClick' => 30,
                    'uniqueMediaPlayed' => 25,
                    'uniqueMediaPlayed100Percent' => null
                ],
                'messages' => [
                    [
                        'seq' => 1,
                        'impression' => 42,
                        'mediaPlayed' => 39,
                        'mediaPlayed25Percent' => null,
                        'mediaPlayed50Percent' => null,
                        'mediaPlayed75Percent' => null,
                        'mediaPlayed100Percent' => null,
                        'uniqueMediaPlayed' => 25,
                        'uniqueMediaPlayed25Percent' => null,
                        'uniqueMediaPlayed50Percent' => null,
                        'uniqueMediaPlayed75Percent' => null,
                        'uniqueMediaPlayed100Percent' => null
                    ],
                ],
                'clicks' => [
                    [
                        'seq' => 1,
                        'url' => 'https://developers.line.biz/',
                        'click' => 35,
                        'uniqueClick' => 25,
                        'uniqueClickOfRequest' => null
                    ],
                    [
                        'seq' => 1,
                        'url' => 'https://www.line-community.me/',
                        'click' => 29,
                        'uniqueClick' => null,
                        'uniqueClickOfRequest' => null
                    ],
                ]
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getUserInteractionStatisticsPerUnit(
            'promotion_a',
            '20210301',
            '20210302'
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(40, $data['overview']['uniqueImpression']);
        $this->assertEquals(30, $data['overview']['uniqueClick']);
        $this->assertEquals(25, $data['overview']['uniqueMediaPlayed']);
        $this->assertEquals(null, $data['overview']['uniqueMediaPlayed100Percent']);
        $this->assertEquals(1, count($data['messages']));
        $this->assertEquals(1, $data['messages'][0]['seq']);
        $this->assertEquals(42, $data['messages'][0]['impression']);
        $this->assertEquals(39, $data['messages'][0]['mediaPlayed']);
        $this->assertEquals(null, $data['messages'][0]['mediaPlayed25Percent']);
        $this->assertEquals(null, $data['messages'][0]['mediaPlayed50Percent']);
        $this->assertEquals(null, $data['messages'][0]['mediaPlayed75Percent']);
        $this->assertEquals(null, $data['messages'][0]['mediaPlayed100Percent']);
        $this->assertEquals(25, $data['messages'][0]['uniqueMediaPlayed']);
        $this->assertEquals(null, $data['messages'][0]['uniqueMediaPlayed25Percent']);
        $this->assertEquals(null, $data['messages'][0]['uniqueMediaPlayed50Percent']);
        $this->assertEquals(null, $data['messages'][0]['uniqueMediaPlayed75Percent']);
        $this->assertEquals(null, $data['messages'][0]['uniqueMediaPlayed100Percent']);
        $this->assertEquals(2, count($data['clicks']));
        $this->assertEquals(1, $data['clicks'][0]['seq']);
        $this->assertEquals('https://developers.line.biz/', $data['clicks'][0]['url']);
        $this->assertEquals(35, $data['clicks'][0]['click']);
        $this->assertEquals(25, $data['clicks'][0]['uniqueClick']);
        $this->assertEquals(null, $data['clicks'][0]['uniqueClickOfRequest']);
        $this->assertEquals(1, $data['clicks'][1]['seq']);
        $this->assertEquals('https://www.line-community.me/', $data['clicks'][1]['url']);
        $this->assertEquals(29, $data['clicks'][1]['click']);
        $this->assertEquals(null, $data['clicks'][1]['uniqueClick']);
        $this->assertEquals(null, $data['clicks'][1]['uniqueClickOfRequest']);
    }

    public function testGetNumberOfUnitsUsedThisMonth()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/aggregation/info', $url);

            return ['numOfCustomAggregationUnits' => 22];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getNumberOfUnitsUsedThisMonth();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(22, $data['numOfCustomAggregationUnits']);
    }

    public function testGetNameListOfUnitsUsedThisMonth()
    {
        // Test: no params
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/message/event/aggregation', $url);
            $testRunner->assertEmpty($data);

            return [
                'customAggregationUnits' => ['promotion_a', 'promotion_b'],
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getNameListOfUnitsUsedThisMonth();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(['promotion_a', 'promotion_b'], $data['customAggregationUnits']);
        $this->assertFalse(isset($data['next']));

        // Test: with params
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/message/event/aggregation', $url);
            $testRunner->assertEquals([
                'limit' => 30,
                'start' => 'jxEWCEEP...'
            ], $data);

            return [
                'customAggregationUnits' => ['promotion_a', 'promotion_b'],
                'next' => 'jxEWCEEP2...',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);

        $res = $bot->getNameListOfUnitsUsedThisMonth(30, 'jxEWCEEP...');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(['promotion_a', 'promotion_b'], $data['customAggregationUnits']);
        $this->assertEquals('jxEWCEEP2...', $data['next']);

        // test getAllNameListOfUnitsUsedThisMonth()
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/insight/message/event/aggregation', $url);

            if (isset($data['start'])) {
                $testRunner->assertEquals(['start' => 'jxEWCEEP...'], $data);
                return [
                    'customAggregationUnits' => ['promotion_c', 'promotion_d']
                ];
            } else {
                return [
                    'customAggregationUnits' => ['promotion_a', 'promotion_b'],
                    'next' => 'jxEWCEEP...',
                ];
            }
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $nameList = $bot->getAllNameListOfUnitsUsedThisMonth();
        $this->assertEquals(
            ['promotion_a', 'promotion_b', 'promotion_c', 'promotion_d'],
            $nameList
        );
    }
}
