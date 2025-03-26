<?php

/**
 * Copyright 2025 LINE Corporation
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
use LINE\Webhook\Model\GroupSource;
use LINE\Webhook\Model\MemberJoinedEvent;
use LINE\Webhook\Model\RoomSource;

class MemberJoinedEventHandler implements EventHandler
{
    /** @var MessagingApiApi $bot */
    private $bot;
    /** @var \Psr\Log\LoggerInterface $logger */
    private $logger;
    /** @var MemberJoinedEvent $memberJoinedEvent */
    private $memberJoinedEvent;

    /**
     * MemberJoinedEventHandler constructor.
     * @param MessagingApiApi $bot
     * @param \Psr\Log\LoggerInterface $logger
     * @param MemberJoinedEvent $memberJoinedEvent
     */
    public function __construct(MessagingApiApi $bot, \Psr\Log\LoggerInterface $logger, MemberJoinedEvent $memberJoinedEvent)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->memberJoinedEvent = $memberJoinedEvent;
    }

    /**
     * @throws LINEBot\Exception\InvalidEventSourceException
     * @throws \ReflectionException|\LINE\Clients\MessagingApi\ApiException
     */
    public function handle()
    {
        $source = $this->memberJoinedEvent->getSource();
        if ($source instanceof GroupSource) {
            $id = $source->getGroupId();
        } elseif ($source instanceof RoomSource) {
            $id = $source->getRoomId();
        } else {
            $this->logger->error("Unknown event type");
            return;
        }

        $joinedMembers = $this->memberJoinedEvent->getJoined()->getMembers();
        $joinedMemberIds = array_map(function ($member) {
            return $member->getUserId();
        }, $joinedMembers);

        $request = new ReplyMessageRequest([
            'replyToken' => $this->memberJoinedEvent->getReplyToken(),
            'messages' => [
                new TextMessage([
                    'type' => MessageType::TEXT,
                    'text' => sprintf('%s joined. %s %s', implode(", ", $joinedMemberIds), $this->memberJoinedEvent->getType(), $id),
                ]),
            ],
        ]);
        $this->bot->replyMessage($request);
    }
}
