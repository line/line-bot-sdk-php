<?php
/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\GuzzleHTTPClient;

$container = $app->getContainer();

$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

$container['bot'] = function ($c) {
    $settings = $c->get('settings')['bot'];
    $config = [
        'channelId' => $settings['channelId'],
        'channelSecret' => $settings['channelSecret'],
        'channelMid' => $settings['channelMid'],
    ];
    $bot = new LINEBot($config, new GuzzleHTTPClient($config));
    return $bot;
};
