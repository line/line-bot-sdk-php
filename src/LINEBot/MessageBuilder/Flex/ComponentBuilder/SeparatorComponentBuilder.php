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

use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentType;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for separator component.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder
 */
class SeparatorComponentBuilder implements ComponentBuilder
{
    /** @var ComponentMargin */
    private $margin;
    /** @var string */
    private $color;

    /** @var array */
    private $component;

    /**
     * SeparatorComponentBuilder constructor.
     *
     * @param ComponentMargin|null $margin
     * @param string|null $color
     */
    public function __construct($margin = null, $color = null)
    {
        $this->margin = $margin;
        $this->color = $color;
    }

    /**
     * Create empty SeparatorComponentBuilder.
     *
     * @return SeparatorComponentBuilder
     */
    public static function builder()
    {
        return new self();
    }

    /**
     * Set margin.
     *
     * @param ComponentMargin|string|null $margin
     * @return SeparatorComponentBuilder
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
        return $this;
    }

    /**
     * Set color.
     *
     * @param string|null $color
     * @return SeparatorComponentBuilder
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Builds separator component structure.
     *
     * @return array
     */
    public function build()
    {
        if (isset($this->component)) {
            return $this->component;
        }

        $this->component = BuildUtil::removeNullElements([
            'type' => ComponentType::SEPARATOR,
            'margin' => $this->margin,
            'color' => $this->color,
        ]);

        return $this->component;
    }
}
