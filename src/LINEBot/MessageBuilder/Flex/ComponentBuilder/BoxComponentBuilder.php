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
     * @param string|null $paddingAll
     * @param string|null $paddingTop
     * @param string|null $paddingBottom
     * @param string|null $paddingStart
     * @param string|null $paddingEnd
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
     * keyword: none (defined in BoxComponentPaddingKeyword)
     *
     * @param string|BoxComponentPaddingKeyword|null $paddingEnd
     * @return $this
     */
    public function setPaddingEnd($paddingEnd)
    {
        $this->paddingEnd = $paddingEnd;
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
        ]);

        return $this->component;
    }
}
