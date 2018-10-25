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
 * A class that represents the message event of location.
 *
 * @package LINE\LINEBot\Event\MessageEvent
 */
class LocationMessage extends MessageEvent
{
    /**
     * LocationMessage constructor.
     *
     * @param array $event
     */
    public function __construct($event)
    {
        parent::__construct($event);
    }

    /**
     * Returns title of the location message.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return array_key_exists('title', $this->message) ? $this->message['title'] : null;
    }

    /**
     * Returns address of the location message.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return array_key_exists('address', $this->message) ? $this->message['address'] : null;
    }

    /**
     * Returns latitude of the location message.
     *
     * @return double
     */
    public function getLatitude()
    {
        return $this->message['latitude'];
    }

    /**
     * Returns longitude of the location message.
     *
     * @return double
     */
    public function getLongitude()
    {
        return $this->message['longitude'];
    }
}
