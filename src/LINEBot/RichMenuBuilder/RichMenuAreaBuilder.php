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
 * A RichMenu area builder class
 *
 * @package LINE\LINEBot\RichMenu
 */
class RichMenuAreaBuilder
{
    /** @var RichMenuAreaBoundsBuilder */
    private $boundsBuilder;
    /** @var TemplateActionBuilder */
    private $actionBuilder;

    /**
     * RichMenuAreaBuilder constructor.
     *
     * @param RichMenuAreaBoundsBuilder $boundsBuilder Object describing the boundaries of the area in pixels.
     * @param TemplateActionBuilder $actionBuilder Action performed when the area is tapped. See action objects.
     *                                             Note: The label property is not supported for actions in rich menus.
     */
    public function __construct($boundsBuilder, $actionBuilder)
    {
        $this->boundsBuilder = $boundsBuilder;
        $this->actionBuilder = $actionBuilder;
    }

    /**
     * Builds RichMenu area object.
     *
     * @return array Built RichMenu area object.
     */
    public function build()
    {
        return [
            'bounds' => $this->boundsBuilder->build(),
            'action' => $this->actionBuilder->buildTemplateAction(),
        ];
    }
}
