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

namespace LINE\Laravel;

use Guzzle\Http\Client;
use LINE\Clients\ChannelAccessToken\Api\ChannelAccessTokenApi;
use LINE\Clients\Insight\Api\InsightApi;
use LINE\Clients\ChannelAccessToken\Configuration as ChannelAccessTokenConfiguration;
use LINE\Clients\Insight\Configuration as InsightConfiguration;
use LINE\Clients\Liff\Api\LiffApi;
use LINE\Clients\Liff\Configuration as LiffConfiguration;
use LINE\Clients\ManageAudience\Api\ManageAudienceApi;
use LINE\Clients\ManageAudience\Api\ManageAudienceBlobApi;
use LINE\Clients\ManageAudience\Configuration as ManageAudienceConfiguration;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;
use LINE\Clients\MessagingApi\Configuration as MessagingApiConfiguration;

class LINEBotServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/line-bot.php',
            'line-bot'
        );
        $this->app->bind('line-bot-http-client', function () {
            return new \GuzzleHttp\Client(
                config: config('line-bot.client.config'),
            );
        });
        $this->bindChannelAccessTokenApi();
        $this->bindInsightApi();
        $this->bindLiffApi();
        $this->bindManageAudienceApi();
        $this->bindManageAudienceBlobApi();
        $this->bindMessagingApi();
        $this->bindMessagingBlobApi();
    }

    private function bindChannelAccessTokenApi() {
        $this->app->bind('line-bot-channel-access-token-api', function ($app) {
            $httpClient = $app->make('line-bot-http-client');
            $config = new ChannelAccessTokenConfiguration();
            return new ChannelAccessTokenApi(
                client: $httpClient,
                config: $config,
            );
        });
    }

    private function bindInsightApi() {
        $this->app->bind('line-bot-insight-api', function ($app) {
            $httpClient = $app->make('line-bot-http-client');
            $config = new InsightConfiguration();
            $config->setAccessToken(config('line-bot.channel_access_token'));
            return new InsightApi(
                client: $httpClient,
                config: $config,
            );
        });
    }

    private function bindLiffApi() {
        $this->app->bind('line-bot-liff-api', function ($app) {
            $httpClient = $app->make('line-bot-http-client');
            $config = new LiffConfiguration();
            $config->setAccessToken(config('line-bot.channel_access_token'));
            return new LiffApi(
                client: $httpClient,
                config: $config,
            );
        });
    }

    private function bindManageAudienceApi() {
        $this->app->bind('line-bot-manage-audience-api', function ($app) {
            $httpClient = $app->make('line-bot-http-client');
            $config = new ManageAudienceConfiguration();
            $config->setAccessToken(config('line-bot.channel_access_token'));
            return new ManageAudienceApi(
                client: $httpClient,
                config: $config,
            );
        });
    }

    private function bindManageAudienceBlobApi() {
        $this->app->bind('line-bot-manage-audience-blob-api', function ($app) {
            $httpClient = $app->make('line-bot-http-client');
            $config = new ManageAudienceConfiguration();
            $config->setAccessToken(config('line-bot.channel_access_token'));
            return new ManageAudienceBlobApi(
                client: $httpClient,
                config: $config,
            );
        });
    }

    private function bindMessagingApi() {
        $this->app->bind('line-bot-messaging-api', function ($app) {
            $httpClient = $app->make('line-bot-http-client');
            $config = new MessagingApiConfiguration();
            $config->setAccessToken(config('line-bot.channel_access_token'));
            return new MessagingApiApi(
                client: $httpClient,
                config: $config,
            );
        });
    }

    private function bindMessagingBlobApi() {
        $this->app->bind('line-bot-messaging-blob-api', function ($app) {
            $httpClient = $app->make('line-bot-http-client');
            $config = new MessagingApiConfiguration();
            $config->setAccessToken(config('line-bot.channel_access_token'));
            return new MessagingApiBlobApi(
                client: $httpClient,
                config: $config,
            );
        });
    }
}
