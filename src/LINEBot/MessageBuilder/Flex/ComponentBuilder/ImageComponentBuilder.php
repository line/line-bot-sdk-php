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
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentType;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for image component.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ImageComponentBuilder implements ComponentBuilder
{
    /** @var string */
    private $url;
    /** @var int */
    private $flex;
    /** @var ComponentMargin */
    private $margin;
    /** @var ComponentAlign */
    private $align;
    /** @var ComponentGravity */
    private $gravity;
    /** @var ComponentImageSize */
    private $size;
    /** @var ComponentImageAspectRatio */
    private $aspectRatio;
    /** @var ComponentImageAspectMode */
    private $aspectMode;
    /** @var string */
    private $backgroundColor;
    /** @var TemplateActionBuilder */
    private $actionBuilder;

    /** @var array */
    private $component;

    /**
     * ImageComponentBuilder constructor.
     *
     * @param string $url
     * @param int|null $flex
     * @param ComponentMargin|null $margin
     * @param ComponentAlign|null $align
     * @param ComponentGravity|null $gravity
     * @param ComponentImageSize|null $size
     * @param ComponentImageAspectRatio|null $aspectRatio
     * @param ComponentImageAspectMode|null $aspectMode
     * @param string|null $backgroundColor
     * @param TemplateActionBuilder|null $actionBuilder
     */
    public function __construct(
        $url,
        $flex = null,
        $margin = null,
        $align = null,
        $gravity = null,
        $size = null,
        $aspectRatio = null,
        $aspectMode = null,
        $backgroundColor = null,
        $actionBuilder = null
    ) {
        $this->url = $url;
        $this->flex = $flex;
        $this->margin = $margin;
        $this->align = $align;
        $this->gravity = $gravity;
        $this->size = $size;
        $this->aspectRatio = $aspectRatio;
        $this->aspectMode = $aspectMode;
        $this->backgroundColor = $backgroundColor;
        $this->actionBuilder = $actionBuilder;
    }

    /**
     * Create empty ImageComponentBuilder.
     *
     * @return ImageComponentBuilder
     */
    public static function builder()
    {
        return new self(null);
    }

    /**
     * Set url.
     *
     * @param string $url
     * @return ImageComponentBuilder
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Set flex.
     *
     * @param int|null $flex
     * @return ImageComponentBuilder
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
     * @return ImageComponentBuilder
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
        return $this;
    }

    /**
     * Set align.
     *
     * @param ComponentAlign|string|null $align
     * @return ImageComponentBuilder
     */
    public function setAlign($align)
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Set gravity.
     *
     * @param ComponentGravity|string|null $gravity
     * @return ImageComponentBuilder
     */
    public function setGravity($gravity)
    {
        $this->gravity = $gravity;
        return $this;
    }

    /**
     * Set size.
     *
     * @param ComponentImageSize|string|null $size
     * @return ImageComponentBuilder
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Set aspectRatio.
     *
     * @param ComponentImageAspectRatio|string|null $aspectRatio
     * @return ImageComponentBuilder
     */
    public function setAspectRatio($aspectRatio)
    {
        $this->aspectRatio = $aspectRatio;
        return $this;
    }

    /**
     * Set aspectMode.
     *
     * @param ComponentImageAspectMode|string|null $aspectMode
     * @return ImageComponentBuilder
     */
    public function setAspectMode($aspectMode)
    {
        $this->aspectMode = $aspectMode;
        return $this;
    }

    /**
     * Set backgroundColor.
     *
     * @param string|null $backgroundColor
     * @return ImageComponentBuilder
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * Set action.
     *
     * @param TemplateActionBuilder|null $actionBuilder
     * @return ImageComponentBuilder
     */
    public function setAction($actionBuilder)
    {
        $this->actionBuilder = $actionBuilder;
        return $this;
    }

    /**
     * Builds image component structure.
     *
     * @return array
     */
    public function build()
    {
        if (isset($this->component)) {
            return $this->component;
        }

        $this->component = BuildUtil::removeNullElements([
            'type' => ComponentType::IMAGE,
            'url' => $this->url,
            'flex' => $this->flex,
            'margin' => $this->margin,
            'align' => $this->align,
            'gravity' => $this->gravity,
            'size' => $this->size,
            'aspectRatio' => $this->aspectRatio,
            'aspectMode' => $this->aspectMode,
            'backgroundColor' => $this->backgroundColor,
            'action' => BuildUtil::build($this->actionBuilder, 'buildTemplateAction'),
        ]);

        return $this->component;
    }
}
