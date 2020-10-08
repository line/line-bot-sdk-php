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

namespace LINE\LINEBot\Narrowcast\DemographicFilter;

/**
 * A builder class for demographic filter has gte and lt parameters
 *
 * @package LINE\LINEBot\Narrowcast\DemographicFilter
 */
abstract class RangeDemographicFilterBuilder extends DemographicFilterBuilder
{
    /** @var string $gte */
    private $gte;

    /** @var string $lt */
    private $lt;

    /**
     * Get type
     *
     * @return string
     */
    abstract protected function getType();

    /**
     * Set gte
     *
     * @param string $gte
     * @return $this
     */
    public function setGte($gte)
    {
        $this->gte = $gte;
        return $this;
    }

    /**
     * Set lt
     *
     * @param string $lt
     * @return $this
     */
    public function setLt($lt)
    {
        $this->lt = $lt;
        return $this;
    }

    /**
     * Builds demographic filter
     *
     * @return array
     */
    public function build()
    {
        return [
            'type' => $this->getType(),
            'gte' => $this->gte,
            'lt' => $this->lt,
        ];
    }
}
