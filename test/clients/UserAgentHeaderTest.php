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

namespace LINE\Tests\Clients;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;
use LINE\Constants\SdkUserAgent;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserAgentHeaderTest extends TestCase
{
    public function testDefaultUserAgentHeader(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertSame(
                        SdkUserAgent::create(),
                        $request->getHeaderLine('User-Agent'),
                    );
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['userIds' => [], 'next' => null]),
            ));

        $api = new MessagingApiApi($client);
        $api->getFollowers();
    }

    public function testCustomUserAgentHeader(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertSame(
                        'custom-user-agent',
                        $request->getHeaderLine('User-Agent'),
                    );
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode(['userIds' => [], 'next' => null]),
            ));

        $config = new Configuration();
        $config->setUserAgent('custom-user-agent');
        $api = new MessagingApiApi($client, $config);
        $api->getFollowers();
    }
}
