<?php

namespace LINE\Laravel;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LINEServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/line-bot.php', 'line-bot'
        );
        $this->app->bind('linebot', function () {
            $httpClient = new CurlHTTPClient(config('line-bot.channel_access_token'));
            return new LINEBot($httpClient, ['channelSecret' => config('line-bot.channel_secret')]);
        });
    }
}
