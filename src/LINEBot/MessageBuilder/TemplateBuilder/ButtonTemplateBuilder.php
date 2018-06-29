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
 * A builder class for button template message.
 *
 * @package LINE\LINEBot\MessageBuilder\TemplateBuilder
 */
class ButtonTemplateBuilder implements TemplateBuilder
{
    /** @var string */
    private $title;
    /** @var string */
    private $text;
    /** @var string */
    private $thumbnailImageUrl;
    /** @var TemplateActionBuilder[] */
    private $actionBuilders;

    /** @var array */
    private $template;

    /**
     * ConfirmTemplate constructor.
     *
     * @param string $title
     * @param string $text
     * @param string $thumbnailImageUrl
     * @param TemplateActionBuilder[] $actionBuilders
     */
    public function __construct($title=null, $text, $thumbnailImageUrl=null, array $actionBuilders)
    {
        $this->title = $title;
        $this->text = $text;
        $this->thumbnailImageUrl = $thumbnailImageUrl;
        $this->actionBuilders = $actionBuilders;
    }

    /**
     * Builds button template message structure.
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
            'type' => TemplateType::BUTTONS,
            'text' => $this->text,
            'actions' => $actions,
        ];
        
        /* Make title optional */
        if($this->title){
            $this->template['title'] = $this->title;
        }
        
        /* Make thumbnailImageUrl optional */
        if($this->thumbnailImageUrl){
            $this->template['thumbnailImageUrl'] = $this->thumbnailImageUrl;
        }
        
        return $this->template;
    }
}
