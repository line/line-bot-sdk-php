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
 * A builder class for demographic filter has a oneOf parameter
 *
 * @package LINE\LINEBot\Narrowcast\DemographicFilter
 */
abstract class OneOfDemographicFilter extends DemographicFilterBuilder
{
    /** @var string[] $oneOf */
    private $oneOf = [];

    /**
     * Get type
     *
     * @return string
     */
    abstract protected function getType();

    /**
     * Set oneOf
     *
     * @param string[] $oneOf
     * @return $this
     */
    public function setOneOf($oneOf)
    {
        $this->oneOf = $oneOf;
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
            'oneOf' => $this->oneOf,
        ];
    }
}
