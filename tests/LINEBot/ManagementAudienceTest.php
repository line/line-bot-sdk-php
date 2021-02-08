<?php

/**
 * Copyright 2020 LINE Corporation
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

namespace LINE\Tests\LINEBot;

use LINE\LINEBot;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class ManagementAudienceTest extends TestCase
{
    public function testCreateAudienceGroupForUploadingUserIds202()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/upload', $url);

            return [
                'status' => 202,
                'audienceGroupId' => 4389303728991,
                'type' => 'UPLOAD',
                'description' => 'TEST DESCRIPTION',
                'created' => 1500351844,
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createAudienceGroupForUploadingUserIds(
            'TEST DESCRIPTION',
            [
                ['id' => 'USER ID1'],
                ['id' => 'USER ID2'],
            ]
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(202, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(4389303728991, $data['audienceGroupId']);
        $this->assertEquals('UPLOAD', $data['type']);
        $this->assertEquals('TEST DESCRIPTION', $data['description']);
        $this->assertEquals(1500351844, $data['created']);
    }

    public function testCreateAudienceGroupForUploadingUserIds400()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/upload', $url);

            return [
                'status' => 400,
                'message' => 'ERROR MESSAGE.',
                'details' => 'AUDIENCE_GROUP_COUNT_MAX_OVER',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createAudienceGroupForUploadingUserIds(
            'TEST DESCRIPTION',
            [
                ['id' => 'USER ID1'],
                ['id' => 'USER ID2'],
            ]
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(400, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ERROR MESSAGE.', $data['message']);
        $this->assertEquals('AUDIENCE_GROUP_COUNT_MAX_OVER', $data['details']);
    }

    public function createAudienceGroupForUploadingUserIdsByFile202()
    {
        $file_name = __DIR__ . '/test.txt';
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api-data.line.me/v2/bot/audienceGroup/upload/byFile', $url);

            return [
                'status' => 202,
                'audienceGroupId' => 4389303728991,
                'type' => 'UPLOAD',
                'description' => 'TEST DESCRIPTION',
                'created' => 1500351844,
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createAudienceGroupForUploadingUserIdsByFile('TEST DESCRIPTION', $file_name);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(202, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(4389303728991, $data['audienceGroupId']);
        $this->assertEquals('UPLOAD', $data['type']);
        $this->assertEquals('TEST DESCRIPTION', $data['description']);
        $this->assertEquals(1500351844, $data['created']);
    }

    public function createAudienceGroupForUploadingUserIdsByFile400()
    {
        $file_name = __DIR__ . '/test.txt';
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api-data.line.me/v2/bot/audienceGroup/upload/byFile', $url);

            return [
                'status' => 400,
                'message' => 'ERROR MESSAGE.',
                'details' => 'AUDIENCE_GROUP_COUNT_MAX_OVER',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createAudienceGroupForUploadingUserIdsByFile('TEST DESCRIPTION', $file_name);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(400, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ERROR MESSAGE.', $data['message']);
        $this->assertEquals('AUDIENCE_GROUP_COUNT_MAX_OVER', $data['details']);
    }

    public function testUpdateAudienceGroupForUploadingUserIds202()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/upload', $url);

            return [
                'status' => 202,
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->updateAudienceGroupForUploadingUserIds(
            4389303728991,
            [
                ['id' => 'USER ID2'],
                ['id' => 'USER ID3'],
            ]
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(202, $res->getJSONDecodedBody()['status']);
    }

    public function testUpdateAudienceGroupForUploadingUserIds400()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/upload', $url);

            return [
                'status' => 400,
                'message' => 'ERROR MESSAGE.',
                'details' => 'UPLOAD_AUDIENCE_GROUP_INVALID_AUDIENCE_ID_FORMAT',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->updateAudienceGroupForUploadingUserIds(
            4389303728991,
            [
                ['id' => 'USER ID2'],
                ['id' => 'USER ID3'],
            ]
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(400, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ERROR MESSAGE.', $data['message']);
        $this->assertEquals('UPLOAD_AUDIENCE_GROUP_INVALID_AUDIENCE_ID_FORMAT', $data['details']);
    }

    public function testUpdateAudienceGroupForUploadingUserIdsByFile202()
    {
        $file_name = __DIR__ . '/test.txt';
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals('https://api-data.line.me/v2/bot/audienceGroup/upload/byFile', $url);

            return [
                'status' => 202,
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->updateAudienceGroupForUploadingUserIdsByFile(4389303728991, $file_name);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(202, $res->getJSONDecodedBody()['status']);
    }

    public function testUpdateAudienceGroupForUploadingUserIdsByFile400()
    {
        $file_name = __DIR__ . '/test.txt';
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals('https://api-data.line.me/v2/bot/audienceGroup/upload/byFile', $url);

            return [
                'status' => 400,
                'message' => 'ERROR MESSAGE.',
                'details' => 'UPLOAD_AUDIENCE_GROUP_INVALID_AUDIENCE_ID_FORMAT',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->updateAudienceGroupForUploadingUserIdsByFile(4389303728991, $file_name);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(400, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ERROR MESSAGE.', $data['message']);
        $this->assertEquals('UPLOAD_AUDIENCE_GROUP_INVALID_AUDIENCE_ID_FORMAT', $data['details']);
    }

    public function testCreateAudienceGroupForClick202()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/click', $url);

            return [
                'status' => 202,
                'audienceGroupId' => 4389303728991,
                'type' => 'CLICK',
                'requestId' => 'f70dd685-499a-4231-a441-f24b8d4fba21',
                'description' => 'TEST DESCRIPTION',
                'clickUrl' => 'https://line.me/en',
                'created' => 1500351844,
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createAudienceGroupForClick(
            'TEST DESCRIPTION',
            'f70dd685-499a-4231-a441-f24b8d4fba21',
            'https://line.me/en'
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(202, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(4389303728991, $data['audienceGroupId']);
        $this->assertEquals('CLICK', $data['type']);
        $this->assertEquals('TEST DESCRIPTION', $data['description']);
        $this->assertEquals('f70dd685-499a-4231-a441-f24b8d4fba21', $data['requestId']);
        $this->assertEquals('https://line.me/en', $data['clickUrl']);
        $this->assertEquals(1500351844, $data['created']);
    }

    public function testCreateAudienceGroupForClick400()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/click', $url);

            return [
                'status' => 400,
                'message' => 'ERROR MESSAGE.',
                'details' => 'AUDIENCE_GROUP_COUNT_MAX_OVER',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createAudienceGroupForClick(
            'TEST DESCRIPTION',
            'f70dd685-499a-4231-a441-f24b8d4fba21',
            'https://line.me/en'
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(400, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ERROR MESSAGE.', $data['message']);
        $this->assertEquals('AUDIENCE_GROUP_COUNT_MAX_OVER', $data['details']);
    }

    public function testCreateAudienceGroupForImpression202()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/imp', $url);

            return [
                'status' => 202,
                'audienceGroupId' => 4389303728991,
                'type' => 'IMP',
                'requestId' => 'f70dd685-499a-4231-a441-f24b8d4fba21',
                'description' => 'TEST DESCRIPTION',
                'created' => 1500351844,
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createAudienceGroupForImpression(
            'TEST DESCRIPTION',
            'f70dd685-499a-4231-a441-f24b8d4fba21'
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(202, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals(4389303728991, $data['audienceGroupId']);
        $this->assertEquals('IMP', $data['type']);
        $this->assertEquals('TEST DESCRIPTION', $data['description']);
        $this->assertEquals('f70dd685-499a-4231-a441-f24b8d4fba21', $data['requestId']);
        $this->assertEquals(1500351844, $data['created']);
    }

    public function testCreateAudienceGroupForImpression400()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/imp', $url);

            return [
                'status' => 400,
                'message' => 'ERROR MESSAGE.',
                'details' => 'AUDIENCE_GROUP_COUNT_MAX_OVER',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createAudienceGroupForImpression(
            'TEST DESCRIPTION',
            'f70dd685-499a-4231-a441-f24b8d4fba21'
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(400, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ERROR MESSAGE.', $data['message']);
        $this->assertEquals('AUDIENCE_GROUP_COUNT_MAX_OVER', $data['details']);
    }

    public function testRenameAudience202()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/4389303728991/updateDescription', $url);

            return [];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->renameAudience(4389303728991, 'TEST DESCRIPTION');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
    }

    public function testRenameAudience400()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/4389303728991/updateDescription', $url);

            return [
                'status' => 400,
                'message' => 'ERROR MESSAGE.',
                'details' => 'AUDIENCE_GROUP_NAME_WRONG',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->renameAudience(4389303728991, 'TEST DESCRIPTION\n');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(400, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ERROR MESSAGE.', $data['message']);
        $this->assertEquals('AUDIENCE_GROUP_NAME_WRONG', $data['details']);
    }

    public function testDeleteAudience202()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('DELETE', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/4389303728991', $url);

            return ['status' => 202];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->deleteAudience(4389303728991);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(202, $res->getJSONDecodedBody()['status']);
    }

    public function testDeleteAudience400()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('DELETE', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/4389303728991', $url);

            return [
                'status' => 400,
                'message' => 'ERROR MESSAGE.',
                'details' => 'AUDIENCE_GROUP_NOT_FOUND',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->deleteAudience(4389303728991);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(400, $res->getJSONDecodedBody()['status']);

        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ERROR MESSAGE.', $data['message']);
        $this->assertEquals('AUDIENCE_GROUP_NOT_FOUND', $data['details']);
    }

    public function testGetAudience200()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/4389303728991', $url);

            return [
                'audienceGroupId' => 4389303728991,
                'type' => 'CLICK',
                'description' => 'TEST DESCRIPTION',
                'status' => 'READY',
                'audienceCount' => 1000,
                'created' => 1500351844,
                'requestId' => 'f70dd685-499a-4231-a441-f24b8d4fba21',
                'clickUrl' => 'https://line.me/en',
                'isIfaAudience' => false,
                'permission' => 'READ',
                'createRoute' => 'MESSAGING_API',
                'jobs' => [
                    [
                        'audienceGroupJobId' => 8389303728991,
                        'audienceGroupId' => 4389303728991,
                        'description' => 'JOB1 DESCRIPTION',
                        'type' => 'DIFF_ADD',
                        'jobStatus' => 'WORKING',
                        'audienceCount' => 500,
                        'created' => 1500351944,
                    ], [
                        'audienceGroupJobId' => 8389303728992,
                        'audienceGroupId' => 4389303728991,
                        'description' => 'JOB2 DESCRIPTION',
                        'type' => 'DIFF_ADD',
                        'jobStatus' => 'FAILED',
                        'failedType' => 'INTERNAL_ERROR',
                        'audienceCount' => 500,
                        'created' => 1500351944,
                    ],
                ]
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getAudience(4389303728991);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(4389303728991, $res->getJSONDecodedBody()['audienceGroupId']);
        $this->assertEquals('CLICK', $res->getJSONDecodedBody()['type']);
        $this->assertEquals('TEST DESCRIPTION', $res->getJSONDecodedBody()['description']);
        $this->assertEquals('READY', $res->getJSONDecodedBody()['status']);
        $this->assertEquals(1000, $res->getJSONDecodedBody()['audienceCount']);
        $this->assertEquals(1500351844, $res->getJSONDecodedBody()['created']);
        $this->assertEquals('f70dd685-499a-4231-a441-f24b8d4fba21', $res->getJSONDecodedBody()['requestId']);
        $this->assertEquals('https://line.me/en', $res->getJSONDecodedBody()['clickUrl']);
        $this->assertEquals(false, $res->getJSONDecodedBody()['isIfaAudience']);
        $this->assertEquals('READ', $res->getJSONDecodedBody()['permission']);
        $this->assertEquals('MESSAGING_API', $res->getJSONDecodedBody()['createRoute']);
        $this->assertEquals([
            [
                'audienceGroupJobId' => 8389303728991,
                'audienceGroupId' => 4389303728991,
                'description' => 'JOB1 DESCRIPTION',
                'type' => 'DIFF_ADD',
                'jobStatus' => 'WORKING',
                'audienceCount' => 500,
                'created' => 1500351944,
            ], [
                'audienceGroupJobId' => 8389303728992,
                'audienceGroupId' => 4389303728991,
                'description' => 'JOB2 DESCRIPTION',
                'type' => 'DIFF_ADD',
                'jobStatus' => 'FAILED',
                'failedType' => 'INTERNAL_ERROR',
                'audienceCount' => 500,
                'created' => 1500351944,
            ],
        ], $res->getJSONDecodedBody()['jobs']);
    }

    public function testGetAuthorityLevel200()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/authorityLevel', $url);

            return [
                'authorityLevel' => 'PUBLIC',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->getAuthorityLevel();

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals('PUBLIC', $res->getJSONDecodedBody()['authorityLevel']);
    }

    public function testUpdateAuthorityLevel200()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/audienceGroup/authorityLevel', $url);

            return [];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->updateAuthorityLevel('PUBLIC');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
    }

    public function testActivateAudience202()
    {
        $audienceGroupId = "4389303728991";
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($audienceGroupId) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals("https://api.line.me/v2/bot/audienceGroup/{$audienceGroupId}/activate", $url);
            return [];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock, 202), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->activateAudience($audienceGroupId);

        $this->assertEquals(202, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
    }

    public function testActivateAudience400()
    {
        $audienceGroupId = "4389303728991";
        $mock = function ($testRunner, $httpMethod, $url, $data) use ($audienceGroupId) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('PUT', $httpMethod);
            $testRunner->assertEquals("https://api.line.me/v2/bot/audienceGroup/{$audienceGroupId}/activate", $url);
            return [
                'status' => 400,
                'message' => 'ERROR MESSAGE.',
                'details' => 'ALREADY_ACTIVE',
            ];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock, 400), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->activateAudience($audienceGroupId);

        $this->assertEquals(400, $res->getHTTPStatus());
        $this->assertFalse($res->isSucceeded());
        $data = $res->getJSONDecodedBody();
        $this->assertEquals('ERROR MESSAGE.', $data['message']);
        $this->assertEquals('ALREADY_ACTIVE', $data['details']);
    }
}
