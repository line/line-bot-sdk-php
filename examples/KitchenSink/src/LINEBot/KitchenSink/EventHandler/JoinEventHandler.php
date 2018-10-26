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
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\KitchenSink\EventHandler;

class JoinEventHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var JoinEvent $joinEvent */
    private $joinEvent;

    /**
     * JoinEventHandler constructor.
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param JoinEvent $joinEvent
     */
    public function __construct($bot, $logger, JoinEvent $joinEvent)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->joinEvent = $joinEvent;
    }

    /**
     * @throws LINEBot\Exception\InvalidEventSourceException
     * @throws \ReflectionException
     */
    public function handle()
    {
        if ($this->joinEvent->isGroupEvent()) {
            $id = $this->joinEvent->getGroupId();
        } elseif ($this->joinEvent->isRoomEvent()) {
            $id = $this->joinEvent->getRoomId();
        } else {
            $this->logger->error("Unknown event type");
            return;
        }

        $this->bot->replyText(
            $this->joinEvent->getReplyToken(),
            sprintf('Joined %s %s', $this->joinEvent->getType(), $id)
        );
    }
}
