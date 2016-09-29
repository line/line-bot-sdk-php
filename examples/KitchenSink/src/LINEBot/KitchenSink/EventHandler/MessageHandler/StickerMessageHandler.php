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
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;

class StickerMessageHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var StickerMessage $stickerMessage */
    private $stickerMessage;

    /**
     * StickerMessageHandler constructor.
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param StickerMessage $stickerMessage
     */
    public function __construct($bot, $logger, StickerMessage $stickerMessage)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->stickerMessage = $stickerMessage;
    }

    public function handle()
    {
        $replyToken = $this->stickerMessage->getReplyToken();
        $packageId = $this->stickerMessage->getPackageId();
        $stickerId = $this->stickerMessage->getStickerId();
        $this->bot->replyMessage($replyToken, new StickerMessageBuilder($packageId, $stickerId));
    }
}
