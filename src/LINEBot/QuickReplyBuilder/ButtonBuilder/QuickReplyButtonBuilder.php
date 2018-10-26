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

namespace LINE\LINEBot\QuickReplyBuilder\ButtonBuilder;

use \LINE\LINEBot\QuickReplyBuilder\QuickReplyButtonBuilder as IQuickReplyButtonBuilder;
use LINE\LINEBot\TemplateActionBuilder;

/**
 * A builder class for quick reply button.
 *
 * @package LINE\LINEBot\QuickReplyBuilder\ButtonBuilder
 */
class QuickReplyButtonBuilder implements IQuickReplyButtonBuilder
{
    /** @var TemplateActionBuilder */
    private $actionBuilder;

    /** @var string */
    private $imageUrl;

    /**
     * QuickReplyButtonBuilder constructor.
     *
     * @param TemplateActionBuilder $actionBuilder
     * @param string $imageUrl
     */
    public function __construct(TemplateActionBuilder $actionBuilder, $imageUrl = null)
    {
        $this->actionBuilder = $actionBuilder;
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return array
     */
    public function buildQuickReplyButton()
    {
        $button = [
            'type' => 'action',
            'action' => $this->actionBuilder->buildTemplateAction(),
        ];

        if ($this->imageUrl) {
            $button['imageUrl'] = $this->imageUrl;
        }

        return $button;
    }
}
