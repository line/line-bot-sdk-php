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

namespace LINE\LINEBot\KitchenSink\EventHandler\MessageHandler;

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;

class LocationMessageHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var LocationMessage $event */
    private $locationMessage;

    /**
     * LocationMessageHandler constructor.
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param LocationMessage $locationMessage
     */
    public function __construct($bot, $logger, LocationMessage $locationMessage)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->locationMessage = $locationMessage;
    }

    public function handle()
    {
        $replyToken = $this->locationMessage->getReplyToken();
        $title = $this->locationMessage->getTitle();
        $address = $this->locationMessage->getAddress();
        $latitude = $this->locationMessage->getLatitude();
        $longitude = $this->locationMessage->getLongitude();

        $this->bot->replyMessage(
            $replyToken,
            new LocationMessageBuilder($title, $address, $latitude, $longitude)
        );
    }
}
