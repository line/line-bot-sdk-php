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
use LINE\LINEBot\MessageBuilder\Text\EmojiTextBuilder;

/**
 * A builder class for text message.
 *
 * @package LINE\LINEBot\MessageBuilder
 */
class TextMessageBuilder implements MessageBuilder
{
    /** @var mixed[] */
    private $texts;

    /** @var array */
    private $message = [];

    /** @var QuickReplyBuilder|null */
    private $quickReply;

    /** @var SenderBuilder|null */
    private $sender;

    /**
     * TextMessageBuilder constructor.
     *
     * Exact signature of this constructor is <code>new TextMessageBuilder(string $text, string[] $extraTexts)</code>.
     *
     * Means, this constructor can also receive multiple messages like so;
     *
     * <code>
     * $textBuilder = new TextMessageBuilder('text', 'extra text1', 'extra text2', ...);
     * </code>
     *
     * @param string $text
     * @param string[]|null $extraTexts
     */
    public function __construct($text, $extraTexts = null)
    {
        $extras = [];
        if (!is_null($extraTexts)) {
            $args = func_get_args();
            $extras = array_slice($args, 1);

            foreach ($extras as $key => $extra) {
                if ($extra instanceof QuickReplyBuilder) {
                    $this->quickReply = $extra;
                    unset($extras[$key]);
                } elseif ($extra instanceof SenderBuilder) {
                    $this->sender = $extra;
                    unset($extras[$key]);
                }
            }
            $extras = array_values($extras);
        }
        $this->texts = array_merge([$text], $extras);
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
            if ($text instanceof EmojiTextBuilder) {
                $this->message[] = $text->build();
                continue;
            }
            $this->message[] = [
                'type' => MessageType::TEXT,
                'text' => $text,
            ];
        }

        if ($this->quickReply) {
            $lastKey = count($this->message) - 1;

            // If the user receives multiple message objects.
            // The quickReply property of the last message object is displayed.
            $this->message[$lastKey]['quickReply'] = $this->quickReply->buildQuickReply();
        }

        if ($this->sender) {
            foreach ($this->message as $messageKey => $message) {
                $this->message[$messageKey]['sender'] = $this->sender->buildSender();
            }
        }

        return $this->message;
    }
}
