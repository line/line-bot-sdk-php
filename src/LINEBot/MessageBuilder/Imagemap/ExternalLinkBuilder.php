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

/**
 * A builder class for external link of imagemap video.
 *
 * @package LINE\LINEBot\MessageBuilder\Imagemap
 */
class ExternalLinkBuilder
{
    /** @var string */
    private $linkUri;
    /** @var string */
    private $label;

    /**
     * ExternalLinkBuilder constructor.
     *
     * @param string $linkUri
     * @param string $label
     */
    public function __construct($linkUri, $label)
    {
        $this->linkUri = $linkUri;
        $this->label = $label;
    }

    /**
     * Builds external link of imagemap video.
     *
     * @return array
     */
    public function build()
    {
        return [
            'linkUri' => $this->linkUri,
            'label' => $this->label
        ];
    }
}
