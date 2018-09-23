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

namespace LINE\LINEBot\TemplateActionBuilder;

use LINE\LINEBot\Constant\ActionType;
use LINE\LINEBot\Util\BuildUtil;
use LINE\LINEBot\TemplateActionBuilder;

/**
 * A builder class for URI action.
 *
 * @package LINE\LINEBot\TemplateActionBuilder
 */
class UriTemplateActionBuilder implements TemplateActionBuilder
{
    /** @var string */
    private $label;
    /** @var string */
    private $uri;

    /**
     * UriAction constructor.
     *
     * @param string $label Label of action.
     * @param string $uri URI of the link.
     */
    public function __construct($label, $uri)
    {
        $this->label = $label;
        $this->uri = $uri;
    }

    /**
     * Builds URI action structure.
     *
     * @return array Built URI action structure.
     */
    public function buildTemplateAction()
    {
        return BuildUtil::removeNullElements([
            'type' => ActionType::URI,
            'label' => $this->label,
            'uri' => $this->uri,
        ]);
    }
}
