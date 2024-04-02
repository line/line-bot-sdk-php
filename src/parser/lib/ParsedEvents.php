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

namespace LINE\Parser;

use LINE\Webhook\Model\Event;

class ParsedEvents
{
    /** @var string|null */
    private $destination;
    /** @var Event[] */
    private $events;

    /**
     * @param string|null $destination
     * @param Event[] $events
     */
    public function __construct(?string $destination, array $events)
    {
        $this->destination = $destination;
        $this->events = $events;
    }

    /**
     * Get destination
     *
     * @return ?string
     */
    public function getDestination(): ?string
    {
        return $this->destination;
    }

    /**
     * Get events
     *
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
