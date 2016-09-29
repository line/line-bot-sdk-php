<?php

/**
 * Copyright 2016 LINE Corporation
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

namespace LINE\LINEBot\MessageBuilder\Imagemap;

/**
 * A builder class for base size of imagemap.
 *
 * @package LINE\LINEBot\MessageBuilder\Imagemap
 */
class BaseSizeBuilder
{
    /** @var int */
    private $height;
    /** @var int */
    private $width;

    /**
     * BaseSizeBuilder constructor.
     *
     * @param int $height
     * @param int $width
     */
    public function __construct($height, $width)
    {
        $this->height = $height;
        $this->width = $width;
    }

    /**
     * Builds base size of imagemap.
     *
     * @return array
     */
    public function build()
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
        ];
    }
}
