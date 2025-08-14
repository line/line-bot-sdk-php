<?php
/**
 * Copyright 2024 LINE Corporation
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

namespace LINE\Tests\Clients\MessagingApi\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\CouponCreateRequest;
use LINE\Clients\MessagingApi\Model\CouponCreateResponse;
use LINE\Clients\MessagingApi\Model\CouponDiscountRewardRequest;
use LINE\Clients\MessagingApi\Model\CouponResponse;
use LINE\Clients\MessagingApi\Model\DiscountFixedPriceInfoRequest;
use LINE\Clients\MessagingApi\Model\LotteryAcquisitionConditionRequest;
use LINE\Clients\MessagingApi\Model\MessagingApiPagerCouponListResponse;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class MessagingApiApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function assertQueryEquals(array $expected, UriInterface $uri): void
    {
        $actual = Query::parse($uri->getQuery());

        $normalize = function (array &$arr): void {
            foreach ($arr as &$v) {
                if (is_array($v)) {
                    sort($v);
                }
            }
            ksort($arr);
        };

        $normalize($expected);
        $normalize($actual);

        $this->assertSame($expected, $actual, 'Query parameters mismatch');
    }

    public function testGetFollowers(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $this->assertEquals('https://api.line.me/v2/bot/followers/ids?limit=99', (string)$request->getUri());
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['userIds' => ["Uaaaaaaaa...", "Ubbbbbbbb...", "Ucccccccc..."], 'next' => "yANU9IA.."]),
            ));
        $api = new MessagingApiApi($client);
        $followers = $api->getFollowers(limit: 99);
        $this->assertEquals(["Uaaaaaaaa...", "Ubbbbbbbb...", "Ucccccccc..."], $followers->getUserIds());
        $this->assertEquals("yANU9IA..", $followers->getNext());
    }

    public function testCreateCoupon(): void
    {
        $startTimestamp = time();
        $endTimestamp = $startTimestamp + 3600;

        $expectedRequestBody = <<<JSON
    {
        "acquisitionCondition": {
            "type": "lottery",
            "lotteryProbability": 50,
            "maxAcquireCount": 100
        },
        "barcodeImageUrl": "https://example.com/barcode.png",
        "couponCode": "UNIQUECODE123",
        "description": "Test coupon description",
        "endTimestamp": $endTimestamp,
        "imageUrl": "https://example.com/image.png",
        "maxUseCountPerTicket": 1,
        "startTimestamp": $startTimestamp,
        "title": "Test Coupon",
        "usageCondition": "Valid at all stores",
        "reward": {
            "type": "discount",
            "priceInfo": {
                "type": "fixed",
                "fixedAmount": 100
            }
        },
        "visibility": "PUBLIC",
        "timezone": "ASIA_TOKYO"
    }
JSON;

        $couponCreateRequest = new CouponCreateRequest([
            'acquisitionCondition' => new LotteryAcquisitionConditionRequest([
                'type' => 'lottery',
                'lotteryProbability' => 50,
                'maxAcquireCount' => 100
            ]),
            'barcodeImageUrl' => 'https://example.com/barcode.png',
            'couponCode' => 'UNIQUECODE123',
            'description' => 'Test coupon description',
            'endTimestamp' => $endTimestamp,
            'imageUrl' => 'https://example.com/image.png',
            'maxUseCountPerTicket' => 1,
            'startTimestamp' => $startTimestamp,
            'title' => 'Test Coupon',
            'usageCondition' => 'Valid at all stores',
            'reward' => new CouponDiscountRewardRequest([
                'type' => 'discount',
                'priceInfo' => new DiscountFixedPriceInfoRequest([
                    'type' => 'fixed',
                    'fixedAmount' => 100
                ])
            ]),
            'visibility' => 'PUBLIC',
            'timezone' => 'ASIA_TOKYO'
        ]);

        $contentType = 'application/json';
        $expectedResponseBody = [
            'couponId' => 'testCouponId'
        ];

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
               ->with(
                   Mockery::on(function (Request $request) use ($expectedRequestBody, $contentType) {
                       $this->assertEquals('POST', $request->getMethod());
                       $this->assertEquals('https://api.line.me/v2/bot/coupon', (string)$request->getUri());
                       $this->assertEquals($contentType, $request->getHeaderLine('Content-Type'));
                       $this->assertJsonStringEqualsJsonString(
                           $expectedRequestBody,
                           (string)$request->getBody()
                       );
                       return true;
                   }),
                   []
               )
               ->once()
               ->andReturn(new Response(
                   status: 200,
                   headers: [],
                   body: json_encode($expectedResponseBody)
               ));

        $api = new MessagingApiApi($client);
        $response = $api->createCoupon($couponCreateRequest, $contentType);

        $this->assertInstanceOf(CouponCreateResponse::class, $response);
        $this->assertEquals('testCouponId', $response->getCouponId());
    }

    public function testCloseCoupon(): void
    {
        $couponId = 'testCouponId';
        $contentType = 'application/json';

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
               ->with(
                   Mockery::on(function (Request $request) use ($couponId, $contentType) {
                       $this->assertEquals('PUT', $request->getMethod());
                       $this->assertEquals("https://api.line.me/v2/bot/coupon/{$couponId}/close", (string)$request->getUri());
                       $this->assertEquals($contentType, $request->getHeaderLine('Content-Type'));
                       return true;
                   }),
                   []
               )
               ->once()
               ->andReturn(new Response(
                   status: 200,
                   headers: [],
                   body: null
               ));

        $api = new MessagingApiApi($client);
        $api->closeCoupon($couponId, $contentType);
    }

    public function testGetCouponDetail(): void
    {
        $couponId = 'testCouponId';
        $contentType = 'application/json';
        $expectedResponseBody = <<<JSON
        {
            "acquisitionCondition": {
                "type": "normal"
            },
            "barcodeImageUrl": "https://example.com/barcode.png",
            "couponCode": "UNIQUECODE123",
            "description": "Test coupon description",
            "endTimestamp": 1700000000,
            "imageUrl": "https://example.com/image.png",
            "maxAcquireCount": 100,
            "maxUseCountPerTicket": 1,
            "maxTicketPerUser": 1,
            "startTimestamp": 1699996400,
            "title": "Test Coupon",
            "usageCondition": "Valid at all stores",
            "reward": {
                "type": "cashback",
                "priceInfo": {
                    "type": "percentage",
                    "percentage": 10
                }
            },
            "visibility": "PUBLIC",
            "timezone": "ASIA_TOKYO",
            "couponId": "$couponId",
            "createdTimestamp": 1699990000,
            "status": "RUNNING"
        }
        JSON;

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) use ($couponId, $contentType) {
                    $this->assertEquals('GET', $request->getMethod());
                    $this->assertEquals("https://api.line.me/v2/bot/coupon/{$couponId}", (string)$request->getUri());
                    $this->assertEquals($contentType, $request->getHeaderLine('Content-Type'));
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: $expectedResponseBody
            ));

        $api = new MessagingApiApi($client);
        $response = $api->getCouponDetail($couponId, $contentType);

        $this->assertInstanceOf(CouponResponse::class, $response);
        $this->assertEquals('normal', $response->getAcquisitionCondition()->getType());
        $this->assertEquals('https://example.com/barcode.png', $response->getBarcodeImageUrl());
        $this->assertEquals('UNIQUECODE123', $response->getCouponCode());
        $this->assertEquals('Test coupon description', $response->getDescription());
        $this->assertEquals(1700000000, $response->getEndTimestamp());
        $this->assertEquals('https://example.com/image.png', $response->getImageUrl());
        $this->assertEquals(100, $response->getMaxAcquireCount());
        $this->assertEquals(1, $response->getMaxUseCountPerTicket());
        $this->assertEquals(1, $response->getMaxTicketPerUser());
        $this->assertEquals(1699996400, $response->getStartTimestamp());
        $this->assertEquals('Test Coupon', $response->getTitle());
        $this->assertEquals('Valid at all stores', $response->getUsageCondition());
        $this->assertEquals('cashback', $response->getReward()->getType());
        $this->assertEquals('PUBLIC', $response->getVisibility());
        $this->assertEquals('ASIA_TOKYO', $response->getTimezone());
        $this->assertEquals($couponId, $response->getCouponId());
        $this->assertEquals(1699990000, $response->getCreatedTimestamp());
        $this->assertEquals('RUNNING', $response->getStatus());

        // TODO: This test should be enabled after we support automatic polymorphism parsing outside of webhook.
        // Right now, polymorphism is only handled automatically in webhook, so this code is commented out for now.
        // $reward = $response->getReward();
        // if ($reward instanceof CouponCashBackRewardResponse) {
        //     $priceInfo = $reward->getPriceInfo();
        //     $this->assertEquals('percentage', $priceInfo->getType());
        //     $this->assertEquals(10, $priceInfo->getPercentage());
        // } else {
        //     $this->fail('Reward is not of type CouponCashBackRewardResponse');
        // }
    }

    public function testListCoupon(): void
    {
        $status = ['RUNNING', 'CLOSED'];
        $start = 'startToken';
        $limit = 10;
        $contentType = 'application/json';
        $expectedQuery = [
            'status' => $status,
            'start'  => $start,
            'limit'  => (string)$limit,
        ];
        $expectedRequestBody = <<<JSON
        {
            "status": ["RUNNING", "CLOSED"],
            "start": "startToken",
            "limit": 10
        }
        JSON;
        $expectedResponseBody = <<<JSON
        {
            "items": [
                {"couponId": "coupon1", "title": "Coupon 1"},
                {"couponId": "coupon2", "title": "Coupon 2"}
            ],
            "next": "nextToken"
        }
        JSON;

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) use ($status, $start, $limit, $contentType, $expectedQuery) {
                    $this->assertEquals('GET', $request->getMethod());
                    $this->assertStringContainsString('https://api.line.me/v2/bot/coupon?', (string)$request->getUri());
                    $this->assertEquals($contentType, $request->getHeaderLine('Content-Type'));
                    $this->assertQueryEquals($expectedQuery, $request->getUri());
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                200,
                [],
                json_encode($expectedResponseBody)
            ));

        $api = new MessagingApiApi($client);
        $response = $api->listCoupon($status, $start, $limit, $contentType);

        $this->assertInstanceOf(MessagingApiPagerCouponListResponse::class, $response);
        $this->assertCount(2, $response->getItems());
        $this->assertEquals('coupon1', $response->getItems()[0]['couponId']);
        $this->assertEquals('Coupon 1', $response->getItems()[0]['title']);
        $this->assertEquals('coupon2', $response->getItems()[1]['couponId']);
        $this->assertEquals('Coupon 2', $response->getItems()[1]['title']);
        $this->assertEquals('nextToken', $response->getNext());
    }
}
