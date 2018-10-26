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

namespace LINE\LINEBot\KitchenSink\EventHandler;

use LINE\LINEBot;
use LINE\LINEBot\Event\BeaconDetectionEvent;
use LINE\LINEBot\KitchenSink\EventHandler;

class BeaconEventHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /* @var BeaconDetectionEvent $beaconEvent */
    private $beaconEvent;

    /**
     * BeaconEventHandler constructor.
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param BeaconDetectionEvent $beaconEvent
     */
    public function __construct($bot, $logger, BeaconDetectionEvent $beaconEvent)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->beaconEvent = $beaconEvent;
    }

    /**
     * @throws \ReflectionException
     */
    public function handle()
    {
        $this->bot->replyText(
            $this->beaconEvent->getReplyToken(),
            'Got beacon message ' . $this->beaconEvent->getHwid()
        );
    }
}
