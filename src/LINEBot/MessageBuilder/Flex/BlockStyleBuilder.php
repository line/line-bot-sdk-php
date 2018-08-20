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
 * A builder class for bubble style.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex
 */
class BlockStyleBuilder
{
    /** @var string */
    private $backgroundColor;
    /** @var boolean */
    private $separator;
    /** @var string */
    private $separatorColor;

    /** @var array */
    private $style;

    /**
     * BlockStyleBuilder constructor.
     *
     * @param string|null $backgroundColor
     * @param boolean|null $separator
     * @param string|null $separatorColor
     */
    public function __construct($backgroundColor = null, $separator = null, $separatorColor = null)
    {
        $this->backgroundColor = $backgroundColor;
        $this->separator = $separator;
        $this->separatorColor = $separatorColor;
    }

    /**
     * Create empty BlockStyleBuilder.
     *
     * @return BlockStyleBuilder
     */
    public static function builder()
    {
        return new self();
    }

    /**
     * Set backgroundColor.
     *
     * @param string|null $backgroundColor
     * @return BlockStyleBuilder
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * Set separator.
     *
     * @param boolean|null $separator
     * @return BlockStyleBuilder
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * Set separatorColor.
     *
     * @param string|null $separatorColor
     * @return BlockStyleBuilder
     */
    public function setSeparatorColor($separatorColor)
    {
        $this->separatorColor = $separatorColor;
        return $this;
    }

    /**
     * Builds block style structure.
     *
     * @return array
     */
    public function build()
    {
        if (isset($this->style)) {
            return $this->style;
        }

        $this->style = BuildUtil::removeNullElements([
            'backgroundColor' => $this->backgroundColor,
            'separator' => $this->separator,
            'separatorColor' => $this->separatorColor,
        ]);

        return $this->style;
    }
}
