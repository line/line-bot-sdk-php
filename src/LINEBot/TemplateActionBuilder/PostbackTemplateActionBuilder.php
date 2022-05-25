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

namespace LINE\LINEBot\TemplateActionBuilder;

use LINE\LINEBot\Constant\ActionType;
use LINE\LINEBot\TemplateActionBuilder;

/**
 * A builder class for postback action.
 *
 * @package LINE\LINEBot\TemplateActionBuilder
 */
class PostbackTemplateActionBuilder implements TemplateActionBuilder
{
    /** @var string */
    private $label;
    /** @var string */
    private $data;
    /** @var string|null */
    private $displayText;
    /** @var string|null */
    private $inputOption;
    /** @var string|null */
    private $fillInText;

    /**
     * PostbackAction constructor.
     *
     * @param string $label Label of action.
     * @param string $data Data of postback.
     * @param string|null $displayText The text which will be sent when action is executed (optional).
     * @param string|null $inputOption The display method of such as rich menu based on user action (optional).
     * @param string|null $fillInText String to be pre-filled in the input field when the keyboard is opened (optional).
     */
    public function __construct($label, $data, $displayText = null, $inputOption = null, $fillInText = null)
    {
        $this->label = $label;
        $this->data = $data;
        $this->displayText = $displayText;
        $this->inputOption = $inputOption;
        $this->fillInText = $fillInText;
    }

    /**
     * Builds postback action structure.
     *
     * @return array Built postback action structure.
     */
    public function buildTemplateAction()
    {
        $action = [
            'type' => ActionType::POSTBACK,
            'label' => $this->label,
            'data' => $this->data,
        ];

        if (isset($this->displayText)) {
            // If text is set, append extend field.
            $action['displayText'] = $this->displayText;
        }

        if (isset($this->inputOption)) {
            // If inputOption is set, append extend field.
            $action['inputOption'] = $this->inputOption;
        }

        if (isset($this->fillInText)) {
            // If fillInText is set, append extend field.
            $action['fillInText'] = $this->fillInText;
        }

        return $action;
    }
}
