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

namespace LINE\LINEBot\Event;

/**
 * A base class of message event.
 *
 * Don't instantiate this class individually.
 *
 * @package LINE\LINEBot\Event
 */
class MessageEvent extends BaseEvent
{
    /** @var array */
    protected $message;

    /**
     * MessageEvent constructor.
     *
     * @param array $event
     */
    public function __construct($event)
    {
        parent::__construct($event);

        $this->message = $event['message'];
    }

    /**
     * Returns the identifier of the message.
     *
     * @return string
     */
    public function getMessageId()
    {
        return $this->message['id'];
    }

    /**
     * Returns the type of the message.
     *
     * @return string
     */
    public function getMessageType()
    {
        return $this->message['type'];
    }
}
