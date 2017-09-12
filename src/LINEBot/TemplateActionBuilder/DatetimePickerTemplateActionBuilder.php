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
 * A builder class for datetime picker action.
 *
 * When this action is tapped, a postback event is returned via webhook with the date and
 *  time selected by the user from the date and time selection dialog.
 *
 * @package LINE\LINEBot\TemplateActionBuilder
 */
class DatetimePickerTemplateActionBuilder implements TemplateActionBuilder
{
    /** @var string */
    private $label;
    /** @var string */
    private $data;
    /** @var string */
    private $mode;
    /** @var string */
    private $initial;
    /** @var string */
    private $max;
    /** @var string */
    private $min;

    /**
     * DatetimePickerAction constructor.
     *
     * @param string $label Label for the action
     * Required for templates other than image carousel. Max: 20 characters
     * Optional for image carousel templates. Max: 12 characters.
     * @param string $data String returned via webhook in the postback.data property of the postback event
     * Max: 300 characters
     * @param string $mode Action mode
     * date: Pick date
     * time: Pick time
     * datetime: Pick date and time
     * @param string initial Initial value of date or time
     * @param string max Largest date or time value that can be selected.
     * Must be greater than the min value.
     * @param string min Smallest date or time value that can be selected.
     * Must be less than the max value.
     */
    public function __construct($label, $data, $mode, $initial = null, $max = null, $min = null)
    {
        $this->label = $label;
        $this->data = $data;
        $this->mode = $mode;
        $this->initial = $initial;
        $this->max = $max;
        $this->min = $min;
    }

    /**
     * Builds datetime picker action structure.
     *
     * @return array Built datetime picker action structure.
     */
    public function buildTemplateAction()
    {
        return [
            'type' => ActionType::DATETIME_PICKER,
            'label' => $this->label,
            'data' => $this->data,
            'mode' => $this->mode,
            'initial' => $this->initial,
            'max' => $this->max,
            'min' => $this->min,
        ];
    }
}
