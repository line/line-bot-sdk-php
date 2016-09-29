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
    /** @var ImagemapActionBuilder[] */
    private $imagemapActionBuilders;

    /** @var array */
    private $message = [];

    /**
     * ImagemapMessageBuilder constructor.
     *
     * @param string $baseUrl
     * @param string $altText
     * @param BaseSizeBuilder $baseSizeBuilder
     * @param ImagemapActionBuilder[] $imagemapActionBuilders
     */
    public function __construct($baseUrl, $altText, $baseSizeBuilder, array $imagemapActionBuilders)
    {
        $this->baseUrl = $baseUrl;
        $this->altText = $altText;
        $this->baseSizeBuilder = $baseSizeBuilder;
        $this->imagemapActionBuilders = $imagemapActionBuilders;
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

        $this->message[] = [
            'type' => MessageType::IMAGEMAP,
            'baseUrl' => $this->baseUrl,
            'altText' => $this->altText,
            'baseSize' => $this->baseSizeBuilder->build(),
            'actions' => $actions,
        ];

        return $this->message;
    }
}
