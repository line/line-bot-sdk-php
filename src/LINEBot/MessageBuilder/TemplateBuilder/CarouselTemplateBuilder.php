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

/**
 * A builder class for carousel template.
 *
 * @package LINE\LINEBot\MessageBuilder\TemplateBuilder
 */
class CarouselTemplateBuilder implements TemplateBuilder
{
    /** @var CarouselColumnTemplateBuilder[] */
    private $columnTemplateBuilders;
    /** @var string */
    private $imageAspectRatio;
    /** @var string */
    private $imageSize;

    /** @var array */
    private $template;

    /**
     * CarouselTemplateBuilder constructor.
     *
     * @param CarouselColumnTemplateBuilder[] $columnTemplateBuilders
     * @param string|null $imageAspectRatio
     * @param string|null $imageSize
     */
    public function __construct(array $columnTemplateBuilders, $imageAspectRatio = null, $imageSize = null)
    {
        $this->columnTemplateBuilders = $columnTemplateBuilders;
        $this->imageAspectRatio = $imageAspectRatio;
        $this->imageSize = $imageSize;
    }

    /**
     * Builds carousel template structure.
     *
     * @return array
     */
    public function buildTemplate()
    {
        if (!empty($this->template)) {
            return $this->template;
        }

        $columns = [];
        foreach ($this->columnTemplateBuilders as $columnTemplateBuilder) {
            $columns[] = $columnTemplateBuilder->buildTemplate();
        }

        $this->template = [
            'type' => TemplateType::CAROUSEL,
            'columns' => $columns,
        ];

        if ($this->imageAspectRatio) {
            $this->template['imageAspectRatio'] = $this->imageAspectRatio;
        }

        if ($this->imageSize) {
            $this->template['imageSize'] = $this->imageSize;
        }

        return $this->template;
    }
}
