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

namespace LINE\LINEBot;

/**
 * A builder class for rich menu.
 *
 * @package LINE\LINEBot
 */
class RichMenuBuilder
{
    /** @var RichMenuSizeBuilder */
    private $sizeBuilder;
    /** @var boolean */
    private $selected;
    /** @var string */
    private $name;
    /** @var string */
    private $chartBarText;
    /** @var RichMenuAreaBuilder[] */
    private $areaBuilders = [];
    
    /**
     * RichMenu constructor.
     *
     * @param RichMenuSizeBuilder $sizeBuilder size object which contains the width and height of the rich menu
     *                                         displayed in the chat.
     * @param boolean $selected true to display the rich menu by default. Otherwise, false.
     * @param string $name Name of the rich menu. This value can be used to help manage your rich menus and
     *                     is not displayed to users.
     * @param string $chatBarText Text displayed in the chat bar.
     * @param RichMenuAreaBuilder[] $areaBuilders
     */
    public function __construct($sizeBuilder, $selected, $name, $chartBarText, $areaBuilders)
    {
        $this->sizeBuilder = $sizeBuilder;
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
    public function build()
    {
        $areas = [];
        foreach ($this->areaBuilders as $areaBuilder) {
            $areas[] = $areaBuilder->build();
        }

        return [
            'size' => $this->sizeBuilder->build(),
            'selected' => $this->selected,
            'name' => $this->name,
            'chatBarText' => $this->chartBarText,
            'areas' => $areas,
        ];
    }
}
