<?php
/**
 * Copyright 2020 LINE Corporation
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

namespace LINE\LINEBot\Narrowcast;

/**
 * A builder class for age demographic filter
 *
 * @package LINE\LINEBot\Narrowcast
 */
class AgeDemographicFilterBuilder extends DemographicFilterBuilder
{
    const TYPE = 'age';

    /** @var string $gte */
    private $gte;

    /** @var string $lt */
    private $lt;

    /**
     * Set gte
     *
     * @param string $gte
     */
    protected function setGte($gte)
    {
        $this->gte = $gte;
    }

    /**
     * Set lt
     *
     * @param string $lt
     */
    protected function setLt($lt)
    {
        $this->lt = $lt;
    }

    public function build()
    {
        return [
            'type' => self::TYPE,
            'gte' => $this->gte,
            'lt' => $this->lt,
        ];
    }
}
