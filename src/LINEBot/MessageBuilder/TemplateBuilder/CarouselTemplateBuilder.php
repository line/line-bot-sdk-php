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
    /** @var string|null */
    private $imageAspectRatio;
    /** @var string|null */
    private $imageSize;


    /** @var array */
    private $template;



    /**
     * CarouselTemplateBuilder constructor.
     *
     * @param CarouselColumnTemplateBuilder[] $columnTemplateBuilders
     */
    public function __construct(array $columnTemplateBuilders,array $options=[])
    {
        $this->columnTemplateBuilders = $columnTemplateBuilders;
        if(isset($options["imageAspectRatio"])){
            $this->imageAspectRatio=$options["imageAspectRatio"];
        }
        if(isset($options["imageSize"])){
            $this->imageSize=$options["imageSize"];
        }
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

        $template=[
            'type' => TemplateType::CAROUSEL,
            'columns' => $columns,
        ];
        if(isset($this->imageAspectRatio)){
            $template["imageAspectRatio"]=$this->imageAspectRatio;
        }
        if(isset($this->imageSize)){
            $template["imageSize"]=$this->imageSize;
        }

        $this->template = $template;

        return $this->template;
    }
}
