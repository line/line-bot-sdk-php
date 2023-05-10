<?php

/**
 * Copyright 2019 LINE Corporation
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
use LINE\Constants\ThingsEventType;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\Webhook\Model\ThingsEvent;

class ThingsEventHandler implements EventHandler
{
    /** @var MessagingApiApi $bot */
    private $bot;
    /** @var \Psr\Log\LoggerInterface $logger */
    private $logger;
    /** @var ThingsEvent $thingsEvent */
    private $thingsEvent;

    /**
     * ThingsEventHandler constructor.
     *
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param ThingsEvent $thingsEvent
     */
    public function __construct(MessagingApiApi $bot, \Psr\Log\LoggerInterface $logger, ThingsEvent $thingsEvent)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->thingsEvent = $thingsEvent;
    }

    /**
     * @throws \ReflectionException
     */
    public function handle()
    {
        $text = 'Device ' . $this->thingsEvent->getThings()->getDeviceId();
        switch ($this->thingsEvent->getThingsEventType()) {
            case ThingsEventType::DEVICE_LINKED:
                $text .= ' was linked!';
                break;
            case ThingsEventType::DEVICE_UNLINKED:
                $text .= ' was unlinked!';
                break;
            case ThingsEventType::SCENARIO_RESULT:
                $result = $this->thingsEvent->getThings();
                $text .= ' executed scenario:' . $result->getScenarioId();
                break;
        }

        $request = new ReplyMessageRequest([
            'replyToken' => $this->thingsEvent->getReplyToken(),
            'messages' => [
                new TextMessage(['type' => MessageType::TEXT, 'text' => $text]),
            ],
        ]);
        $this->bot->replyMessage($request);
    }
}
