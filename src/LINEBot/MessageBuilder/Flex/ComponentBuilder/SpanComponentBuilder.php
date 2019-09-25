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

namespace LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
use LINE\LINEBot\Constant\Flex\ComponentType;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentTextDecoration;
use LINE\LINEBot\Constant\Flex\ComponentTextStyle;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for span component.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class SpanComponentBuilder implements ComponentBuilder
{
    /** @var string */
    private $text;
    /** @var ComponentFontSize */
    private $size;
    /** @var string */
    private $color;
    /** @var ComponentFontWeight */
    private $weight;
    /** @var ComponentTextStyle */
    private $style;
    /** @var ComponentTextDecoration */
    private $decoration;

    public function __construct(
        $text,
        $size = null,
        $color = null,
        $weight = null,
        $style = null,
        $decoration = null
    ) {
        $this->text = $text;
        $this->size = $size;
        $this->color = $color;
        $this->weight = $weight;
        $this->style = $style;
        $this->decoration = $decoration;
    }

    /**
     * Create empty TextComponentBuilder.
     *
     * @return TextComponentBuilder
     */
    public static function builder()
    {
        return new self(null);
    }

    /**
     * Set text.
     *
     * @param string $text
     * @return TextComponentBuilder
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Set size.
     *
     * @param ComponentFontSize|string|null $size
     * @return TextComponentBuilder
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Set weight.
     *
     * @param ComponentFontWeight|string|null $weight
     * @return TextComponentBuilder
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Set color.
     *
     * @param string|null $color
     * @return TextComponentBuilder
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Set style.
     *
     * @param string|null $style
     * @return TextComponentBuilder
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * Set decoration.
     *
     * @param string|null $decoration
     * @return TextComponentBuilder
     */
    public function setDecoration($decoration)
    {
        $this->decoration = $decoration;
        return $this;
    }

    public function build()
    {
        return BuildUtil::removeNullElements([
            'type' => ComponentType::SPAN,
            'text' => $this->text,
            'size' => $this->size,
            'color' => $this->color,
            'weight' => $this->weight,
            'style' => $this->style,
            'decoration' => $this->decoration
        ]);
    }
}
