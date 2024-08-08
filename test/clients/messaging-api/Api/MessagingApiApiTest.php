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
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use Mockery;
use PHPUnit\Framework\TestCase;

class MessagingApiApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
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
        $followers = $api->getFollowers(limit:  99);
        $this->assertEquals(["Uaaaaaaaa...", "Ubbbbbbbb...", "Ucccccccc..."], $followers->getUserIds());
        $this->assertEquals("yANU9IA..", $followers->getNext());
    }
}
