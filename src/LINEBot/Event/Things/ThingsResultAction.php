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
class ThingsResultAction
{
    const TYPE_VOID = 'void';
    const TYPE_BINARY = 'binary';

    /** @var string */
    private $type;
    /** @var string|null */
    private $data;

    /**
     * ThingsResultAction constructor
     *
     * @param string $type
     * @param string|null $data
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Returns the type of things event result action.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the data of things event result action.
     *
     * @return string|null
     */
    public function getData()
    {
        return $this->data;
    }
}
