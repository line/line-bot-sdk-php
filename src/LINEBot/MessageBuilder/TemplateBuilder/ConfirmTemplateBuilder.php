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

namespace LINE\LINEBot\MessageBuilder\TemplateBuilder;

use LINE\LINEBot\Constant\TemplateType;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder;

/**
 * A builder class for confirm template.
 *
 * @package LINE\LINEBot\MessageBuilder\TemplateBuilder
 */
class ConfirmTemplateBuilder implements TemplateBuilder
{
    /** @var string */
    private $text;
    /** @var TemplateActionBuilder[] */
    private $actionBuilders;

    /** @var array */
    private $template;

    /**
     * ConfirmTemplate constructor.
     *
     * @param string $text
     * @param TemplateActionBuilder[] $actionBuilders
     */
    public function __construct($text, array $actionBuilders)
    {
        $this->text = $text;
        $this->actionBuilders = $actionBuilders;
    }

    /**
     * Builds confirm template structure.
     *
     * @return array
     */
    public function buildTemplate()
    {
        if (!empty($this->template)) {
            return $this->template;
        }

        $actions = [];
        foreach ($this->actionBuilders as $actionBuilder) {
            $actions[] = $actionBuilder->buildTemplateAction();
        }

        $this->template = [
            'type' => TemplateType::CONFIRM,
            'text' => $this->text,
            'actions' => $actions,
        ];

        return $this->template;
    }
}
