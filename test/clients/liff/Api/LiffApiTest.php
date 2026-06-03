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

namespace LINE\Tests\Clients\Liff\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\Liff\Api\LiffApi;
use LINE\Clients\Liff\Model\AddLiffAppRequest;
use LINE\Clients\Liff\Model\AddLiffAppResponse;
use LINE\Clients\Liff\Model\GetAllLiffAppsResponse;
use LINE\Clients\Liff\Model\UpdateLiffAppRequest;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Characterization tests for the auto-generated LiffApi client.
 *
 * They pin the HTTP method, the resource path (including path-parameter
 * substitution) and the JSON body keys produced by ObjectSerializer, so that an
 * openapi-generator update which changes request building is caught early.
 */
class LiffApiTest extends TestCase
{
    public function testAddLIFFApp(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('POST', $request->getMethod());
                    $this->assertEquals('https://api.line.me/liff/v1/apps', (string)$request->getUri());
                    $body = json_decode((string)$request->getBody(), true);
                    // attributeMap keys are kept as-is (camelCase) in the JSON body.
                    $this->assertSame('https://example.com/liff', $body['view']['url']);
                    $this->assertSame('full', $body['view']['type']);
                    $this->assertSame('my liff app', $body['description']);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['liffId' => '1234567890-AbcdEfgh']),
            ));
        $api = new LiffApi($client);
        $request = new AddLiffAppRequest([
            'view' => ['type' => 'full', 'url' => 'https://example.com/liff'],
            'description' => 'my liff app',
        ]);
        $response = $api->addLIFFApp($request);
        $this->assertInstanceOf(AddLiffAppResponse::class, $response);
        $this->assertEquals('1234567890-AbcdEfgh', $response->getLiffId());
    }

    public function testGetAllLIFFApps(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $this->assertEquals('https://api.line.me/liff/v1/apps', (string)$request->getUri());
                    $this->assertEquals('', (string)$request->getBody());
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode([
                    'apps' => [
                        [
                            'liffId' => '1234567890-AbcdEfgh',
                            'view' => ['type' => 'full', 'url' => 'https://example.com/liff'],
                        ],
                    ],
                ]),
            ));
        $api = new LiffApi($client);
        $response = $api->getAllLIFFApps();
        $this->assertInstanceOf(GetAllLiffAppsResponse::class, $response);
        $apps = $response->getApps();
        $this->assertCount(1, $apps);
        $this->assertEquals('1234567890-AbcdEfgh', $apps[0]->getLiffId());
    }

    public function testUpdateLIFFAppSubstitutesPathParameter(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('PUT', $request->getMethod());
                    // The {liffId} path placeholder must be url-encoded and substituted.
                    $this->assertEquals(
                        'https://api.line.me/liff/v1/apps/1234567890-AbcdEfgh',
                        (string)$request->getUri()
                    );
                    $body = json_decode((string)$request->getBody(), true);
                    $this->assertSame('updated description', $body['description']);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(status: 200, headers: [], body: ''));
        $api = new LiffApi($client);
        $request = new UpdateLiffAppRequest(['description' => 'updated description']);
        $api->updateLIFFApp('1234567890-AbcdEfgh', $request);
    }

    public function testDeleteLIFFAppSubstitutesPathParameter(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('DELETE', $request->getMethod());
                    $this->assertEquals(
                        'https://api.line.me/liff/v1/apps/1234567890-AbcdEfgh',
                        (string)$request->getUri()
                    );
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(status: 200, headers: [], body: ''));
        $api = new LiffApi($client);
        $api->deleteLIFFApp('1234567890-AbcdEfgh');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
