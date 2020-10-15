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
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ComponentType;
use LINE\LINEBot\Constant\Flex\ComponentPosition;
use LINE\LINEBot\Constant\Flex\ComponentJustifyContent;
use LINE\LINEBot\Constant\Flex\ComponentAlignItems;
use LINE\LINEBot\Constant\Flex\ComponentBackgroundType;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for box component.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder
 */
class BoxComponentBuilder implements ComponentBuilder
{
    /** @var ComponentLayout */
    private $layout;
    /** @var ComponentBuilder[] */
    private $componentBuilders;
    /** @var int */
    private $flex;
    /** @var ComponentSpacing */
    private $spacing;
    /** @var ComponentMargin */
    private $margin;
    /** @var TemplateActionBuilder */
    private $actionBuilder;

    /** @var string */
    private $paddingAll;
    /** @var string */
    private $paddingTop;
    /** @var string */
    private $paddingBottom;
    /** @var string */
    private $paddingStart;
    /** @var string */
    private $paddingEnd;

    /** @var string */
    private $backgroundColor;
    /** @var string */
    private $borderColor;
    /** @var string */
    private $borderWidth;
    /** @var string */
    private $cornerRadius;
    /** @var string */
    private $width;
    /** @var string */
    private $height;

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

    /** @var ComponentJustifyContent */
    private $justifyContent;
    /** @var ComponentAlignItems */
    private $alignItems;

    /** @var ComponentBackgroundType */
    private $backgroundType;
    /** @var string */
    private $backgroundAngle;
    /** @var string */
    private $backgroundStartColor;
    /** @var string */
    private $backgroundEndColor;
    /** @var string */
    private $backgroundCenterColor;
    /** @var string */
    private $backgroundCenterPosition;

    /** @var array */
    private $component;

    /**
     * BoxComponentBuilder constructor.
     *
     * @param ComponentLayout|string $layout
     * @param ComponentBuilder[] $componentBuilders
     * @param int|null $flex
     * @param ComponentSpacing|string|null $spacing
     * @param ComponentMargin|null $margin
     */
    public function __construct(
        $layout,
        $componentBuilders,
        $flex = null,
        $spacing = null,
        $margin = null,
        $actionBuilder = null
    ) {
        $this->layout = $layout;
        $this->componentBuilders = $componentBuilders;
        $this->flex = $flex;
        $this->spacing = $spacing;
        $this->margin = $margin;
        $this->actionBuilder = $actionBuilder;
    }

    /**
     * Create empty BoxComponentBuilder.
     *
     * @return BoxComponentBuilder
     */
    public static function builder()
    {
        return new self(null, null);
    }

    /**
     * Set laytout.
     *
     * @param ComponentLayout|string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Set contents.
     *
     * @param ComponentBuilder[] $componentBuilders
     * @return $this
     */
    public function setContents($componentBuilders)
    {
        $this->componentBuilders = $componentBuilders;
        return $this;
    }

    /**
     * Set flex.
     *
     * @param int|null $flex
     * @return $this
     */
    public function setFlex($flex)
    {
        $this->flex = $flex;
        return $this;
    }

    /**
     * Set spacing.
     *
     * @param ComponentSpacing|string|null $spacing
     * @return $this
     */
    public function setSpacing($spacing)
    {
        $this->spacing = $spacing;
        return $this;
    }

    /**
     * Set margin.
     *
     * @param ComponentMargin|string|null $margin
     * @return $this
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
        return $this;
    }

    /**
     * Set action.
     *
     * @param TemplateActionBuilder|null $actionBuilder
     * @return $this
     */
    public function setAction($actionBuilder)
    {
        $this->actionBuilder = $actionBuilder;
        return $this;
    }

    /**
     * Set paddingAll.
     *
     * specifiable percentage, pixel and keyword.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param string|ComponentSpacing|null $paddingAll
     * @return $this
     */
    public function setPaddingAll($paddingAll)
    {
        $this->paddingAll = $paddingAll;
        return $this;
    }

    /**
     * Set paddingTop.
     *
     * specifiable percentage, pixel and keyword.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param string|ComponentSpacing|null $paddingTop
     * @return $this
     */
    public function setPaddingTop($paddingTop)
    {
        $this->paddingTop = $paddingTop;
        return $this;
    }

    /**
     * Set paddingBottom.
     *
     * specifiable percentage, pixel and keyword.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param string|ComponentSpacing|null $paddingBottom
     * @return $this
     */
    public function setPaddingBottom($paddingBottom)
    {
        $this->paddingBottom = $paddingBottom;
        return $this;
    }

    /**
     * Set paddingStart.
     *
     * specifiable percentage, pixel and keyword.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param string|ComponentSpacing|null $paddingStart
     * @return $this
     */
    public function setPaddingStart($paddingStart)
    {
        $this->paddingStart = $paddingStart;
        return $this;
    }

