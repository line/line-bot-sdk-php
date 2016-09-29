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

namespace LINE\LINEBot\ImagemapActionBuilder;

use LINE\LINEBot\Constant\ActionType;
use LINE\LINEBot\ImagemapActionBuilder;

/**
 * A builder class for URI action of imagemap.
 *
 * @package LINE\LINEBot\ImagemapActionBuilder
 */
class ImagemapUriActionBuilder implements ImagemapActionBuilder
{
    private $linkUri;
    private $areaBuilder;

    /**
     * ImagemapUriActionBuilder constructor.
     *
     * @param string $linkUri URI of the link.
     * @param AreaBuilder $areaBuilder Builder of area.
     */
    public function __construct($linkUri, AreaBuilder $areaBuilder)
    {
        $this->linkUri = $linkUri;
        $this->areaBuilder = $areaBuilder;
    }

    /**
     * Builds imagemap URI action structure.
     *
     * @return array Built URI action structure.
     */
    public function buildImagemapAction()
    {
        return [
            'type' => ActionType::URI,
            'linkUri' => $this->linkUri,
            'area' => $this->areaBuilder->build(),
        ];
    }
}
