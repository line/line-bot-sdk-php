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
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\ChannelAccessToken\Api\ChannelAccessTokenApi;
use Mockery;
use PHPUnit\Framework\TestCase;

class ChannelAccessTokenApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
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
}
