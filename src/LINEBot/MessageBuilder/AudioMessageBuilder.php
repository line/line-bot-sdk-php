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
 * A builder class for audio message.
 *
 * @package LINE\LINEBot\MessageBuilder
 */
class AudioMessageBuilder implements MessageBuilder
{
    /** @var string */
    private $originalContentUrl;
    /** @var int */
    private $duration;

    /**
     * AudioMessageBuilder constructor.
     *
     * @param string $originalContentUrl URL that serves audio file.
     * @param int $duration Duration of audio file (milli seconds)
     */
    public function __construct($originalContentUrl, $duration)
    {
        $this->originalContentUrl = $originalContentUrl;
        $this->duration = $duration;
    }

    /**
     * Builds
     * @return array
     */
    public function buildMessage()
    {
        return [
            [
                'type' => MessageType::AUDIO,
                'originalContentUrl' => $this->originalContentUrl,
                'duration' => $this->duration,
            ]
        ];
    }
}
