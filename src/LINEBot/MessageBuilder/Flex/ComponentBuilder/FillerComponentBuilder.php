<?php

/**
 * Copyright 2018 LINE Corporation
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

namespace LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;

use LINE\LINEBot\Constant\Flex\ComponentType;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;

/**
 * A builder class for filler component.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder
 */
class FillerComponentBuilder implements ComponentBuilder
{
    /** @var int */
    private $flex;

    /**
     * Create empty FillerComponentBuilder.
     *
     * @return FillerComponentBuilder
     */
    public static function builder()
    {
        return new self();
    }

    public function setFlex($flex)
    {
        $this->flex = $flex;
        return $this;
    }

    /**
     * Builds filler component structure.
     *
     * @return array
     */
    public function build()
    {
        $component = [
            'type' => ComponentType::FILLER,
        ];
        if (isset($this->flex)) {
            $component['flex'] = $this->flex;
        }
        return $component;
    }
}
