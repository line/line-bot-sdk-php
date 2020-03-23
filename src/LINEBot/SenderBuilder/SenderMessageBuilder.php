<?php
/**
 * Copyright 2020 LINE Corporation
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

namespace LINE\LINEBot\SenderBuilder;

use LINE\LINEBot\SenderBuilder\SenderBuilder;

/**
 * A builder class for sender message.
 *
 * @package LINE\LINEBot\SenderMessageBuilder
 */
class SenderMessageBuilder implements SenderBuilder
{
    /** @var string|null */
    private $name;
    /** @var string|null */
    private $iconUrl;
    /** @var array */
    private $sender;

    /**
     * SenderMessageBuilder constructor.
     * @param string|null $name sender name. Max 20 charcters
     * @param string|null $iconUrl icon url. Max 1000 characters https only
     */
    public function __construct($name = null, $iconUrl = null)
    {
        $this->name = $name;
        $this->iconUrl = $iconUrl;
    }

    /**
     * Builds sender structure.
     *
     * @return array
     */
    public function buildSender()
    {
        if (!is_null($this->sender)) {
            return $this->sender;
        }

        $sender = [];

        if (isset($this->name)) {
            $sender['name'] = $this->name;
        }
        if (isset($this->iconUrl)) {
            $sender['iconUrl'] = $this->iconUrl;
        }

        $this->sender = $sender;

        return $this->sender;
    }
}
