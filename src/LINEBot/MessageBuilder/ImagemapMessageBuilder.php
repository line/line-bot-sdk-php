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
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\VideoBuilder;
use LINE\LINEBot\QuickReplyBuilder;
use LINE\LINEBot\SenderBuilder\SenderBuilder;

/**
 * A builder class for imagemap message.
 *
 * @package LINE\LINEBot\MessageBuilder
 */
class ImagemapMessageBuilder implements MessageBuilder
{
    /** @var string */
    private $baseUrl;

    /** @var string */
    private $altText;

    /** @var BaseSizeBuilder */
    private $baseSizeBuilder;

    /** @var VideoBuilder|null */
    private $videoBuilder;

    /** @var ImagemapActionBuilder[] */
    private $imagemapActionBuilders;

    /** @var array */
    private $message = [];

    /** @var QuickReplyBuilder|null */
    private $quickReply;

    /** @var SenderBuilder|null */
    private $sender;

    /**
     * ImagemapMessageBuilder constructor.
     *
     * @param string $baseUrl
     * @param string $altText
     * @param BaseSizeBuilder $baseSizeBuilder
     * @param ImagemapActionBuilder[] $imagemapActionBuilders
     * @param QuickReplyBuilder|null $quickReply
     * @param VideoBuilder|null $videoBuilder
     * @param SenderBuilder|null $sender
     */
    public function __construct(
        $baseUrl,
        $altText,
        $baseSizeBuilder,
        array $imagemapActionBuilders,
        QuickReplyBuilder $quickReply = null,
        VideoBuilder $videoBuilder = null,
        SenderBuilder $sender = null
    ) {
        $this->baseUrl = $baseUrl;
        $this->altText = $altText;
        $this->baseSizeBuilder = $baseSizeBuilder;
        $this->imagemapActionBuilders = $imagemapActionBuilders;
        $this->quickReply = $quickReply;
        $this->videoBuilder = $videoBuilder;
        $this->sender = $sender;
    }

    /**
     * Builds imagemap message strucutre.
     *
     * @return array
     */
    public function buildMessage()
    {
        if (!empty($this->message)) {
            return $this->message;
        }

        $actions = [];
        foreach ($this->imagemapActionBuilders as $builder) {
            $actions[] = $builder->buildImagemapAction();
        }

        $imagemapMessage = [
            'type' => MessageType::IMAGEMAP,
            'baseUrl' => $this->baseUrl,
            'altText' => $this->altText,
            'baseSize' => $this->baseSizeBuilder->build(),
            'actions' => $actions,
        ];

        if ($this->quickReply) {
            $imagemapMessage['quickReply'] = $this->quickReply->buildQuickReply();
        }

        if ($this->videoBuilder) {
            $imagemapMessage['video'] = $this->videoBuilder->build();
        }

        if ($this->sender) {
            $imagemapMessage['sender'] = $this->sender->buildSender();
        }

        $this->message[] = $imagemapMessage;

        return $this->message;
    }
}
