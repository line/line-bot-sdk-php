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

namespace LINE\LINEBot\MessageBuilder;

use LINE\LINEBot\Constant\MessageType;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\QuickReplyBuilder;
use LINE\LINEBot\SenderBuilder\SenderBuilder;

/**
 * A builder class for video message.
 *
 * @package LINE\LINEBot\MessageBuilder
 */
class VideoMessageBuilder implements MessageBuilder
{
    /** @var string */
    private $originalContentUrl;

    /** @var string */
    private $previewImageUrl;

    /** @var QuickReplyBuilder|null */
    private $quickReply;

    /** @var SenderBuilder|null */
    private $sender;

    /** @var array */
    private $message = [];

    /**
     * VideoMessageBuilder constructor.
     *
     * @param string $originalContentUrl
     * @param string $previewImageUrl
     * @param QuickReplyBuilder|null $quickReply
     * @param SenderBuilder|null $sender
     */
    public function __construct(
        $originalContentUrl,
        $previewImageUrl,
        QuickReplyBuilder $quickReply = null,
        SenderBuilder $sender = null
    ) {
        $this->originalContentUrl = $originalContentUrl;
        $this->previewImageUrl = $previewImageUrl;
        $this->quickReply = $quickReply;
        $this->sender = $sender;
    }

    /**
     * Builds video message structure.
     *
     * @return array
     */
    public function buildMessage()
    {
        if (!empty($this->message)) {
            return $this->message;
        }

        $video = [
            'type' => MessageType::VIDEO,
            'originalContentUrl' => $this->originalContentUrl,
            'previewImageUrl' => $this->previewImageUrl,
        ];

        if ($this->quickReply) {
            $video['quickReply'] = $this->quickReply->buildQuickReply();
        }

        if ($this->sender) {
            $video['sender'] = $this->sender->buildSender();
        }

        $this->message[] = $video;

        return $this->message;
    }
}
