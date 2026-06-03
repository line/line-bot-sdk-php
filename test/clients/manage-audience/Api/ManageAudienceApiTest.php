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

namespace LINE\Tests\Clients\ManageAudience\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\ManageAudience\Api\ManageAudienceApi;
use LINE\Clients\ManageAudience\Model\CreateAudienceGroupRequest;
use LINE\Clients\ManageAudience\Model\CreateAudienceGroupResponse;
use LINE\Clients\ManageAudience\Model\GetAudienceGroupsResponse;
use LINE\Clients\ManageAudience\Model\UpdateAudienceGroupDescriptionRequest;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

/**
 * Characterization tests for the auto-generated ManageAudienceApi client.
 *
 * They pin the request shape (method, path, query keys and JSON body keys) so a
 * future openapi-generator update that alters serialization is caught early.
 */
class ManageAudienceApiTest extends TestCase
{
    private function assertQueryEquals(array $expected, UriInterface $uri): void
    {
        $this->assertSame($expected, Query::parse($uri->getQuery()), 'Query parameters mismatch');
    }

    public function testCreateAudienceGroup(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('POST', $request->getMethod());
                    $this->assertEquals(
                        'https://api.line.me/v2/bot/audienceGroup/upload',
                        (string)$request->getUri()
                    );
                    $body = json_decode((string)$request->getBody(), true);
                    // camelCase attributeMap keys must be preserved in the JSON body.
                    $this->assertSame('test audience', $body['description']);
                    $this->assertFalse($body['isIfaAudience']);
                    $this->assertSame('upload', $body['uploadDescription']);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode([
                    'audienceGroupId' => 12345,
                    'type' => 'UPLOAD',
                    'description' => 'test audience',
                ]),
            ));
        $api = new ManageAudienceApi($client);
        $request = new CreateAudienceGroupRequest([
            'description' => 'test audience',
            'isIfaAudience' => false,
            'uploadDescription' => 'upload',
        ]);
        $response = $api->createAudienceGroup($request);
        $this->assertInstanceOf(CreateAudienceGroupResponse::class, $response);
        $this->assertEquals(12345, $response->getAudienceGroupId());
    }

    public function testGetAudienceGroups(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $uri = $request->getUri();
                    $this->assertEquals('/v2/bot/audienceGroup/list', $uri->getPath());
                    // Only the required "page" parameter is sent; optional null params are omitted.
                    $this->assertQueryEquals(['page' => '1'], $uri);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode([
                    'audienceGroups' => [],
                    'hasNextPage' => false,
                    'totalCount' => 0,
                    'page' => 1,
                    'size' => 20,
                ]),
            ));
        $api = new ManageAudienceApi($client);
        $response = $api->getAudienceGroups(page: 1);
        $this->assertInstanceOf(GetAudienceGroupsResponse::class, $response);
        $this->assertFalse($response->getHasNextPage());
        $this->assertEquals(0, $response->getTotalCount());
    }

    public function testDeleteAudienceGroupSubstitutesPathParameter(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('DELETE', $request->getMethod());
                    $this->assertEquals(
                        'https://api.line.me/v2/bot/audienceGroup/12345',
                        (string)$request->getUri()
                    );
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(status: 200, headers: [], body: ''));
        $api = new ManageAudienceApi($client);
        $api->deleteAudienceGroup(12345);
    }

    public function testUpdateAudienceGroupDescription(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('PUT', $request->getMethod());
                    $this->assertEquals(
                        'https://api.line.me/v2/bot/audienceGroup/12345/updateDescription',
                        (string)$request->getUri()
                    );
                    $body = json_decode((string)$request->getBody(), true);
                    $this->assertSame('new description', $body['description']);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(status: 200, headers: [], body: ''));
        $api = new ManageAudienceApi($client);
        $request = new UpdateAudienceGroupDescriptionRequest(['description' => 'new description']);
        $api->updateAudienceGroupDescription(12345, $request);
    }

    public function testThrowsApiExceptionOnMalformedJsonResponse(): void
    {
        // Pins the response-handling invariant that the 7.22.0 regeneration
        // refactors into handleResponseWithDataType().
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: 'this is not valid json {',
            ));
        $api = new ManageAudienceApi($client);
        $this->expectException(\LINE\Clients\ManageAudience\ApiException::class);
        $api->getAudienceGroups(page: 1);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
