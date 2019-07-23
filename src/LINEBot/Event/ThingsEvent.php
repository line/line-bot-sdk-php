<?php

/**
 * Copyright 2019 LINE Corporation
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

use LINE\LINEBot\Event\Things\ThingsResult;

/**
 * A class that represents the event of things event.
 *
 * @package LINE\LINEBot\Event
 */
class ThingsEvent extends BaseEvent
{
    const TYPE_DEVICE_LINKED = 'link';
    const TYPE_DEVICE_UNLINKED = 'unlink';
    const TYPE_SCENARIO_RESULT = 'scenarioResult';

    /** @var ThingsResult */
    private $result;

    /**
     * ThingsEvent constructor.
     *
     * @param array $event
     */
    public function __construct($event)
    {
        parent::__construct($event);
        if (isset($this->event['things']['result'])) {
            $this->result = new ThingsResult($this->event['things']['result']);
        }
    }

    /**
     * Gets the device ID
     *
     * @return string
     */
    public function getDeviceId()
    {
        return $this->event['things']['deviceId'];
    }

    /**
     * Returns the things event type.
     *
     * @return string
     */
    public function getThingsEventType()
    {
        return $this->event['things']['type'];
    }

    /**
     * Returns the things event result.
     *
     * @return ThingsResult|null
     */
    public function getScenarioResult()
    {
        return $this->result;
    }
}
