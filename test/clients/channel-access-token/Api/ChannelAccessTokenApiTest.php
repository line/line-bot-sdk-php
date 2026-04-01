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

namespace LINE\Tests\Clients\ChannelAccessToken\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\ChannelAccessToken\Api\ChannelAccessTokenApi;
use LINE\Clients\ChannelAccessToken\Model\ChannelAccessTokenKeyIdsResponse;
use LINE\Clients\ChannelAccessToken\Model\VerifyChannelAccessTokenResponse;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class ChannelAccessTokenApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function assertQueryContains(array $expected, UriInterface $uri): void
    {
        $actual = Query::parse($uri->getQuery());
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $actual, "Query parameter '{$key}' is missing");
            $this->assertSame($value, $actual[$key], "Query parameter '{$key}' has wrong value");
        }
    }

    private function assertQueryNotContains(array $keys, UriInterface $uri): void
    {
        $actual = Query::parse($uri->getQuery());
        foreach ($keys as $key) {
            $this->assertArrayNotHasKey($key, $actual, "Query parameter '{$key}' should not be present (camelCase key used instead of snake_case)");
        }
    }

    public function testVerifyChannelTokenByJWT(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $uri = $request->getUri();
                    // Must use snake_case key from OpenAPI spec
                    $this->assertQueryContains(['access_token' => 'myAccessToken'], $uri);
                    // Must NOT use camelCase key
                    $this->assertQueryNotContains(['accessToken'], $uri);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['client_id' => '1234', 'expires_in' => 2592000, 'scope' => 'profile']),
            ));
        $api = new ChannelAccessTokenApi($client);
        $response = $api->verifyChannelTokenByJWT(accessToken: 'myAccessToken');
        $this->assertInstanceOf(VerifyChannelAccessTokenResponse::class, $response);
        $this->assertEquals('1234', $response->getClientId());
        $this->assertEquals(2592000, $response->getExpiresIn());
    }

    public function testGetsAllValidChannelAccessTokenKeyIds(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('GET', $request->getMethod());
                    $uri = $request->getUri();
                    // Must use snake_case keys from OpenAPI spec
                    $this->assertQueryContains([
                        'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                        'client_assertion' => 'myJwt',
                    ], $uri);
                    // Must NOT use camelCase keys
                    $this->assertQueryNotContains(['clientAssertionType', 'clientAssertion'], $uri);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['kids' => ['kid1', 'kid2']]),
            ));
        $api = new ChannelAccessTokenApi($client);
        $response = $api->getsAllValidChannelAccessTokenKeyIds(
            clientAssertionType: 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            clientAssertion: 'myJwt',
        );
        $this->assertInstanceOf(ChannelAccessTokenKeyIdsResponse::class, $response);
        $this->assertEquals(['kid1', 'kid2'], $response->getKids());
    }

    public function testIssueStatelessChannelToken(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('POST', $request->getMethod());
                    $this->assertEquals('https://api.line.me/oauth2/v3/token', (string)$request->getUri());
                    $this->assertEquals('grant_type=client_credentials&client_id=1234&client_secret=clientSecret', (string)$request->getBody());
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['access_token' => 'accessToken', 'expires_in' => 30, 'token_type' => 'Bearer',]),
            ));
        $api = new ChannelAccessTokenApi($client);
        $response = $api->issueStatelessChannelToken(grantType: "client_credentials", clientId: "1234", clientSecret: "clientSecret");
        $this->assertEquals('accessToken', $response->getAccessToken());
        $this->assertEquals(30, $response->getExpiresIn());
        $this->assertEquals('Bearer', $response->getTokenType());
    }

    public function testIssueStatelessChannelTokenByJWTAssertion(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('POST', $request->getMethod());
                    $this->assertEquals('https://api.line.me/oauth2/v3/token', (string)$request->getUri());
                    $body = (string)$request->getBody();
                    parse_str($body, $params);
                    $this->assertEquals('client_credentials', $params['grant_type']);
                    $this->assertEquals('urn:ietf:params:oauth:client-assertion-type:jwt-bearer', $params['client_assertion_type']);
                    $this->assertEquals('jwtAssertionToken', $params['client_assertion']);
                    $this->assertArrayNotHasKey('client_id', $params);
                    $this->assertArrayNotHasKey('client_secret', $params);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['access_token' => 'accessToken', 'expires_in' => 30, 'token_type' => 'Bearer']),
            ));
        $api = new ChannelAccessTokenApi($client);
        $response = $api->issueStatelessChannelTokenByJWTAssertion(clientAssertion: "jwtAssertionToken");
        $this->assertEquals('accessToken', $response->getAccessToken());
        $this->assertEquals(30, $response->getExpiresIn());
        $this->assertEquals('Bearer', $response->getTokenType());
    }

    public function testIssueStatelessChannelTokenByClientSecret(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('POST', $request->getMethod());
                    $this->assertEquals('https://api.line.me/oauth2/v3/token', (string)$request->getUri());
                    $body = (string)$request->getBody();
                    parse_str($body, $params);
                    $this->assertEquals('client_credentials', $params['grant_type']);
                    $this->assertEquals('1234', $params['client_id']);
                    $this->assertEquals('clientSecret', $params['client_secret']);
                    $this->assertArrayNotHasKey('client_assertion_type', $params);
                    $this->assertArrayNotHasKey('client_assertion', $params);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['access_token' => 'accessToken', 'expires_in' => 30, 'token_type' => 'Bearer']),
            ));
        $api = new ChannelAccessTokenApi($client);
        $response = $api->issueStatelessChannelTokenByClientSecret(clientId: "1234", clientSecret: "clientSecret");
        $this->assertEquals('accessToken', $response->getAccessToken());
        $this->assertEquals(30, $response->getExpiresIn());
        $this->assertEquals('Bearer', $response->getTokenType());
    }
}
