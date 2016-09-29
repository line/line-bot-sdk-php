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

/**
 * A builder class for text message.
 *
 * @package LINE\LINEBot\MessageBuilder
 */
class TextMessageBuilder implements MessageBuilder
{
    /** @var string[] */
    private $texts;
    /** @var array */
    private $message = [];

    /**
     * TextMessageBuilder constructor.
     *
     * @param string $text
     * @param string[] $extraTexts
     */
    public function __construct($text, ...$extraTexts)
    {
        $this->texts = array_merge([$text], $extraTexts);
    }

    /**
     * Builds text message structure.
     *
     * @return array
     */
    public function buildMessage()
    {
        if (!empty($this->message)) {
            return $this->message;
        }

        foreach ($this->texts as $text) {
            $this->message[] = [
                'type' => MessageType::TEXT,
                'text' => $text,
            ];
        }

        return $this->message;
    }
}
