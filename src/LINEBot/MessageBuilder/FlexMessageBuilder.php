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

namespace LINE\LINEBot\MessageBuilder;

use LINE\LINEBot\Constant\MessageType;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\QuickReplyBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for flex message.
 *
 * @package LINE\LINEBot\MessageBuilder
 */
class FlexMessageBuilder implements MessageBuilder
{
    /** @var string */
    private $altText;
    /** @var ContainerBuilder */
    private $containerBuilder;

    /** @var QuickReplyBuilder|null */
    private $quickReply;

    /** @var array */
    private $message;

    /**
     * FlexMessageBuilder constructor.
     *
     * @param string $altText
     * @param ContainerBuilder $containerBuilder
     * @param QuickReplyBuilder|null $quickReply
     */
    public function __construct($altText, $containerBuilder, QuickReplyBuilder $quickReply = null)
    {
        $this->altText = $altText;
        $this->containerBuilder = $containerBuilder;
        $this->quickReply = $quickReply;
    }

    /**
     * Create empty FlexMessageBuilder.
     *
     * @return FlexMessageBuilder
     */
    public static function builder()
    {
        return new self(null, null);
    }

    /**
     * Set altText.
     *
     * @param string $altText
     * @return FlexMessageBuilder
     */
    public function setAltText($altText)
    {
        $this->altText = $altText;
        return $this;
    }

    /**
     * Set contents.
     *
     * @param ContainerBuilder $containerBuilder
     * @return FlexMessageBuilder
     */
    public function setContents($containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
        return $this;
    }

    /**
     * Set quickReply.
     *
     * @param QuickReplyBuilder|null $quickReply
     * @return FlexMessageBuilder
     */
    public function setQuickReply(QuickReplyBuilder $quickReply = null)
    {
        $this->quickReply = $quickReply;
        return $this;
    }

    /**
     * Builds flex message structure.
     *
     * @return array
     */
    public function buildMessage()
    {
        if (isset($this->message)) {
            return $this->message;
        }

        $this->message = [
            BuildUtil::removeNullElements([
                'type' => MessageType::FLEX,
                'altText' => $this->altText,
                'contents' => $this->containerBuilder->build(),
                'quickReply' => BuildUtil::build($this->quickReply, 'buildQuickReply'),
            ])
        ];

        return $this->message;
    }
}
