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

namespace LINE\Tests\Clients\MessagingApi\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\ApiException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * Verifies that Guzzle exceptions thrown by the HTTP client are converted
 * into ApiException in the same way on both Guzzle 7 and Guzzle 8.
 *
 * All exceptions are constructed only with signatures that are compatible
 * with both major versions:
 * - On Guzzle 7 every RequestException has getResponse() (nullable).
 * - On Guzzle 8 getResponse() exists only on ResponseException subclasses
 *   (BadResponseException, TooManyRedirectsException), so the API code
 *   guards the call with method_exists().
 *
 * See https://github.com/line/line-bot-sdk-php/pull/880
 */
class MessagingApiApiGuzzleExceptionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private const REQUEST_URI = 'https://api.line.me/v2/bot/followers/ids';

    private function requestForException(): Request
    {
        return new Request('GET', self::REQUEST_URI);
    }

    private function apiThrowingOnSend(\Throwable $exception): MessagingApiApi
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')->once()->andThrow($exception);
        return new MessagingApiApi($client);
    }

    private function apiRejectingOnSendAsync(\Throwable $exception): MessagingApiApi
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('sendAsync')->once()->andReturn(new RejectedPromise($exception));
        return new MessagingApiApi($client);
    }

    public function testSyncCallConvertsBadResponseExceptionKeepingResponse(): void
    {
        $body = '{"message":"The request body has 1 error(s)"}';
        $api = $this->apiThrowingOnSend(new BadResponseException(
            'Client error',
            $this->requestForException(),
            new Response(400, ['x-line-request-id' => 'req-id'], $body),
        ));

        try {
            $api->getFollowers();
            $this->fail('ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertSame(400, $e->getCode());
            $this->assertStringContainsString('[400]', $e->getMessage());
            $this->assertSame(['req-id'], $e->getResponseHeaders()['x-line-request-id']);
            $this->assertSame($body, $e->getResponseBody());
        }
    }

    public function testSyncCallConvertsTooManyRedirectsExceptionKeepingResponse(): void
    {
        $api = $this->apiThrowingOnSend(new TooManyRedirectsException(
            'Will not follow more than 5 redirects',
            $this->requestForException(),
            new Response(302, ['location' => 'https://example.com/'], ''),
        ));

        try {
            $api->getFollowers();
            $this->fail('ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertSame(302, $e->getCode());
            $this->assertSame(['https://example.com/'], $e->getResponseHeaders()['location']);
            $this->assertSame('', $e->getResponseBody());
        }
    }

    public function testSyncCallConvertsRequestExceptionWithoutResponse(): void
    {
        $api = $this->apiThrowingOnSend(new RequestException(
            'Error completing request',
            $this->requestForException(),
        ));

        try {
            $api->getFollowers();
            $this->fail('ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertSame(0, $e->getCode());
            $this->assertStringContainsString('Error completing request', $e->getMessage());
            $this->assertNull($e->getResponseHeaders());
            $this->assertNull($e->getResponseBody());
        }
    }

    public function testSyncCallConvertsConnectException(): void
    {
        $api = $this->apiThrowingOnSend(new ConnectException(
            'Connection refused',
            $this->requestForException(),
        ));

        try {
            $api->getFollowers();
            $this->fail('ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertSame(0, $e->getCode());
            $this->assertStringContainsString('Connection refused', $e->getMessage());
            $this->assertNull($e->getResponseHeaders());
            $this->assertNull($e->getResponseBody());
        }
    }

    public function testAsyncCallConvertsBadResponseExceptionKeepingResponse(): void
    {
        $body = '{"message":"The request body has 1 error(s)"}';
        $api = $this->apiRejectingOnSendAsync(new BadResponseException(
            'Client error',
            $this->requestForException(),
            new Response(400, ['x-line-request-id' => 'req-id'], $body),
        ));

        try {
            $api->getFollowersAsync()->wait();
            $this->fail('ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertSame(400, $e->getCode());
            $this->assertStringContainsString('Error connecting to the API', $e->getMessage());
            $this->assertSame(['req-id'], $e->getResponseHeaders()['x-line-request-id']);
            $this->assertSame($body, $e->getResponseBody());
        }
    }

    public function testAsyncCallConvertsRequestExceptionWithoutResponse(): void
    {
        $api = $this->apiRejectingOnSendAsync(new RequestException(
            'Error completing request',
            $this->requestForException(),
        ));

        try {
            $api->getFollowersAsync()->wait();
            $this->fail('ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertSame(0, $e->getCode());
            $this->assertStringContainsString('Error completing request', $e->getMessage());
            $this->assertNull($e->getResponseHeaders());
            $this->assertNull($e->getResponseBody());
        }
    }

    public function testAsyncCallConvertsConnectException(): void
    {
        $api = $this->apiRejectingOnSendAsync(new ConnectException(
            'Connection refused',
            $this->requestForException(),
        ));

        try {
            $api->getFollowersAsync()->wait();
            $this->fail('ApiException was not thrown');
        } catch (ApiException $e) {
            $this->assertSame(0, $e->getCode());
            $this->assertStringContainsString('Connection refused', $e->getMessage());
            $this->assertNull($e->getResponseHeaders());
            $this->assertNull($e->getResponseBody());
        }
    }
}
