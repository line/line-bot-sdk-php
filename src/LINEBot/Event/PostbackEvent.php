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
 * A class that represents the event of postback.
 *
 * @package LINE\LINEBot\Event
 */
class PostbackEvent extends BaseEvent
{
    /**
     * PostbackEvent constructor.
     *
     * @param array $event
     */
    public function __construct($event)
    {
        parent::__construct($event);
    }

    /**
     * Returns the data of postback.
     *
     * @return string
     */
    public function getPostbackData()
    {
        return $this->event['postback']['data'];
    }

    /**
     * Returns the params of postback.
     *
     * @return array|null
     */
    public function getPostbackParams()
    {
        return isset($this->event['postback']['params'])
            ? $this->event['postback']['params']
            : null;
    }
}
