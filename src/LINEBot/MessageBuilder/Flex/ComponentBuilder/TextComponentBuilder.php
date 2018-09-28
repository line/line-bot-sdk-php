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

use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\Constant\Flex\ComponentAlign;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentType;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for text component.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder
 */
class TextComponentBuilder implements ComponentBuilder
{
    /** @var string */
    private $text;
    /** @var int */
    private $flex;
    /** @var ComponentMargin */
    private $margin;
    /** @var ComponentFontSize */
    private $size;
    /** @var ComponentAlign */
    private $align;
    /** @var ComponentGravity */
    private $gravity;
    /** @var boolean */
    private $wrap;
    /** @var int */
    private $maxLines;
    /** @var ComponentFontWeight */
    private $weight;
    /** @var string */
    private $color;
    /** @var TemplateActionBuilder */
    private $actionBuilder;

    /** @var array */
    private $component;

    /**
     * TextComponentBuilder constructor.
     *
     * @param string $text
     * @param int|null $flex
     * @param ComponentMargin|null $margin
     * @param ComponentFontSize|null $size
     * @param ComponentAlign|null $align
     * @param ComponentGravity|null $gravity
     * @param boolean|null $wrap
     * @param int|null $maxLines
     * @param ComponentFontWeight|null $weight
     * @param string|null $color
     * @param TemplateActionBuilder|null $actionBuilder
     */
    public function __construct(
        $text,
        $flex = null,
        $margin = null,
        $size = null,
        $align = null,
        $gravity = null,
        $wrap = null,
        $maxLines = null,
        $weight = null,
        $color = null,
        $actionBuilder = null
    ) {
        $this->text = $text;
        $this->flex = $flex;
        $this->margin = $margin;
        $this->size = $size;
        $this->align = $align;
        $this->gravity = $gravity;
        $this->wrap = $wrap;
        $this->maxLines = $maxLines;
        $this->weight = $weight;
        $this->color = $color;
        $this->actionBuilder = $actionBuilder;
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
     * Set flex.
     *
     * @param int|null $flex
     * @return TextComponentBuilder
     */
    public function setFlex($flex)
    {
        $this->flex = $flex;
        return $this;
    }

    /**
     * Set margin.
     *
     * @param ComponentMargin|null $margin
     * @return TextComponentBuilder
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
        return $this;
    }

    /**
     * Set size.
     *
     * @param ComponentFontSize|null $size
     * @return TextComponentBuilder
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Set align.
     *
     * @param ComponentAlign|null $align
     * @return TextComponentBuilder
     */
    public function setAlign($align)
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Set gravity.
     *
     * @param ComponentGravity|null $gravity
     * @return TextComponentBuilder
     */
    public function setGravity($gravity)
    {
        $this->gravity = $gravity;
        return $this;
    }

    /**
     * Set wrap.
     *
     * @param boolean|null $wrap
     * @return TextComponentBuilder
     */
    public function setWrap($wrap)
    {
        $this->wrap = $wrap;
        return $this;
    }

    /**
     * Set maxLines.
     *
     * @param int|null $maxLines
     * @return TextComponentBuilder
     */
    public function setMaxLines($maxLines)
    {
        $this->maxLines = $maxLines;
        return $this;
    }

    /**
     * Set weight.
     *
     * @param ComponentFontWeight|null $weight
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
     * Set action.
     *
     * @param TemplateActionBuilder|null $actionBuilder
     * @return TextComponentBuilder
     */
    public function setAction($actionBuilder)
    {
        $this->actionBuilder = $actionBuilder;
        return $this;
    }

    /**
     * Builds text component structure.
     *
     * @return array
     */
    public function build()
    {
        if (isset($this->component)) {
            return $this->component;
        }

        $this->component = BuildUtil::removeNullElements([
            'type' => ComponentType::TEXT,
            'text' => $this->text,
            'flex' => $this->flex,
            'margin' => $this->margin,
            'size' => $this->size,
            'align' => $this->align,
            'gravity' => $this->gravity,
            'wrap' => $this->wrap,
            'maxLines' => $this->maxLines,
            'weight' => $this->weight,
            'color' => $this->color,
            'action' => BuildUtil::build($this->actionBuilder, 'buildTemplateAction'),
        ]);

        return $this->component;
    }
}
