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
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\Util\UrlBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;

class VideoMessageHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var \Slim\Http\Request $logger */
    private $req;
    /** @var VideoMessage $videoMessage */
    private $videoMessage;

    /**
     * VideoMessageHandler constructor.
     *
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param \Slim\Http\Request $req
     * @param VideoMessage $videoMessage
     */
    public function __construct($bot, $logger, \Slim\Http\Request $req, VideoMessage $videoMessage)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->req = $req;
        $this->videoMessage = $videoMessage;
    }

    public function handle()
    {
        $replyToken = $this->videoMessage->getReplyToken();

        $contentProvider = $this->videoMessage->getContentProvider();
        if ($contentProvider->isExternal()) {
            $this->bot->replyMessage(
                $replyToken,
                new VideoMessageBuilder(
                    $contentProvider->getOriginalContentUrl(),
                    $contentProvider->getPreviewImageUrl()
                )
            );
            return;
        }

        $contentId = $this->videoMessage->getMessageId();
        $video = $this->bot->getMessageContent($contentId)->getRawBody();

        $tempFilePath = tempnam($_SERVER['DOCUMENT_ROOT'] . '/static/tmpdir', 'video-');
        unlink($tempFilePath);
        $filePath = $tempFilePath . '.mp4';
        $filename = basename($filePath);

        $fh = fopen($filePath, 'x');
        fwrite($fh, $video);
        fclose($fh);

        $url = UrlBuilder::buildUrl($this->req, ['static', 'tmpdir', $filename]);

        // NOTE: You should pass the url of thumbnail image to `previewImageUrl`.
        // This sample doesn't treat that so this sample cannot show the thumbnail.
        $this->bot->replyMessage($replyToken, new VideoMessageBuilder($url, $url));
    }
}
