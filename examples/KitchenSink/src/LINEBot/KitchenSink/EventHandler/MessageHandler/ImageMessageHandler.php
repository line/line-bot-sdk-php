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
use LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;
use LINE\Clients\MessagingApi\Model\ImageMessage;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Constants\MessageContentProviderType;
use LINE\Constants\MessageType;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\Util\UrlBuilder;
use LINE\Webhook\Model\ImageMessageContent;
use LINE\Webhook\Model\MessageEvent;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImageMessageHandler implements EventHandler
{
    /** @var = $bot */
    private $bot;
    /** @var MessagingApiBlobApi $bot */
    private $botBlob;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var \Slim\Http\Request $logger */
    private $req;
    /** @var ImageMessageContent $imageMessage */
    private $imageMessage;
    /** @var MessageEvent $event */
    private $event;

    /**
     * ImageMessageHandler constructor.
     * @param MessagingApiApi $bot
     * @param MessagingApiBlobApi $botBlob
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Psr\Http\Message\RequestInterface $req
     * @param MessageEvent $event
     */
    public function __construct(MessagingApiApi $bot, MessagingApiBlobApi $botBlob, \Psr\Log\LoggerInterface $logger, \Psr\Http\Message\RequestInterface $req, MessageEvent $event)
    {
        $this->bot = $bot;
        $this->botBlob = $botBlob;
        $this->logger = $logger;
        $this->req = $req;
        $this->event = $event;
        $this->imageMessage = $event->getMessage();
    }

    public function handle()
    {
        $replyToken = $this->event->getReplyToken();

        $contentProvider = $this->imageMessage->getContentProvider();
        if ($contentProvider->getType() == MessageContentProviderType::EXTERNAL) {
            $this->replyImageMessage(
                $replyToken,
                $contentProvider->getOriginalContentUrl(),
                $contentProvider->getPreviewImageUrl(),
            );
            return;
        }

        $contentId = $this->imageMessage->getId();
        $sfo = $this->botBlob->getMessageContent($contentId);
        $image = $sfo->fread($sfo->getSize());

        $tempFilePath = tempnam($_SERVER['DOCUMENT_ROOT'] . '/static/tmpdir', 'image-');
        unlink($tempFilePath);
        $filePath = $tempFilePath . '.jpg';
        $filename = basename($filePath);

        $fh = fopen($filePath, 'x');
        fwrite($fh, $image);
        fclose($fh);

        $url = UrlBuilder::buildUrl($this->req, ['static', 'tmpdir', $filename]);

        // NOTE: You should pass the url of small image to `previewImageUrl`.
        // This sample doesn't treat that.
        $this->replyImageMessage($replyToken, $url, $url);
    }

    private function replyImageMessage(string $replyToken, string $original, string $preview)
    {
        $message = new ImageMessage([
            'type' => MessageType::IMAGE,
            'originalContentUrl' => $original,
            'previewImageUrl' => $preview,
        ]);
        $request = new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => [$message],
        ]);
        $this->bot->replyMessage($request);
    }
}
