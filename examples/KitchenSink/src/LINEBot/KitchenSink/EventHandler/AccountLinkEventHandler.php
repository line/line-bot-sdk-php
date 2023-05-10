<?php

/**
 * Copyright 2018 LINE Corporation
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
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Constants\MessageType;
use LINE\Webhook\Model\AccountLinkEvent;

class AccountLinkEventHandler
{
    /** @var MessagingApiApi $bot */
    private $bot;
    /** @var \Psr\Log\LoggerInterface $logger */
    private $logger;
    /* @var AccountLinkEvent $accountLinkEvent */
    private $accountLinkEvent;

    /**
     * AccountLinkEventHandler constructor.
     * @param MessagingApiApi $bot
     * @param AccountLinkEvent $accountLinkEvent
     */
    public function __construct(MessagingApiApi $bot, \Psr\Log\LoggerInterface $logger, AccountLinkEvent $accountLinkEvent)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->accountLinkEvent = $accountLinkEvent;
    }

    /**
     * @throws \ReflectionException
     */
    public function handle()
    {
        $link = $this->accountLinkEvent->getLink();
        $this->logger->info('Link result: ' . $link->getResult());
        $request = new ReplyMessageRequest([
            'replyToken' => $this->accountLinkEvent->getReplyToken(),
            'messages' => [
                new TextMessage([
                    'type' => MessageType::TEXT,
                    'text' => 'Got account link event ' . $link->getNonce(),
                ]),
            ],
        ]);
        $this->bot->replyMessage($request);
    }
}
