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
 * A builder class for column of image carousel template.
 *
 * @package LINE\LINEBot\MessageBuilder\TemplateBuilder
 */
class ImageCarouselColumnTemplateBuilder implements TemplateBuilder
{
    /** @var string */
    private $imageUrl;
    /** @var TemplateActionBuilder */
    private $actionBuilder;

    /** @var array */
    private $template;

    /**
     * ImageCarouselColumnTemplateBuilder constructor.
     *
     * @param string $imageUrl
     * @param TemplateActionBuilder $actionBuilder
     */
    public function __construct($imageUrl, $actionBuilder)
    {
        $this->imageUrl = $imageUrl;
        $this->actionBuilder = $actionBuilder;
    }

    /**
     * Builds column of image carousel template structure.
     *
     * @return array
     */
    public function buildTemplate()
    {
        if (!empty($this->template)) {
            return $this->template;
        }

        $this->template = [
            'imageUrl' => $this->imageUrl,
            'action' => $this->actionBuilder->buildTemplateAction(),
        ];

        return $this->template;
    }
}
