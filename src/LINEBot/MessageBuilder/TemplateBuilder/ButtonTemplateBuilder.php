<?php /** @noinspection PhpOptionalBeforeRequiredParametersInspection */

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
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class ButtonTemplateBuilder implements TemplateBuilder
{
    /** @var string */
    private $title;

    /** @var string */
    private $text;

    /** @var string */
    private $thumbnailImageUrl;

    /** @var string */
    private $imageAspectRatio;

    /** @var string */
    private $imageSize;

    /** @var string */
    private $imageBackgroundColor;

    /** @var TemplateActionBuilder[] */
    private $actionBuilders;

    /** @var array */
    private $template;

    /**
     * @var TemplateActionBuilder
     */
    private $defaultAction;

    /**
     * ButtonTemplateBuilder constructor.
     *
     * @param string|null $title
     * @param string $text
     * @param string|null $thumbnailImageUrl
     * @param TemplateActionBuilder[] $actionBuilders
     * @param string|null $imageAspectRatio
     * @param string|null $imageSize
     * @param string|null $imageBackgroundColor
     * @param TemplateActionBuilder|null $defaultAction
     */
    public function __construct(
        $title = null,
        $text, // phpcs:ignore
        $thumbnailImageUrl = null,
        array $actionBuilders,
        $imageAspectRatio = null,
        $imageSize = null,
        $imageBackgroundColor = null,
        TemplateActionBuilder $defaultAction = null
    ) {
        $this->title = $title;
        $this->text = $text;
        $this->thumbnailImageUrl = $thumbnailImageUrl;
        $this->actionBuilders = $actionBuilders;
        $this->imageAspectRatio = $imageAspectRatio;
        $this->imageSize = $imageSize;
        $this->imageBackgroundColor = $imageBackgroundColor;
        $this->defaultAction = $defaultAction;
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

        if ($this->title) {
            $this->template['title'] = $this->title;
        }

        if ($this->thumbnailImageUrl) {
            $this->template['thumbnailImageUrl'] = $this->thumbnailImageUrl;
        }

        if ($this->imageAspectRatio) {
            $this->template['imageAspectRatio'] = $this->imageAspectRatio;
        }

        if ($this->imageSize) {
            $this->template['imageSize'] = $this->imageSize;
        }

        if ($this->imageBackgroundColor) {
            $this->template['imageBackgroundColor'] = $this->imageBackgroundColor;
        }

        if ($this->defaultAction) {
            $this->template['defaultAction'] = $this->defaultAction->buildTemplateAction();
        }

        return $this->template;
    }
}
