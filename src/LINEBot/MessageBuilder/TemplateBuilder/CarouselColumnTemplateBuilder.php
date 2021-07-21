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

use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder;

/**
 * A builder class for column of carousel template.
 *
 * @package LINE\LINEBot\MessageBuilder\TemplateBuilder
 */
class CarouselColumnTemplateBuilder implements TemplateBuilder
{
    /** @var string */
    private $title;
    /** @var string */
    private $text;
    /** @var string */
    private $thumbnailImageUrl;
    /** @var string */
    private $imageBackgroundColor;
    /** @var TemplateActionBuilder[] */
    private $actionBuilders;

    /** @var array */
    private $template;

    /**
     * @var TemplateActionBuilder|null
     */
    private $defaultAction;

    /**
     * CarouselColumnTemplateBuilder constructor.
     *
     * @param string $title
     * @param string $text
     * @param string $thumbnailImageUrl
     * @param TemplateActionBuilder[] $actionBuilders
     * @param string|null $imageBackgroundColor
     * @param TemplateActionBuilder|null $defaultAction
     */
    public function __construct(
        $title,
        $text,
        $thumbnailImageUrl,
        array $actionBuilders,
        $imageBackgroundColor = null,
        TemplateActionBuilder $defaultAction = null
    ) {
        $this->title = $title;
        $this->text = $text;
        $this->thumbnailImageUrl = $thumbnailImageUrl;
        $this->actionBuilders = $actionBuilders;
        $this->imageBackgroundColor = $imageBackgroundColor;
        $this->defaultAction = $defaultAction;
    }

    /**
     * Builds column of carousel template structure.
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
            'thumbnailImageUrl' => $this->thumbnailImageUrl,
            'title' => $this->title,
            'text' => $this->text,
            'actions' => $actions,
        ];

        if ($this->imageBackgroundColor) {
            $this->template['imageBackgroundColor'] = $this->imageBackgroundColor;
        }
        if ($this->defaultAction) {
            $this->template['defaultAction'] = $this->defaultAction->buildTemplateAction();
        }

        return $this->template;
    }
}
