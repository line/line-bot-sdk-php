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

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

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
            return new CurlHTTPClient(config('line-bot.channel_access_token'));
        });
        $this->app->bind('line-bot', function ($app) {
            $httpClient = $app->make('line-bot-http-client');
            return new LINEBot($httpClient, ['channelSecret' => config('line-bot.channel_secret')]);
        });
    }
}
