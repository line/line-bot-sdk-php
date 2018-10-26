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

namespace LINE\LINEBot\MessageBuilder\Flex;

use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for bubble styles.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex
 */
class BubbleStylesBuilder
{
    /** @var BlockStyleBuilder */
    private $headerStyleBuilder;
    /** @var BlockStyleBuilder */
    private $heroStyleBuilder;
    /** @var BlockStyleBuilder */
    private $bodyStyleBuilder;
    /** @var BlockStyleBuilder */
    private $footerStyleBuilder;

    /** @var array */
    private $styles;

    /**
     * BubbleStylesBuilder constructor.
     *
     * @param BlockStyleBuilder|null $headerStyleBuilder
     * @param BlockStyleBuilder|null $heroStyleBuilder
     * @param BlockStyleBuilder|null $bodyStyleBuilder
     * @param BlockStyleBuilder|null $footerStyleBuilder
     */
    public function __construct(
        $headerStyleBuilder = null,
        $heroStyleBuilder = null,
        $bodyStyleBuilder = null,
        $footerStyleBuilder = null
    ) {
        $this->headerStyleBuilder = $headerStyleBuilder;
        $this->heroStyleBuilder = $heroStyleBuilder;
        $this->bodyStyleBuilder = $bodyStyleBuilder;
        $this->footerStyleBuilder = $footerStyleBuilder;
    }

    /**
     * Create empty BubbleStylesBuilder.
     *
     * @return BubbleStylesBuilder
     */
    public static function builder()
    {
        return new self();
    }

    /**
     * Set header.
     *
     * @param BlockStyleBuilder|null $headerStyleBuilder
     * @return BubbleStylesBuilder
     */
    public function setHeader($headerStyleBuilder)
    {
        $this->headerStyleBuilder = $headerStyleBuilder;
        return $this;
    }

    /**
     * Set hero.
     *
     * @param BlockStyleBuilder|null $heroStyleBuilder
     * @return BubbleStylesBuilder
     */
    public function setHero($heroStyleBuilder)
    {
        $this->heroStyleBuilder = $heroStyleBuilder;
        return $this;
    }

    /**
     * Set body.
     *
     * @param BlockStyleBuilder|null $bodyStyleBuilder
     * @return BubbleStylesBuilder
     */
    public function setBody($bodyStyleBuilder)
    {
        $this->bodyStyleBuilder = $bodyStyleBuilder;
        return $this;
    }

    /**
     * Set footer.
     *
     * @param BlockStyleBuilder|null $footerStyleBuilder
     * @return BubbleStylesBuilder
     */
    public function setFooter($footerStyleBuilder)
    {
        $this->footerStyleBuilder = $footerStyleBuilder;
        return $this;
    }

    /**
     * Builds bubble styles structure.
     *
     * @return array
     */
    public function build()
    {
        if (isset($this->styles)) {
            return $this->styles;
        }

        $this->styles = BuildUtil::removeNullElements([
            'header' => BuildUtil::build($this->headerStyleBuilder),
            'hero' => BuildUtil::build($this->heroStyleBuilder),
            'body' => BuildUtil::build($this->bodyStyleBuilder),
            'footer' => BuildUtil::build($this->footerStyleBuilder),
        ]);

        return $this->styles;
    }
}
