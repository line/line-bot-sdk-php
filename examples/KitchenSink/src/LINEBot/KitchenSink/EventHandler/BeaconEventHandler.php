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
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Constants\MessageType;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\Webhook\Model\BeaconEvent;

class BeaconEventHandler implements EventHandler
{
    /** @var MessagingApiApi $bot */
    private $bot;
    /** @var \Psr\Log\LoggerInterface $logger */
    private $logger;
    /** @var MessageEvent $event */
    private $beaconEvent;

    /**
     * BeaconEventHandler constructor.
     * @param MessagingApiApi $bot
     * @param \Psr\Log\LoggerInterface $logger
     * @param BeaconEvent $beaconEvent
     */
    public function __construct(MessagingApiApi $bot, \Psr\Log\LoggerInterface $logger, BeaconEvent $beaconEvent)
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
        $request = new ReplyMessageRequest([
            'replyToken' => $this->beaconEvent->getReplyToken(),
            'messages' => [
                new TextMessage(['type' => MessageType::TEXT, 'text' => 'Got beacon message ' . $this->beaconEvent->getHwid()]),
            ],
        ]);
        $this->bot->replyMessage($request);
    }
}
