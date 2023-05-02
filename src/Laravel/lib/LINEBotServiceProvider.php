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

use GuzzleHttp\Client as GuzzleHttpClient;

class LINEBotServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private static $apiBindings = [
        'line-bot-channel-access-token-api' => [
            'config' => \LINE\Clients\ChannelAccessToken\Configuration::class,
            'api' => \LINE\Clients\ChannelAccessToken\Api\ChannelAccessTokenApi::class,
        ],
        'line-bot-insight-api' => [
            'config' => \LINE\Clients\Insight\Configuration::class,
            'api' => \LINE\Clients\Insight\Api\InsightApi::class,
        ],
        'line-bot-liff-api' => [
            'config' => \LINE\Clients\Liff\Configuration::class,
            'api' => \LINE\Clients\Liff\Api\LiffApi::class,
        ],
        'line-bot-manage-audience-api' => [
            'config' => \LINE\Clients\ManageAudience\Configuration::class,
            'api' => \LINE\Clients\ManageAudience\Api\ManageAudienceApi::class,
        ],
        'line-bot-manage-audience-blob-api' => [
            'config' => \LINE\Clients\ManageAudience\Configuration::class,
            'api' => \LINE\Clients\ManageAudience\Api\ManageAudienceBlobApi::class,
        ],
        'line-bot-messaging-api' => [
            'config' => \LINE\Clients\MessagingApi\Configuration::class,
            'api' => \LINE\Clients\MessagingApi\Api\MessagingApiApi::class,
        ],
        'line-bot-messaging-blob-api' => [
            'config' => \LINE\Clients\MessagingApi\Configuration::class,
            'api' => \LINE\Clients\MessagingApi\Api\MessagingApiBlobApi::class,
        ],
    ];

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
            return new GuzzleHttpClient(
                config: config('line-bot.client.config'),
            );
        });
        foreach (self::$apiBindings as $facadeName => $classes) {
            $this->bindApis($facadeName, $classes['api'], $classes['config']);
        }
    }

    private function bindApis($facadeName, $clientClass, $configClass)
    {
        $this->app->bind($facadeName, function ($app) use ($clientClass, $configClass) {
            $httpClient = $app->make('line-bot-http-client');
            $config = new $configClass();
            $config->setAccessToken(config('line-bot.channel_access_token'));
            return new $clientClass(
                client: $httpClient,
                config: $config,
            );
        });
    }
}
