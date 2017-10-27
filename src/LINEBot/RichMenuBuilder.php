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

namespace LINE\LINEBot;

use LINE\LINEBot\RichMenuBuilder\AreaBuilder;

/**
 * A builder class for rich menu.
 *
 * @package LINE\LINEBot
 */
class RichMenuBuilder
{
    /** @var int */
    private $width;
    /** @var int */
    private $height;
    /** @var boolean */
    private $selected;
    /** @var string */
    private $name;
    /** @var string */
    private $chartBarText;
    /** @var AreaBuilder[] */
    private $areaBuilders = [];
    
    /**
     * RichMenuBuilder constructor.
     * @param boolean $isThinMenu
     * @param boolean $selected
     * @param string $name
     * @param string $chartBarText
     * @param AreaBuilder[] $areaBuilders
     */
    public function __construct($isThinMenu, $selected, $name, $chartBarText, $areaBuilders)
    {
        $this->width = 2500;
        $this->height = ($isThinMenu) ? 843 : 1686;
        $this->selected = $selected;
        $this->name = $name;
        $this->chartBarText = $chartBarText;
        $this->areaBuilders = $areaBuilders;
    }
 
    /**
     * Builds message structure.
     *
     * @return array Built message structure.
     */
    public function buildRichMenu() {
        $actions = [];
        foreach ($this->areaBuilders as $areaBuilder) {
            $actions[] = $areaBuilder->buildArea();
        }

        return [
            'size' => [
                'width' => $this->width,
                'height' => $this->height,
            ],
            'selected' => $this->selected,
            'name' => $this->name,
            'chatBarText' => $this->chartBarText,
            'areas' => $actions,
        ];
    }
}
