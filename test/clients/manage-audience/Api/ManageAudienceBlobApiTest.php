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
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\Clients\ManageAudience\Api\ManageAudienceBlobApi;
use LINE\Clients\ManageAudience\Model\CreateAudienceGroupResponse;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SplFileObject;

class ManageAudienceBlobApiTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempFile = tempnam(sys_get_temp_dir(), 'audience-test-');
        file_put_contents($this->tempFile, "U1234567890abcdef\nU0987654321fedcba\n");
    }

    protected function tearDown(): void
    {
        @unlink($this->tempFile);
        parent::tearDown();
    }

    public function testCreateAudienceForUploadingUserIds(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('POST', $request->getMethod());
                    $this->assertEquals(
                        'https://api-data.line.me/v2/bot/audienceGroup/upload/byFile',
                        (string)$request->getUri()
                    );
                    $this->assertInstanceOf(MultipartStream::class, $request->getBody());
                    $body = (string)$request->getBody();
                    // form param keys must follow the OpenAPI spec (camelCase here)
                    $this->assertStringContainsString('name="description"', $body);
                    $this->assertStringContainsString('name="isIfaAudience"', $body);
                    $this->assertStringContainsString('name="uploadDescription"', $body);
                    $this->assertStringContainsString('name="file"', $body);
                    // file content is included
                    $this->assertStringContainsString('U1234567890abcdef', $body);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(
                status: 200,
                headers: [],
                body: json_encode([
                    'audienceGroupId' => 1234567890,
                    'createRoute' => 'MESSAGING_API',
                    'type' => 'UPLOAD',
                    'description' => 'audience-test',
                    'created' => 1700000000,
                    'permission' => 'READ_WRITE',
                    'expireTimestamp' => 1800000000,
                    'isIfaAudience' => false,
                ]),
            ));
        $api = new ManageAudienceBlobApi($client);
        $response = $api->createAudienceForUploadingUserIds(
            file: new SplFileObject($this->tempFile),
            description: 'audience-test',
            isIfaAudience: false,
            uploadDescription: 'upload-1',
        );
        $this->assertInstanceOf(CreateAudienceGroupResponse::class, $response);
        $this->assertEquals(1234567890, $response->getAudienceGroupId());
        $this->assertEquals('MESSAGING_API', $response->getCreateRoute());
    }

    public function testAddUserIdsToAudience(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->with(
                Mockery::on(function (Request $request) {
                    $this->assertEquals('PUT', $request->getMethod());
                    $this->assertEquals(
                        'https://api-data.line.me/v2/bot/audienceGroup/upload/byFile',
                        (string)$request->getUri()
                    );
                    $this->assertInstanceOf(MultipartStream::class, $request->getBody());
                    $body = (string)$request->getBody();
                    $this->assertStringContainsString('name="audienceGroupId"', $body);
                    $this->assertStringContainsString('1234567890', $body);
                    $this->assertStringContainsString('name="uploadDescription"', $body);
                    $this->assertStringContainsString('name="file"', $body);
                    $this->assertStringContainsString('U1234567890abcdef', $body);
                    return true;
                }),
                []
            )
            ->once()
            ->andReturn(new Response(status: 202, headers: [], body: ''));
        $api = new ManageAudienceBlobApi($client);
        $api->addUserIdsToAudience(
            file: new SplFileObject($this->tempFile),
            audienceGroupId: 1234567890,
            uploadDescription: 'upload-2',
        );
    }
}
