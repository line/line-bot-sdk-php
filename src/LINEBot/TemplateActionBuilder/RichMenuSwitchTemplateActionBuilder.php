<?php

/**
 * Copyright 2022 LINE Corporation
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

namespace LINE\LINEBot\TemplateActionBuilder;

use LINE\LINEBot\Constant\ActionType;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for rich menu switch action.
 *
 * @package LINE\LINEBot\TemplateActionBuilder
 */
class RichMenuSwitchTemplateActionBuilder implements TemplateActionBuilder
{
    /** @var string */
    private $richMenuAliasId;
    /** @var string */
    private $data;
    /** @var string|null */
    private $label;

    /**
     * RichMenuSwitchAction constructor.
     *
     * @param string $richMenuAliasId The rich menu to be switched to.
     * @param string $data Returned text when postback event is triggered.
     * @param string $label Label of action.
     */
    public function __construct($richMenuAliasId, $data, $label = null)
    {
        $this->richMenuAliasId = $richMenuAliasId;
        $this->data = $data;
        $this->label = $label;
    }

    /**
     * Builds rich menu switch action structure.
     *
     * @return array Built rich menu switch action structure.
     */
    public function buildTemplateAction()
    {
        return BuildUtil::removeNullElements([
            'type' => ActionType::RICH_MENU_SWITCH,
            'richMenuAliasId' => $this->richMenuAliasId,
            'data' => $this->data,
            'label' => $this->label,
        ]);
    }
}
