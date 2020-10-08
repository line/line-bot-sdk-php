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
use LINE\LINEBot\Constant\MessageType;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Narrowcast\Recipient\OperatorRecipientBuilder;
use LINE\LINEBot\Narrowcast\Recipient\AudienceRecipientBuilder;
use LINE\LINEBot\Narrowcast\Recipient\RedeliveryRecipientBuilder;
use LINE\LINEBot\Narrowcast\DemographicFilter\OperatorDemographicFilterBuilder;
use LINE\LINEBot\Narrowcast\DemographicFilter\GenderDemographicFilterBuilder;
use LINE\LINEBot\Narrowcast\DemographicFilter\AgeDemographicFilterBuilder;
use LINE\LINEBot\Narrowcast\DemographicFilter\AppTypeDemographicFilterBuilder;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class NarrowCastTest extends TestCase
{
    public function testSendNarrowcast1()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $headers) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/narrowcast', $url);

            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text', $data['messages'][0]['text']);
            $testRunner->assertEquals('operator', $data['recipient']['type']);
            $testRunner->assertEquals([
                'type' => 'audience',
                'audienceGroupId' => 11234567890
            ], $data['recipient']['and'][0]);
            $testRunner->assertEquals([
                'type' => 'operator',
                'not' => [
                    'type' => 'audience',
                    'audienceGroupId' => 21234567890
                ]
            ], $data['recipient']['and'][1]);
            $testRunner->assertEquals([
                'type' => 'redelivery',
                'requestId' => 'test request id 1'
            ], $data['recipient']['and'][2]);
            $testRunner->assertEquals([
                'type' => 'operator',
                'not' => [
                    'type' => 'redelivery',
                    'requestId' => 'test request id 2'
                ]
            ], $data['recipient']['and'][3]);
            $testRunner->assertEquals('operator', $data['filter']['demographic']['type']);
            $testRunner->assertEquals([
                'type' => 'gender',
                'oneOf' => ['male', 'female']
            ], $data['filter']['demographic']['and'][0]);
            $testRunner->assertEquals([
                'type' => 'age',
                'gte' => 'age_20',
                'lt' => 'age_25',
            ], $data['filter']['demographic']['and'][1]);
            $testRunner->assertEquals([
                'type' => 'operator',
                'not' => [
                    'type' => 'appType',
                    'oneOf' => ['ios', 'android']
                ]
            ], $data['filter']['demographic']['and'][2]);
            $testRunner->assertEquals(100, $data['limit']['max']);
            $testRunner->assertFalse($data['limit']['upToRemainingQuota']);
            $testRunner->assertTrue(\in_array('X-Line-Retry-Key: 123e4567-e89b-12d3-a456-426614174000', $headers));

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->sendNarrowcast(
            new TextMessageBuilder("test text"),
            OperatorRecipientBuilder::builder()
                ->setAnd([
                    AudienceRecipientBuilder::builder()
                        ->setAudienceGroupId(11234567890),
                    OperatorRecipientBuilder::builder()
                        ->setNot(
                            AudienceRecipientBuilder::builder()
                                ->setAudienceGroupId(21234567890)
                        ),
                    RedeliveryRecipientBuilder::builder()
                        ->setRequestId('test request id 1'),
                    OperatorRecipientBuilder::builder()
                        ->setNot(
                            RedeliveryRecipientBuilder::builder()
                                ->setRequestId('test request id 2')
                        )
                ]),
            OperatorDemographicFilterBuilder::builder()
                ->setAnd([
                    GenderDemographicFilterBuilder::builder()
                        ->setOneOf(['male', 'female']),
                    AgeDemographicFilterBuilder::builder()
                        ->setGte('age_20')
                        ->setLt('age_25'),
                    OperatorDemographicFilterBuilder::builder()
                        ->setNot(
                            AppTypeDemographicFilterBuilder::builder()
                                ->setOneOf(['ios', 'android'])
                        )
                ]),
            100,
            '123e4567-e89b-12d3-a456-426614174000'
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testSendNarrowcast2()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $headers) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/narrowcast', $url);

            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text', $data['messages'][0]['text']);
            $testRunner->assertEquals('operator', $data['recipient']['type']);
            $testRunner->assertEquals([
                'type' => 'audience',
                'audienceGroupId' => 11234567890
            ], $data['recipient']['and'][0]);
            $testRunner->assertEquals([
                'type' => 'operator',
                'not' => [
                    'type' => 'audience',
                    'audienceGroupId' => 21234567890
                ]
            ], $data['recipient']['and'][1]);
            $testRunner->assertEquals('operator', $data['filter']['demographic']['type']);
            $testRunner->assertEquals([
                'type' => 'gender',
                'oneOf' => ['male', 'female']
            ], $data['filter']['demographic']['and'][0]);
            $testRunner->assertEquals([
                'type' => 'age',
                'gte' => 'age_20',
                'lt' => 'age_25',
            ], $data['filter']['demographic']['and'][1]);
            $testRunner->assertEquals([
                'type' => 'operator',
                'not' => [
                    'type' => 'appType',
                    'oneOf' => ['ios', 'android']
                ]
            ], $data['filter']['demographic']['and'][2]);
            $testRunner->assertArrayNotHasKey('max', $data['limit']);
            $testRunner->assertTrue($data['limit']['upToRemainingQuota']);
            $testRunner->assertTrue(\in_array('X-Line-Retry-Key: 123e4567-e89b-12d3-a456-426614174000', $headers));

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->sendNarrowcast(
            new TextMessageBuilder("test text"),
            OperatorRecipientBuilder::builder()
                ->setAnd([
                    AudienceRecipientBuilder::builder()
                        ->setAudienceGroupId(11234567890),
                    OperatorRecipientBuilder::builder()
                        ->setNot(
                            AudienceRecipientBuilder::builder()
                                ->setAudienceGroupId(21234567890)
                        )
                ]),
            OperatorDemographicFilterBuilder::builder()
                ->setAnd([
                    GenderDemographicFilterBuilder::builder()
                        ->setOneOf(['male', 'female']),
                    AgeDemographicFilterBuilder::builder()
                        ->setGte('age_20')
                        ->setLt('age_25'),
                    OperatorDemographicFilterBuilder::builder()
                        ->setNot(
                            AppTypeDemographicFilterBuilder::builder()
                                ->setOneOf(['ios', 'android'])
                        )
                ]),
            null,
            '123e4567-e89b-12d3-a456-426614174000',
            true
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testGetNarrowcastProgress1()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/progress/narrowcast', $url);

            $testRunner->assertEquals('test request id', $data['requestId']);

            return [
                'status' => 200,
                'phase' => 'waiting',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNarrowcastProgress('test request id');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(200, $data['status']);
        $this->assertEquals('waiting', $data['phase']);
        $this->assertArrayNotHasKey('successCount', $data);
        $this->assertArrayNotHasKey('failureCount', $data);
        $this->assertArrayNotHasKey('targetCount', $data);
        $this->assertArrayNotHasKey('failedDescription', $data);
        $this->assertArrayNotHasKey('errorCode', $data);
    }

    public function testGetNarrowcastProgress2()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/progress/narrowcast', $url);

            $testRunner->assertEquals('test request id', $data['requestId']);

            return [
                'status' => 200,
                'phase' => 'failed',
                'successCount' => 1,
                'failureCount' => 2,
                'targetCount' => 10,
                'failedDescription' => 'unknown',
                'errorCode' => 1
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getNarrowcastProgress('test request id');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(200, $data['status']);
        $this->assertEquals('failed', $data['phase']);
        $this->assertEquals(1, $data['successCount']);
        $this->assertEquals(2, $data['failureCount']);
        $this->assertEquals(10, $data['targetCount']);
        $this->assertEquals('unknown', $data['failedDescription']);
        $this->assertEquals(1, $data['errorCode']);
    }
}
