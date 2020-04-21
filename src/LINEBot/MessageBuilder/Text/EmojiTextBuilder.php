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

use LINE\LINEBot\Constant\MessageType;

/**
 * A builder class for emoji text.
 *
 * @package LINE\LINEBot\MessageBuilder\Text
 */
class EmojiTextBuilder
{
    /** @var string */
    private $text;

    /** @var EmojiBuilder[] */
    private $emojis;

    /** @var array */
    private $emojiText;

    /**
     * EmojiTextBuilder constructor.
     *
     * @param string $text Message text.
     * @param array $emojis One or more LINE emoji. Max: 20 LINE emoji.
     */
    public function __construct($text, $emojis)
    {
        $this->text = $text;
        $args = func_get_args();
        $this->emojis = array_slice($args, 1);
    }

    /**
     * Build text.
     *
     * @return array
     */
    public function build()
    {
        if (!empty($this->emojiText)) {
            return $this->emojiText;
        }

        $emojiText = [
            'type' => MessageType::TEXT,
            'text' => $this->text,
            'emojis' => array_map(function ($emoji) {
                return $emoji->build();
            }, $this->emojis),
        ];

        $this->emojiText = $emojiText;

        return $this->emojiText;
    }
}
