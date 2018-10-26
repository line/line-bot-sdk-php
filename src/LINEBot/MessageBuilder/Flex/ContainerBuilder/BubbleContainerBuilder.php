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

namespace LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder;

use LINE\LINEBot\Constant\Flex\ContainerDirection;
use LINE\LINEBot\Constant\Flex\ContainerType;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for bubble container.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder
 */
class BubbleContainerBuilder implements ContainerBuilder
{
    /** @var ContainerDirection */
    private $direction;
    /** @var BoxComponentBuilder */
    private $headerComponentBuilder;
    /** @var ImageComponentBuilder */
    private $heroComponentBuilder;
    /** @var BoxComponentBuilder */
    private $bodyComponentBuilder;
    /** @var BoxComponentBuilder */
    private $footerComponentBuilder;
    /** @var BubbleStylesBuilder */
    private $stylesBuilder;

    /** @var array */
    private $container;

    /**
     * BubbleContainerBuilder constructor.
     *
     * @param ContainerDirection|null $direction
     * @param BoxComponentBuilder|null $headerComponentBuilder
     * @param ImageComponentBuilder|null $heroComponentBuilder
     * @param BoxComponentBuilder|null $bodyComponentBuilder
     * @param BoxComponentBuilder|null $footerComponentBuilder
     * @param BubbleStylesBuilder|null $stylesBuilder
     */
    public function __construct(
        $direction = null,
        $headerComponentBuilder = null,
        $heroComponentBuilder = null,
        $bodyComponentBuilder = null,
        $footerComponentBuilder = null,
        $stylesBuilder = null
    ) {
        $this->direction = $direction;
        $this->headerComponentBuilder = $headerComponentBuilder;
        $this->heroComponentBuilder = $heroComponentBuilder;
        $this->bodyComponentBuilder = $bodyComponentBuilder;
        $this->footerComponentBuilder = $footerComponentBuilder;
        $this->stylesBuilder = $stylesBuilder;
    }

    /**
     * Create empty BubbleContainerBuilder.
     *
     * @return BubbleContainerBuilder
     */
    public static function builder()
    {
        return new self();
    }

    /**
     * Set direction.
     *
     * @param ContainerDirection|string|null $direction
     * @return BubbleContainerBuilder
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        return $this;
    }

    /**
     * Set header.
     *
     * @param BoxComponentBuilder|null $headerComponentBuilder
     * @return BubbleContainerBuilder
     */
    public function setHeader($headerComponentBuilder)
    {
        $this->headerComponentBuilder = $headerComponentBuilder;
        return $this;
    }

    /**
     * Set hero.
     *
     * @param ImageComponentBuilder|null $heroComponentBuilder
     * @return BubbleContainerBuilder
     */
    public function setHero($heroComponentBuilder)
    {
        $this->heroComponentBuilder = $heroComponentBuilder;
        return $this;
    }

    /**
     * Set body.
     *
     * @param BoxComponentBuilder|null $bodyComponentBuilder
     * @return BubbleContainerBuilder
     */
    public function setBody($bodyComponentBuilder)
    {
        $this->bodyComponentBuilder = $bodyComponentBuilder;
        return $this;
    }

    /**
     * Set footer.
     *
     * @param BoxComponentBuilder|null $footerComponentBuilder
     * @return BubbleContainerBuilder
     */
    public function setFooter($footerComponentBuilder)
    {
        $this->footerComponentBuilder = $footerComponentBuilder;
        return $this;
    }

    /**
     * Set style.
     *
     * @param BubbleStylesBuilder|null $stylesBuilder
     * @return BubbleContainerBuilder
     */
    public function setStyles($stylesBuilder)
    {
        $this->stylesBuilder = $stylesBuilder;
        return $this;
    }

    /**
     * Builds bubble container structure.
     *
     * @return array
     */
    public function build()
    {
        if (!empty($this->container)) {
            return $this->container;
        }

        $this->container = BuildUtil::removeNullElements([
            'type' => ContainerType::BUBBLE,
            'direction' => $this->direction,
            'header' => BuildUtil::build($this->headerComponentBuilder),
            'hero' => BuildUtil::build($this->heroComponentBuilder),
            'body' => BuildUtil::build($this->bodyComponentBuilder),
            'footer' => BuildUtil::build($this->footerComponentBuilder),
            'styles' => BuildUtil::build($this->stylesBuilder),
        ]);

        return $this->container;
    }
}
