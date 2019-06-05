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

namespace LINE\LINEBot\Event\Things;

/**
 * @package LINE\LINEBot\Event\Things
 */
class ThingsResult
{
    private $result;
    private $actionResults;

    /**
     * ThingsResult constructor.
     *
     * @param array $result
     */
    public function __construct($result)
    {
        $this->result = $result;
        $this->actionResults = array_map(function ($actionResult) {
            return new ThingsResultAction(
                $actionResult['type'],
                isset($actionResult['data']) ? $actionResult['data'] : null
            );
        }, $this->result['actionResults']);
    }

    /**
     * Returns the things event result senario id.
     *
     * @return string
     */
    public function getScenarioId()
    {
        return $this->result['scenarioId'];
    }

    /**
     * Returns the things event result revision.
     *
     * @return int
     */
    public function getRevision()
    {
        return $this->result['revision'];
    }

    /**
     * Returns the things event result code.
     *
     * @return string
     */
    public function getResultCode()
    {
        return $this->result['resultCode'];
    }

    /**
     * Returns the things event result start time.
     *
     * @return int
     */
    public function getStartTime()
    {
        return $this->result['startTime'];
    }

    /**
     * Returns the things event result end time.
     *
     * @return int
     */
    public function getEndTime()
    {
        return $this->result['endTime'];
    }

    /**
     * Returns the things event result ble notification payload.
     *
     * @return string
     */
    public function getBleNotificationPayload()
    {
        return $this->result['bleNotificationPayload'];
    }


    /**
     * Returns the things event result action results.
     *
     * @return array
     */
    public function getActionResults()
    {
        return $this->actionResults;
    }
}
