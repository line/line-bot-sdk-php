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
use LINE\Clients\MessagingApi\Model\AcquisitionConditionResponse;
use LINE\Clients\MessagingApi\Model\CouponCashBackRewardResponse;
use LINE\Clients\MessagingApi\Model\CouponCreateRequest;
use LINE\Clients\MessagingApi\Model\CouponCreateResponse;
use LINE\Clients\MessagingApi\Model\CouponDiscountRewardRequest;
use LINE\Clients\MessagingApi\Model\CouponListResponse;
use LINE\Clients\MessagingApi\Model\CouponResponse;
use LINE\Clients\MessagingApi\Model\DiscountFixedPriceInfoRequest;
use LINE\Clients\MessagingApi\Model\GetFollowersResponse;
use LINE\Clients\MessagingApi\Model\LotteryAcquisitionConditionRequest;
use LINE\Clients\MessagingApi\Model\MessagingApiPagerCouponListResponse;
use LINE\Clients\MessagingApi\Model\NormalAcquisitionConditionResponse;
use LINE\Clients\MessagingApi\Model\PostbackAction;
use LINE\Clients\MessagingApi\Model\RichMenuArea;
use LINE\Clients\MessagingApi\Model\RichMenuListResponse;
use LINE\Clients\MessagingApi\Model\RichMenuResponse;
use LINE\Clients\MessagingApi\Model\RichMenuSize;
use LINE\Clients\MessagingApi\Model\URIAction;
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
        $this->assertInstanceOf(GetFollowersResponse::class, $followers);
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
                "type": "cashBack",
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
        $this->assertInstanceOf(NormalAcquisitionConditionResponse::class, $response->getAcquisitionCondition());
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
        $this->assertEquals('cashBack', $response->getReward()->getType());
        $this->assertEquals('PUBLIC', $response->getVisibility());
        $this->assertEquals('ASIA_TOKYO', $response->getTimezone());
        $this->assertEquals($couponId, $response->getCouponId());
        $this->assertEquals(1699990000, $response->getCreatedTimestamp());
        $this->assertEquals('RUNNING', $response->getStatus());

        $reward = $response->getReward();
        $this->assertInstanceOf(CouponCashBackRewardResponse::class, $reward);
        $priceInfo = $reward->getPriceInfo();
        $this->assertEquals('percentage', $priceInfo->getType());
        $this->assertEquals(10, $priceInfo->getPercentage());
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
        $coupon1 = $response->getItems()[0];
        $this->assertInstanceOf(CouponListResponse::class, $coupon1);
        $this->assertEquals('coupon1', $coupon1['couponId']);
        $this->assertEquals('Coupon 1', $coupon1['title']);
        $coupon2 = $response->getItems()[1];
        $this->assertInstanceOf(CouponListResponse::class, $coupon2);
        $this->assertEquals('coupon2', $coupon2['couponId']);
        $this->assertEquals('Coupon 2', $coupon2['title']);
        $this->assertEquals('nextToken', $response->getNext());
    }

    public function testGetRichMenuList(): void
    {
        $expectedResponseBody = <<<JSON
{
  "richmenus": [
    {
      "richMenuId": "{richMenuId}",
      "name": "Nice rich menu",
      "size": {
        "width": 2500,
        "height": 1686
      },
      "chatBarText": "Tap to open",
      "selected": false,
      "areas": [
        {
          "bounds": {
            "x": 0,
            "y": 0,
            "width": 2500,
            "height": 1686
          },
          "action": {
            "type": "postback",
            "data": "action=buy&itemid=123"
          }
        }
      ]
    },
    {
      "richMenuId": "{richMenuId2}",
      "name": "Nice rich menu 2",
      "size": {
        "width": 2501,
        "height": 1687
      },
      "chatBarText": "Tap to open 2",
      "selected": true,
      "areas": [
        {
          "bounds": {
            "x": 0,
            "y": 0,
            "width": 1501,
            "height": 687
          },
          "action": {
            "type": "postback",
            "data": "action=buy&itemid=123"
          }
        },
        {
          "bounds": {
            "x": 1501,
            "y": 687,
            "width": 1000,
            "height": 1000
          },
          "action": {
            "type": "uri",
            "label": "メニューを見る",
            "uri": "https://example.com/menu"
          }
        }
      ]
    }
  ]
}
JSON;

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $this->assertEquals('https://api.line.me/v2/bot/richmenu/list', (string)$request->getUri());
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: $expectedResponseBody,
            ));
        $api = new MessagingApiApi($client);
        $richMenuListResponse = $api->getRichMenuList();
        $this->assertInstanceOf(RichMenuListResponse::class, $richMenuListResponse);
        $richMenus = $richMenuListResponse->getRichmenus();
        $this->assertCount(2, $richMenus);

        // First rich menu
        $this->assertInstanceOf(RichMenuResponse::class, $richMenus[0]);
        $this->assertEquals('{richMenuId}', $richMenus[0]->getRichMenuId());
        $this->assertEquals('Nice rich menu', $richMenus[0]->getName());
        $this->assertEquals('Tap to open', $richMenus[0]->getChatBarText());
        $this->assertFalse($richMenus[0]->getSelected());
        $this->assertInstanceOf(RichMenuSize::class, $richMenus[0]->getSize());
        $this->assertEquals(2500, $richMenus[0]->getSize()->getWidth());
        $this->assertEquals(1686, $richMenus[0]->getSize()->getHeight());
        $this->assertCount(1, $richMenus[0]->getAreas());

        // First rich menu - area check
        $area1 = $richMenus[0]->getAreas()[0];
        $this->assertInstanceOf(RichMenuArea::class, $area1);
        $this->assertEquals(0, $area1->getBounds()->getX());
        $this->assertEquals(0, $area1->getBounds()->getY());
        $this->assertEquals(2500, $area1->getBounds()->getWidth());
        $this->assertEquals(1686, $area1->getBounds()->getHeight());
        $this->assertInstanceOf(PostbackAction::class, $area1->getAction());
        $this->assertEquals('postback', $area1->getAction()->getType());
        $this->assertEquals('action=buy&itemid=123', $area1->getAction()->getData());

        // Second rich menu
        $this->assertInstanceOf(RichMenuResponse::class, $richMenus[1]);
        $this->assertEquals('{richMenuId2}', $richMenus[1]->getRichMenuId());
        $this->assertEquals('Nice rich menu 2', $richMenus[1]->getName());
        $this->assertEquals('Tap to open 2', $richMenus[1]->getChatBarText());
        $this->assertTrue($richMenus[1]->getSelected());
        $this->assertInstanceOf(RichMenuSize::class, $richMenus[1]->getSize());
        $this->assertEquals(2501, $richMenus[1]->getSize()->getWidth());
        $this->assertEquals(1687, $richMenus[1]->getSize()->getHeight());
        $this->assertCount(2, $richMenus[1]->getAreas());

        // Second rich menu - first area check
        $area21 = $richMenus[1]->getAreas()[0];
        $this->assertInstanceOf(RichMenuArea::class, $area21);
        $this->assertEquals(0, $area21->getBounds()->getX());
        $this->assertEquals(0, $area21->getBounds()->getY());
        $this->assertEquals(1501, $area21->getBounds()->getWidth());
        $this->assertEquals(687, $area21->getBounds()->getHeight());
        $this->assertInstanceOf(PostbackAction::class, $area21->getAction());
        $this->assertEquals('postback', $area21->getAction()->getType());
        $this->assertEquals('action=buy&itemid=123', $area21->getAction()->getData());

        // Second rich menu - second area check
        $area22 = $richMenus[1]->getAreas()[1];
        $this->assertInstanceOf(RichMenuArea::class, $area22);
        $this->assertEquals(1501, $area22->getBounds()->getX());
        $this->assertEquals(687, $area22->getBounds()->getY());
        $this->assertEquals(1000, $area22->getBounds()->getWidth());
        $this->assertEquals(1000, $area22->getBounds()->getHeight());
        $this->assertInstanceOf(URIAction::class, $area22->getAction());
        $this->assertEquals('uri', $area22->getAction()->getType());
        $this->assertEquals('メニューを見る', $area22->getAction()->getLabel());
        $this->assertEquals('https://example.com/menu', $area22->getAction()->getUri());
    }
}
