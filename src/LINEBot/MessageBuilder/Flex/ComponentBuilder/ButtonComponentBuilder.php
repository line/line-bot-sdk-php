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
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentType;
use LINE\LINEBot\Constant\Flex\ComponentAdjustMode;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for button component.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder
 */
class ButtonComponentBuilder implements ComponentBuilder
{
    /** @var TemplateActionBuilder */
    private $actionBuilder;
    /** @var int */
    private $flex;
    /** @var ComponentMargin */
    private $margin;
    /** @var ComponentButtonHeight */
    private $height;
    /** @var ComponentButtonStyle */
    private $style;
    /** @var string */
    private $color;
    /** @var ComponentGravity */
    private $gravity;

    /** @var string */
    private $position;
    /** @var string */
    private $offsetTop;
    /** @var string */
    private $offsetBottom;
    /** @var string */
    private $offsetStart;
    /** @var string */
    private $offsetEnd;

    /** @var ComponentAdjustMode */
    private $adjustMode;

    /** @var array */
    private $component;

    /**
     * ButtonComponentBuilder constructor.
     *
     * @param TemplateActionBuilder $actionBuilder
     * @param int|null $flex
     * @param ComponentMargin|null $margin
     * @param ComponentButtonHeight|null $height
     * @param ComponentButtonStyle|null $style
     * @param string|null $color
     * @param ComponentGravity|null $gravity
     */
    public function __construct(
        $actionBuilder,
        $flex = null,
        $margin = null,
        $height = null,
        $style = null,
        $color = null,
        $gravity = null
    ) {
        $this->actionBuilder = $actionBuilder;
        $this->flex = $flex;
        $this->margin = $margin;
        $this->height = $height;
        $this->style = $style;
        $this->color = $color;
        $this->gravity = $gravity;
    }

    /**
     * Create empty ButtonComponentBuilder.
     *
     * @return ButtonComponentBuilder
     */
    public static function builder()
    {
        return new self(null);
    }

    /**
     * Set action.
     *
     * @param TemplateActionBuilder $actionBuilder
     * @return ButtonComponentBuilder
     */
    public function setAction($actionBuilder)
    {
        $this->actionBuilder = $actionBuilder;
        return $this;
    }

    /**
     * Set flex.
     *
     * @param int|null $flex
     * @return ButtonComponentBuilder
     */
    public function setFlex($flex)
    {
        $this->flex = $flex;
        return $this;
    }

    /**
     * Set margin.
     *
     * @param ComponentMargin|string|null $margin
     * @return ButtonComponentBuilder
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
        return $this;
    }

    /**
     * Set height.
     *
     * @param ComponentButtonHeight|string|null $height
     * @return ButtonComponentBuilder
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Set style.
     *
     * @param ComponentButtonStyle|string|null $style
     * @return ButtonComponentBuilder
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * Set color.
     *
     * @param string|null $color
     * @return ButtonComponentBuilder
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Set gravity.
     *
     * @param ComponentGravity|string|null $gravity
     * @return ButtonComponentBuilder
     */
    public function setGravity($gravity)
    {
        $this->gravity = $gravity;
        return $this;
    }

    /**
     * Set position.
     *
     * specifiable relative or absolute
     *
     * @param string|ComponentPosition|null $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Set offsetTop.
     *
     * specifiable percentage, pixel and keyword.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param string|ComponentSpacing|null $offsetTop
     * @return $this
     */
    public function setOffsetTop($offsetTop)
    {
        $this->offsetTop = $offsetTop;
        return $this;
    }
    
    /**
     * Set offsetBottom.
     *
     * specifiable percentage, pixel and keyword.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param string|ComponentSpacing|null $offsetBottom
     * @return $this
     */
    public function setOffsetBottom($offsetBottom)
    {
        $this->offsetBottom = $offsetBottom;
        return $this;
    }
    
    /**
     * Set offsetStart.
     *
     * specifiable percentage, pixel and keyword.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param string|ComponentSpacing|null $offsetStart
     * @return $this
     */
    public function setOffsetStart($offsetStart)
    {
        $this->offsetStart = $offsetStart;
        return $this;
    }
    
    /**
     * Set offsetEnd.
     *
     * specifiable percentage, pixel and keyword.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param string|ComponentSpacing|null $offsetEnd
     * @return $this
     */
    public function setOffsetEnd($offsetEnd)
    {
        $this->offsetEnd = $offsetEnd;
        return $this;
    }

    /**
     * Set adjustMode
     *
     * @param ComponentAdjustMode|null $adjustMode
     * @return $this
     */
    public function setAdjustMode($adjustMode)
    {
        $this->adjustMode = $adjustMode;
        return $this;
    }

    /**
     * Builds button component structure.
     *
     * @return array
     */
    public function build()
    {
        if (isset($this->component)) {
            return $this->component;
        }

        $this->component = BuildUtil::removeNullElements([
            'type' => ComponentType::BUTTON,
            'action' => $this->actionBuilder->buildTemplateAction(),
            'flex' => $this->flex,
            'margin' => $this->margin,
            'height' => $this->height,
            'style' => $this->style,
            'color' => $this->color,
            'gravity' => $this->gravity,
            'position' => $this->position,
            'offsetTop' => $this->offsetTop,
            'offsetBottom' => $this->offsetBottom,
            'offsetStart' => $this->offsetStart,
            'offsetEnd' => $this->offsetEnd,
            'adjustMode' => $this->adjustMode,
        ]);

        return $this->component;
    }
}
