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
use LINE\LINEBot\Narrowcast\DemographicFilter\OperatorDemographicFilterBuilder;
use LINE\LINEBot\Narrowcast\DemographicFilter\GenderDemographicFilterBuilder;
use LINE\LINEBot\Narrowcast\DemographicFilter\AgeDemographicFilterBuilder;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class NarrowCastTest extends TestCase
{
    public function testSendNarrowcast()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/narrowcast', $url);

            $testRunner->assertEquals(MessageType::TEXT, $data['messages'][0]['type']);
            $testRunner->assertEquals('test text', $data['messages'][0]['text']);
            $testRunner->assertEquals('operator', $data['recipient']['type']);
            $testRunner->assertEquals([
                'type' => 'audience',
                'audienceGroupId' => 'test audience group id'
            ], $data['recipient']['and'][0]);
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
            $testRunner->assertEquals(100, $data['limit']['max']);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->sendNarrowcast(
            new TextMessageBuilder("test text"),
            OperatorRecipientBuilder::builder()
                ->setAnd([
                    AudienceRecipientBuilder::builder()
                        ->setAudienceGroupId('test audience group id')
                ]),
            OperatorDemographicFilterBuilder::builder()
                ->setAnd([
                    GenderDemographicFilterBuilder::builder()
                        ->setOneOf(['male', 'female']),
                    AgeDemographicFilterBuilder::builder()
                        ->setGte('age_20')
                        ->setLt('age_25')
                ]),
            100
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }
}
