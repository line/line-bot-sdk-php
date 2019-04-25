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

namespace LINE\LINEBot\TemplateActionBuilder\Uri;

/**
 * A builder class for alt uri of uri.
 *
 * @package LINE\LINEBot\TemplateActionBuilder\Uri
 */
class AltUriBuilder
{
    /** @var string */
    private $desktop;

    /**
     * AltUriBuilder constructor.
     *
     * @param string $desktop
     */
    public function __construct($desktop)
    {
        $this->desktop = $desktop;
    }

    /**
     * Builds alt uri of uri.
     *
     * @return array
     */
    public function build()
    {
        return [
            'desktop' => $this->desktop
        ];
    }
}