    /**
     * Set paddingEnd.
     *
     * specifiable percentage, pixel and keyword.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param string|ComponentSpacing|null $paddingEnd
     * @return $this
     */
    public function setPaddingEnd($paddingEnd)
    {
        $this->paddingEnd = $paddingEnd;
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
     * Set background color
     *
     * Hex color string: #RRGGBB or #RRGGBBAA
     *
     * @param string|null $backgroundColor
     * @return $this
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * Set border color
     *
     * Hex color string: #RRGGBB or #RRGGBBAA
     *
     * @param string|null $borderColor
     * @return $this
     */
    public function setBorderColor($borderColor)
    {
        $this->borderColor = $borderColor;
        return $this;
    }

    /**
     * Set border width
     *
     * specifiable pixel and keyword.
     * (e.g.
     * pixel: 5px
     * keyword: none (defined in ComponentBorderWidth)
     *
     * @param ComponentBorderWidth|string|null $borderWidth
     * @return $this
     */
    public function setBorderWidth($borderWidth)
    {
        $this->borderWidth = $borderWidth;
        return $this;
    }

    /**
     * Set corner radius
     *
     * specifiable pixel and keyword.
     * (e.g.
     * pixel: 5px
     * keyword: none (defined in ComponentSpacing)
     *
     * @param ComponentSpacing|string|null $cornerRadius
     * @return $this
     */
    public function setCornerRadius($cornerRadius)
    {
        $this->cornerRadius = $cornerRadius;
        return $this;
    }

    /**
     * Set width
     *
     * specifiable percentage and pixel.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     *
     * ※In horizontal and baseline box,
     *  `flex` property is ignored and the value is regarded as 0
     *
     * @param string|null $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Set height
     *
     * specifiable percentage and pixel.
     * (e.g.
     * percentage: 5%
     * pixel: 5px
     *
     * ※In horizontal and baseline box,
     *  `flex` property is ignored and the value is regarded as 0
     *
     * @param string|null $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Set justifyContent
     *
     * @param string|ComponentJustifyContent|null $justifyContent
     * @return $this
     */
    public function setJustifyContent($justifyContent)
    {
        $this->justifyContent = $justifyContent;
        return $this;
    }

    /**
     * Set alignItems
     *
     * @param string|ComponentAlignItems|null $alignItems
     * @return $this
     */
    public function setAlignItems($alignItems)
    {
        $this->alignItems = $alignItems;
        return $this;
    }

    /**
     * Set backgroundType
     *
     * @param string|ComponentBackgroundType|null $backgroundType
     * @return $this
     */
    public function setBackgroundType($backgroundType)
    {
        $this->backgroundType = $backgroundType;
        return $this;
    }

    /**
     * Set backgroundAngle
     *
     * specifiable "**deg".
     * (e.g. 90deg, 23.5deg
     *
     * @param string $backgroundAngle
     * @return $this
     */
    public function setBackgroundAngle($backgroundAngle)
    {
        $this->backgroundAngle = $backgroundAngle;
        return $this;
    }

    /**
     * Set backgroundStartColor
     *
     * Hex color string: #RRGGBB or #RRGGBBAA
     *
     * @param string $backgroundStartColor
     * @return $this
     */
    public function setBackgroundStartColor($backgroundStartColor)
    {
        $this->backgroundStartColor = $backgroundStartColor;
        return $this;
    }

    /**
     * Set backgroundEndColor
     *
     * Hex color string: #RRGGBB or #RRGGBBAA
     *
     * @param string $backgroundEndColor
     * @return $this
     */
    public function setBackgroundEndColor($backgroundEndColor)
    {
        $this->backgroundEndColor = $backgroundEndColor;
        return $this;
    }

    /**
     * Set backgroundCenterColor
     *
     * Hex color string: #RRGGBB or #RRGGBBAA
     *
     * @param string $backgroundCenterColor
     * @return $this
     */
    public function setBackgroundCenterColor($backgroundCenterColor)
    {
        $this->backgroundCenterColor = $backgroundCenterColor;
        return $this;
    }

    /**
     * Set backgroundCenterPosition
     *
     * specifiable percentage (0%~100%).
     * (e.g. 5%, 10.1%, 100%
     *
     * @param string $backgroundCenterPosition
     * @return $this
     */
    public function setBackgroundCenterPosition($backgroundCenterPosition)
    {
        $this->backgroundCenterPosition = $backgroundCenterPosition;
        return $this;
    }

    /**
     * Builds box component structure.
     *
     * @return array
     */
    public function build()
    {
        if (isset($this->component)) {
            return $this->component;
        }

        $background = BuildUtil::removeNullElements([
            'type' => $this->backgroundType,
            'angle' => $this->backgroundAngle,
            'startColor' => $this->backgroundStartColor,
            'endColor' => $this->backgroundEndColor,
            'centerColor' => $this->backgroundCenterColor,
            'centerPosition' => $this->backgroundCenterPosition,
        ]);

        $contents = array_map(function ($componentBuilder) {
            /** @var ComponentBuilder $componentBuilder */
            return $componentBuilder->build();
        }, $this->componentBuilders);

        $this->component = BuildUtil::removeNullElements([
            'type' => ComponentType::BOX,
            'layout' => $this->layout,
            'contents' => $contents,
            'flex' => $this->flex,
            'spacing' => $this->spacing,
            'margin' => $this->margin,
            'action' => BuildUtil::build($this->actionBuilder, 'buildTemplateAction'),
            'paddingAll' => $this->paddingAll,
            'paddingTop' => $this->paddingTop,
            'paddingBottom' => $this->paddingBottom,
            'paddingStart' => $this->paddingStart,
            'paddingEnd' => $this->paddingEnd,
            'backgroundColor' => $this->backgroundColor,
            'borderColor' => $this->borderColor,
            'borderWidth' => $this->borderWidth,
            'cornerRadius' => $this->cornerRadius,
            'width' => $this->width,
            'height' => $this->height,
            'position' => $this->position,
            'offsetTop' => $this->offsetTop,
            'offsetBottom' => $this->offsetBottom,
            'offsetStart' => $this->offsetStart,
            'offsetEnd' => $this->offsetEnd,
            'justifyContent' => $this->justifyContent,
            'alignItems' => $this->alignItems,
            'background' => empty($background) ? null : $background,
        ]);

        return $this->component;
    }
}
