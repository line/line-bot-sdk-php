<?php

/**
 * Copyright 2016 LINE Corporation
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

namespace LINE\LINEBot\KitchenSink;

use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;
use LINE\Clients\MessagingApi\Configuration;

class Dependency
{
    public function register(\DI\Container $container)
    {
        $container->set('settings', function ($c) {
            return Setting::getSetting()['settings'];
        });

        $container->set(\Psr\Log\LoggerInterface::class, function ($c) {
            $settings = $c->get('settings')['logger'];
            $logger = new \Monolog\Logger($settings['name']);
            $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Level::Debug));
            $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], \Monolog\Level::Debug));
            return $logger;
        });

        $container->set(MessagingApiApi::class, function ($c) {
            $settings = $c->get('settings');
            $channelToken = $settings['bot']['channelToken'];
            $config = new Configuration();
            $config->setAccessToken($channelToken);
            $bot = new MessagingApiApi(
                client: new \GuzzleHttp\Client(),
                config: $config,
            );
            return $bot;
        });

        $container->set(MessagingApiBlobApi::class, function ($c) {
            $settings = $c->get('settings');
            $channelToken = $settings['bot']['channelToken'];
            $config = new Configuration();
            $config->setAccessToken($channelToken);
            $bot = new MessagingApiBlobApi(
                client: new \GuzzleHttp\Client(),
                config: $config,
            );
            return $bot;
        });
    }
}
