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

use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\StickerMessage;
use LINE\Constants\MessageType;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\Webhook\Model\MessageEvent;
use LINE\Webhook\Model\StickerMessageContent;

class StickerMessageHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Psr\Log\LoggerInterface $logger */
    private $logger;
    /** @var StickerMessageContent $stickerMessage */
    private $stickerMessage;
    /** @var MessageEvent $event */
    private $event;

    /**
     * StickerMessageHandler constructor.
     * @param MessagingApiApi $bot
     * @param \Psr\Log\LoggerInterface $logger
     * @param MessageEvent $event
     */
    public function __construct(MessagingApiApi $bot, \Psr\Log\LoggerInterface $logger, MessageEvent $event)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->event = $event;
        $this->stickerMessage = $event->getMessage();
    }

    public function handle()
    {
        $replyToken = $this->event->getReplyToken();
        $message = new StickerMessage([
            'type' => MessageType::STICKER,
            'packageId' => $this->stickerMessage->getPackageId(),
            'stickerId' => $this->stickerMessage->getStickerId(),
        ]);
        $request = new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => [$message],
        ]);
        $this->bot->replyMessage($request);
    }
}
