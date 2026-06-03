<?php

/**
 * Copyright 2026 LINE Corporation
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

namespace LINE\Tests\Clients\Insight\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\Insight\Api\InsightApi;
use LINE\Clients\Insight\Model\GetFriendsDemographicsResponse;
use LINE\Clients\Insight\Model\GetMessageEventResponse;
use LINE\Clients\Insight\Model\GetNumberOfFollowersResponse;
use LINE\Clients\Insight\Model\GetNumberOfMessageDeliveriesResponse;
use LINE\Clients\Insight\Model\GetStatisticsPerUnitResponse;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

/**
 * Characterization tests for the auto-generated InsightApi client.
 *
 * These tests pin the request shape (HTTP method, URL path and query parameter
 * keys) that the OpenAPI generator currently produces. They are intentionally
 * strict about query keys so that a future openapi-generator bump that changes
 * the serialization (e.g. camelCase vs snake_case, like the form-param
 * regression fixed in #810/#823) is caught before release.
 */
class InsightApiTest extends TestCase
{
    private function assertQueryEquals(array $expected, UriInterface $uri): void
    {
        $this->assertSame($expected, Query::parse($uri->getQuery()), 'Query parameters mismatch');
    }

    public function testGetFriendsDemographics(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $this->assertEquals(
                        'https://api.line.me/v2/bot/insight/demographic',
                        (string)$request->getUri()
                    );
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['available' => true, 'genders' => []]),
            ));
        $api = new InsightApi($client);
        $response = $api->getFriendsDemographics();
        $this->assertInstanceOf(GetFriendsDemographicsResponse::class, $response);
        $this->assertTrue($response->getAvailable());
    }

    public function testGetNumberOfFollowers(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $uri = $request->getUri();
                    $this->assertEquals('/v2/bot/insight/followers', $uri->getPath());
                    $this->assertQueryEquals(['date' => '20260601'], $uri);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode([
                    'status' => 'ready',
                    'followers' => 100,
                    'targetedReaches' => 90,
                    'blocks' => 5,
                ]),
            ));
        $api = new InsightApi($client);
        $response = $api->getNumberOfFollowers(date: '20260601');
        $this->assertInstanceOf(GetNumberOfFollowersResponse::class, $response);
        $this->assertEquals('ready', $response->getStatus());
        $this->assertEquals(100, $response->getFollowers());
        $this->assertEquals(5, $response->getBlocks());
    }

    public function testGetNumberOfMessageDeliveries(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $uri = $request->getUri();
                    $this->assertEquals('/v2/bot/insight/message/delivery', $uri->getPath());
                    $this->assertQueryEquals(['date' => '20260601'], $uri);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['status' => 'ready', 'broadcast' => 50]),
            ));
        $api = new InsightApi($client);
        $response = $api->getNumberOfMessageDeliveries(date: '20260601');
        $this->assertInstanceOf(GetNumberOfMessageDeliveriesResponse::class, $response);
        $this->assertEquals('ready', $response->getStatus());
        $this->assertEquals(50, $response->getBroadcast());
    }

    public function testGetMessageEvent(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $uri = $request->getUri();
                    $this->assertEquals('/v2/bot/insight/message/event', $uri->getPath());
                    // The LINE Insight API uses the camelCase "requestId" query key.
                    $this->assertQueryEquals(['requestId' => 'abcdef'], $uri);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['overview' => ['requestId' => 'abcdef']]),
            ));
        $api = new InsightApi($client);
        $response = $api->getMessageEvent(requestId: 'abcdef');
        $this->assertInstanceOf(GetMessageEventResponse::class, $response);
    }

    public function testGetStatisticsPerUnit(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $uri = $request->getUri();
                    $this->assertEquals('/v2/bot/insight/message/event/aggregation', $uri->getPath());
                    // All three query keys are camelCase in the spec and must be preserved.
                    $this->assertQueryEquals([
                        'customAggregationUnit' => 'promotion_a',
                        'from' => '20260601',
                        'to' => '20260630',
                    ], $uri);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['overview' => [], 'messages' => [], 'clicks' => []]),
            ));
        $api = new InsightApi($client);
        $response = $api->getStatisticsPerUnit(
            customAggregationUnit: 'promotion_a',
            from: '20260601',
            to: '20260630',
        );
        $this->assertInstanceOf(GetStatisticsPerUnitResponse::class, $response);
    }

    public function testGetStatisticsPerUnitRejectsInvalidUnit(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $api = new InsightApi($client);
        $this->expectException(\InvalidArgumentException::class);
        // "customAggregationUnit" must match /^[a-zA-Z0-9_]{1,30}$/.
        $api->getStatisticsPerUnit(
            customAggregationUnit: 'invalid unit!',
            from: '20260601',
            to: '20260630',
        );
    }

    public function testGetNumberOfMessageDeliveriesRejectsInvalidDate(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $api = new InsightApi($client);
        $this->expectException(\InvalidArgumentException::class);
        // "date" must match /^[0-9]{8}$/.
        $api->getNumberOfMessageDeliveries(date: '2026-06-01');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
