<?php

/**
 * Copyright 2020 LINE Corporation
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

namespace LINE\LINEBot\MessageBuilder\Text;

class EmojiBuilder
{
    /** @var int */
    private $index;

    /** @var string */
    private $productId;

    /** @var string */
    private $emojiId;

    /** @var array */
    private $emoji;

    /**
     * EmojiBuilder constructor.
     *
     * @param array $emojis The specified position must correspond to a $.
     * @param string $productId Product ID for a set of LINE emoji.
     * @param string $emojiId ID for a LINE emoji inside a set.
     */
    public function __construct($index, $productId, $emojiId)
    {
        $this->index = $index;
        $this->productId = $productId;
        $this->emojiId = $emojiId;
    }

    /**
     * Build emoji.
     *
     * @return array
     */
    public function build()
    {
        if (!empty($this->emoji)) {
            return $this->emoji;
        }

        $emoji = [
            'index' => $this->index,
            'productId' => $this->productId,
            'emojiId' => $this->emojiId,
        ];

        $this->emoji = $emoji;

        return $this->emoji;
    }
}
