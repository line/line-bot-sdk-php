<?php

/**
 * Copyright 2025 LINE Corporation
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

/**
 * Options for event request parsing.
 */
class EventRequestOptions
{
    /**
     * @var callable|null Function that returns boolean to determine if signature validation should be skipped.
     *                    If the function returns true, the signature verification step is skipped.
     *                    This can be useful in scenarios such as when you're in the process of updating
     *                    the channel secret and need to temporarily bypass verification to avoid disruptions.
     */
    public $skipSignatureValidation;

    /**
     * Constructor
     *
     * @param callable|null $skipSignatureValidation Function that returns boolean to determine if signature validation should be skipped
     */
    public function __construct(?callable $skipSignatureValidation = null)
    {
        $this->skipSignatureValidation = $skipSignatureValidation;
    }
}
