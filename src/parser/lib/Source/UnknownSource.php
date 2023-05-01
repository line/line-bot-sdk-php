<?php

/**
 * Copyright 2023 LINE Corporation
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

namespace LINE\Parser\Source;
use LINE\Webhook\Model\Source;

/**
 * A class that represents the unknown source.
 *
 * If the event is not supported by this SDK, the event will be instantiate to this.
 *
 * @package LINE\Parser\Source
 */
class UnknownSource extends Source
{
    /**
     * UnknownSource constructor.
     *
     * @param array $event
     */
    public function __construct($source)
    {
        parent::__construct($source);
    }
}
