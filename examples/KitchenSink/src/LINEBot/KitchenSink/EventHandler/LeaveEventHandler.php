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
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\KitchenSink\EventHandler;

class LeaveEventHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var LeaveEvent $leaveEvent */
    private $leaveEvent;

    /**
     * LeaveEventHandler constructor.
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param LeaveEvent $leaveEvent
     */
    public function __construct($bot, $logger, LeaveEvent $leaveEvent)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->leaveEvent = $leaveEvent;
    }

    public function handle()
    {
        if ($this->leaveEvent->isGroupEvent()) {
            $id = $this->leaveEvent->getGroupId();
        } elseif ($this->leaveEvent->isRoomEvent()) {
            $id = $this->leaveEvent->getRoomId();
        } else {
            $this->logger->error("Unknown event type");
            return;
        }

        $this->logger->info(sprintf('Leaved %s %s', $this->leaveEvent->getType(), $id));
    }
}
