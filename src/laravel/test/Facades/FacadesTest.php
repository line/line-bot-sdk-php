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

namespace LINE\Laravel\Tests\Facades;

/**
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
class FacadesTest extends \Orchestra\Testbench\TestCase
{
    /**
     * Load package service provider
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     * @param  \Illuminate\Foundation\Application $app
     * @return array<string>
     */
    protected function getPackageProviders($app): array
    {
        return ['LINE\Laravel\LINEBotServiceProvider'];
    }

    /**
     * Load package alias
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     * @param  \Illuminate\Foundation\Application $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'LINEChannelAccessTokenApi' => \LINE\Laravel\Facades\LINEChannelAccessTokenApi::class,
            'LINEInsightApi' => \LINE\Laravel\Facades\LINEInsightApi::class,
            'LINELiffApi' => \LINE\Laravel\Facades\LINELiffApi::class,
            'LINEManageAudienceApi' => \LINE\Laravel\Facades\LINEManageAudienceApi::class,
            'LINEManageAudienceBlobApi' => \LINE\Laravel\Facades\LINEManageAudienceBlobApi::class,
            'LINEMessagingApi' => \LINE\Laravel\Facades\LINEMessagingApi::class,
            'LINEMessagingBlobApi' => \LINE\Laravel\Facades\LINEMessagingBlobApi::class,
        ];
    }

    /**
     * Testing config loaded
     *
     * @return void
     */
    public function testConfigLoaded(): void
    {
        $this->assertEquals('test_channel_access_token', config('line-bot.channel_access_token'));
        $this->assertEquals('test_channel_secret', config('line-bot.channel_secret'));
    }

    /**
     * Testing LINEBot facade instance
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     * @return void
     */
    public function testLINEBotFacadeInstance(): void
    {
        $this->assertInstanceOf(\LINE\Clients\ChannelAccessToken\Api\ChannelAccessTokenApi::class, \LINEChannelAccessTokenApi::getFacadeRoot());
        $this->assertInstanceOf(\LINE\Clients\Insight\Api\InsightApi::class, \LINEInsightApi::getFacadeRoot());
        $this->assertInstanceOf(\LINE\Clients\Liff\Api\LiffApi::class, \LINELiffApi::getFacadeRoot());
        $this->assertInstanceOf(\LINE\Clients\ManageAudience\Api\ManageAudienceApi::class, \LINEManageAudienceApi::getFacadeRoot());
        $this->assertInstanceOf(\LINE\Clients\ManageAudience\Api\ManageAudienceBlobApi::class, \LINEManageAudienceBlobApi::getFacadeRoot());
        $this->assertInstanceOf(\LINE\Clients\MessagingApi\Api\MessagingApiApi::class, \LINEMessagingApi::getFacadeRoot());
        $this->assertInstanceOf(\LINE\Clients\MessagingApi\Api\MessagingApiBlobApi::class, \LINEMessagingBlobApi::getFacadeRoot());
    }
}
