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

use LINE\LINEBot\Constant\Flex\ContainerType;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder;

/**
 * A builder class for carousel container.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder
 */
class CarouselContainerBuilder implements ContainerBuilder
{
    /** @var BubbleContainerBuilder[] */
    private $containerBuilders;

    /** @var array */
    private $container;

    /**
     * AreaBuilder constructor.
     *
     * @param BubbleContainerBuilder[] $containerBuilders
     */
    public function __construct($containerBuilders)
    {
        $this->containerBuilders = $containerBuilders;
    }

    /**
     * Create empty CarouselContainerBuilder.
     *
     * @return CarouselContainerBuilder
     */
    public static function builder()
    {
        return new self(null);
    }

    /**
     * Set contents.
     *
     * @param BubbleContainerBuilder[] $containerBuilders
     * @return CarouselContainerBuilder
     */
    public function setContents($containerBuilders)
    {
        $this->containerBuilders = $containerBuilders;
        return $this;
    }

    /**
     * Builds carousel container structure.
     *
     * @return array
     */
    public function build()
    {
        if (!empty($this->container)) {
            return $this->container;
        }

        $contents = array_map(function ($containerBuilder) {
            return $containerBuilder->build();
        }, $this->containerBuilders);

        $this->container = [
            'type' => ContainerType::CAROUSEL,
            'contents' => $contents,
        ];

        return $this->container;
    }
}
