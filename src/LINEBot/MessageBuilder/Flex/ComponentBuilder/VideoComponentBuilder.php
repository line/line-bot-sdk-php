<?php

/**
 * Copyright 2022 LINE Corporation
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

namespace LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;

use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentType;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\Util\BuildUtil;

/**
 * A builder class for video component.
 *
 * @package LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class VideoComponentBuilder implements ComponentBuilder
{
    /** @var string */
    private $url;
    /** @var string */
    private $previewUrl;
    /** @var ImageComponentBuilder|BoxComponentBuilder */
    private $altContent;
    /** @var ComponentImageAspectRatio|null */
    private $aspectRatio;
    /** @var TemplateActionBuilder|null */
    private $actionBuilder;

    /** @var array */
    private $component;

    /**
     * VideoComponentBuilder constructor.
     *
     * @param string $url
     * @param string $previewUrl
     * @param ImageComponentBuilder|BoxComponentBuilder $altContent
     * @param ComponentImageAspectRatio|string|null $aspectRatio
     * @param TemplateActionBuilder|null $actionBuilder
     */
    public function __construct(
        $url,
        $previewUrl,
        $altContent,
        $aspectRatio = null,
        $actionBuilder = null
    ) {
        $this->url = $url;
        $this->previewUrl = $previewUrl;
        $this->altContent = $altContent;
        $this->aspectRatio = $aspectRatio;
        $this->actionBuilder = $actionBuilder;
    }

    /**
     * Create empty VideoComponentBuilder.
     *
     * @return VideoComponentBuilder
     */
    public static function builder()
    {
        return new self(null, null, null);
    }

    /**
     * Set url.
     *
     * @param string $url
     * @return VideoComponentBuilder
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Set previewUrl.
     *
     * @param string $previewUrl
     * @return VideoComponentBuilder
     */
    public function setPreviewUrl($previewUrl)
    {
        $this->previewUrl = $previewUrl;
        return $this;
    }

    /**
     * Set altContent.
     *
     * @param ImageComponentBuilder|BoxComponentBuilder $altContent
     * @return VideoComponentBuilder
     */
    public function setAltContent($altContent)
    {
        $this->altContent = $altContent;
        return $this;
    }

    /**
     * Set aspectRatio.
     *
     * @param ComponentImageAspectRatio|string|null $aspectRatio
     * @return VideoComponentBuilder
     */
    public function setAspectRatio($aspectRatio)
    {
        $this->aspectRatio = $aspectRatio;
        return $this;
    }

    /**
     * Set action.
     *
     * @param TemplateActionBuilder|null $actionBuilder
     * @return VideoComponentBuilder
     */
    public function setAction($actionBuilder)
    {
        $this->actionBuilder = $actionBuilder;
        return $this;
    }

    /**
     * Builds video component structure.
     *
     * @return array
     */
    public function build()
    {
        if (isset($this->component)) {
            return $this->component;
        }

        $this->component = BuildUtil::removeNullElements([
            'type' => ComponentType::VIDEO,
            'url' => $this->url,
            'previewUrl' => $this->previewUrl,
            'altContent' => $this->altContent->build(),
            'aspectRatio' => $this->aspectRatio,
            'action' => BuildUtil::build($this->actionBuilder, 'buildTemplateAction'),
        ]);

        return $this->component;
    }
}
