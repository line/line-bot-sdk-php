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

namespace LINE\LINEBot\Event\MessageEvent;

use LINE\LINEBot\Event\MessageEvent;

/**
 * A class that represents the message event of audio.
 *
 * @package LINE\LINEBot\Event\MessageEvent
 */
class AudioMessage extends MessageEvent
{
    /** @var ContentProvider */
    private $contentProvider;

    /**
     * AudioMessage constructor.
     *
     * @param array $event
     */
    public function __construct($event)
    {
        parent::__construct($event);
        $this->contentProvider = new ContentProvider($this->message['contentProvider']);
    }

    /**
     * Returns duration of the audio message.
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->message['duration'];
    }

    /**
     * Returns contentProvider of the audio message.
     *
     * @return ContentProvider
     */
    public function getContentProvider()
    {
        return $this->contentProvider;
    }
}
