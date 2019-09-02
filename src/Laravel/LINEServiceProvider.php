<?php

namespace LINE\Laravel;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LINEServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/line-bot.php' => config_path('line-bot.php'),
        ]);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('linebot', function () {
            $httpClient = new CurlHTTPClient(config('line-bot.channel_access_token'));
            return new LINEBot($httpClient, ['channelSecret' => config('line-bot.channel_secret')]);
        });
    }
}
