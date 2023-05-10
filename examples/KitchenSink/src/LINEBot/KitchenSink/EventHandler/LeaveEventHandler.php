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

use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\Webhook\Model\GroupSource;
use LINE\Webhook\Model\LeaveEvent;
use LINE\Webhook\Model\RoomSource;

class LeaveEventHandler implements EventHandler
{
    /** @var MessagingApiApi $bot */
    private $bot;
    /** @var \Psr\Log\LoggerInterface $logger */
    private $logger;
    /** @var LeaveEvent $leaveEvent */
    private $leaveEvent;

    /**
     * LeaveEventHandler constructor.
     * @param MessagingApiApi $bot
     * @param \Psr\Log\LoggerInterface $logger
     * @param LeaveEvent $leaveEvent
     */
    public function __construct(MessagingApiApi $bot, \Psr\Log\LoggerInterface $logger, LeaveEvent $leaveEvent)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->leaveEvent = $leaveEvent;
    }

    public function handle()
    {
        $source = $this->leaveEvent->getSource();
        if ($source instanceof GroupSource) {
            $id = $source->getGroupId();
        } elseif ($source instanceof RoomSource) {
            $id = $source->getRoomId();
        } else {
            $this->logger->error("Unknown event type");
            return;
        }

        $this->logger->info(sprintf('Left %s %s', $this->leaveEvent->getType(), $id));
    }
}
