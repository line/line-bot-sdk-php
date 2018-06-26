<?php

/**
 * Copyright 2017 LINE Corporation
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

namespace LINE\LINEBot\RichMenuBuilder;

/**
 * A RichMenu size builder class
 *
 * @package LINE\LINEBot\RichMenu
 */
class RichMenuSizeBuilder
{
    /** @var int */
    private $height;
    /** @var int */
    private $width;

    /**
     * RichMenuSizeBuilder constructor.
     *
     * @param int $height Height of the rich menu. Possible values: 1686, 843.
     * @param int $width Width of the rich menu. Must be 2500.
     */
    public function __construct($height, $width)
    {
        $this->height = $height;
        $this->width = $width;
    }

    /**
     * RichMenuSizeBuilder helper function
     *
     * @return RichMenuSizeBuilder instance with full size
     */
    public static function getFull()
    {
        return new self(1686, 2500);
    }

    /**
     * RichMenuSizeBuilder helper function
     *
     * @return RichMenuSizeBuilder instance with half size
     */
    public static function getHalf()
    {
        return new self(843, 2500);
    }

    /**
     * Builds RichMenu size object.
     *
     * @return array Built RichMenu size object.
     */
    public function build()
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
        ];
    }
}
