<?php

/**
 * Copyright 2017 LINE Corporation
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
 * A class that represents the unknown event.
 *
 * If the event is not supported by this SDK, the event will be instantiate to this.
 *
 * @package LINE\LINEBot\Event
 */
class UnknownEvent extends BaseEvent
{
    /**
     * UnknownEvent constructor.
     *
     * @param array $event
     */
    public function __construct($event)
    {
        parent::__construct($event);
    }

    /**
     * Returns unprocessed event body.
     *
     * You can handle the event with getting the event body through this even if the event type is unknown.
     *
     * @return array
     */
    public function getEventBody()
    {
        return $this->event;
    }
}
