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
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\VideoMessage;
use LINE\Constants\MessageContentProviderType;
use LINE\Constants\MessageType;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\Util\UrlBuilder;
use LINE\Webhook\Model\MessageEvent;
use LINE\Webhook\Model\VideoMessageContent;

class VideoMessageHandler implements EventHandler
{
    /** @var MessagingApiApi $bot */
    private $bot;
    /** @var MessagingApiBlobApi $bot */
    private $botBlob;
    /** @var \Psr\Log\LoggerInterface $logger */
    private $logger;
    /** @var \Slim\Http\Request $logger */
    private $req;
    /** @var VideoMessageContent $videoMessage */
    private $videoMessage;
    /** @var MessageEvent $event */
    private $event;

    /**
     * VideoMessageHandler constructor.
     *
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
        $this->videoMessage = $event->getMessage();
    }

    public function handle()
    {
        $replyToken = $this->event->getReplyToken();

        $contentProvider = $this->videoMessage->getContentProvider();
        if ($contentProvider->getType() == MessageContentProviderType::EXTERNAL) {
            $this->replyVideoMessage(
                $replyToken,
                $contentProvider->getOriginalContentUrl(),
                $contentProvider->getPreviewImageUrl(),
            );
            return;
        }

        $contentId = $this->videoMessage->getId();
        $sfo = $this->botBlob->getMessageContent($contentId);
        $video = $sfo->fread($sfo->getSize());

        $tempFilePath = tempnam($_SERVER['DOCUMENT_ROOT'] . '/static/tmpdir', 'video-');
        unlink($tempFilePath);
        $filePath = $tempFilePath . '.mp4';
        $filename = basename($filePath);

        $fh = fopen($filePath, 'x');
        fwrite($fh, $video);
        fclose($fh);

        $url = UrlBuilder::buildUrl($this->req, ['static', 'tmpdir', $filename]);
        $previewUrl = UrlBuilder::buildUrl($this->req, ['static', 'preview.jpg']);

        // NOTE: You should pass the url of thumbnail image to `previewImageUrl`.
        // This sample doesn't treat that so this sample cannot show the thumbnail.
        $this->replyVideoMessage($replyToken, $url, $previewUrl);
    }

    private function replyVideoMessage(string $replyToken, string $original, string $preview)
    {
        $message = new VideoMessage([
            'type' => MessageType::VIDEO,
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
