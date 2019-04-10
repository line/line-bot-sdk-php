<?php

/**
 * Copyright 2019 LINE Corporation
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

namespace LINE\LINEBot\MessageBuilder\Imagemap;

use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;

/**
 * A builder class for video of imagemap.
 *
 * @package LINE\LINEBot\MessageBuilder\Imagemap
 */
class VideoBuilder
{
    /** @var string */
    private $originalContentUrl;
    /** @var string */
    private $previewImageUrl;
    /** @var AreaBuilder */
    private $areaBuilder;
    /** @var ExternalLinkBuilder|null */
    private $externalLinkBuilder;
    
    /**
     * VideoBuilder constructor.
     *
     * @param string $originalContentUrl
     * @param string $previewImageUrl
     * @param AreaBuilder $area
     * @param ExternalLinkBuilder|null $externalLink
     */
    public function __construct(
        $originalContentUrl,
        $previewImageUrl,
        AreaBuilder $areaBuilder,
        ExternalLinkBuilder $externalLinkBuilder = null
    ) {
        $this->originalContentUrl = $originalContentUrl;
        $this->previewImageUrl = $previewImageUrl;
        $this->areaBuilder = $areaBuilder;
        $this->externalLinkBuilder = $externalLinkBuilder;
    }

    /**
     * Builds video of imagemap.
     *
     * @return array
     */
    public function build()
    {
        $video = [
            'originalContentUrl' => $this->originalContentUrl,
            'previewImageUrl' => $this->previewImageUrl,
            'area' => $this->areaBuilder->build()
        ];
        if ($this->externalLinkBuilder) {
            $video['externalLink'] = $this->externalLinkBuilder->build();
        }
        return $video;
    }
}
