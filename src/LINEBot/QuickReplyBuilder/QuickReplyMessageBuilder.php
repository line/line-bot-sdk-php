<?php
/**
 * Copyright 2018 LINE Corporation
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

namespace LINE\LINEBot\QuickReplyBuilder;

use LINE\LINEBot\QuickReplyBuilder;

/**
 * A builder class for quick reply message.
 *
 * @package LINE\LINEBot\QuickReplyBuilder
 */
class QuickReplyMessageBuilder implements QuickReplyBuilder
{
    /** @var QuickReplyButtonBuilder[] */
    private $buttonBuilders;

    /** @var array */
    private $quickReply;

    /**
     * QuickReplyMessageBuilder constructor.
     * Quick reply button objects. Max: 13 objects
     *
     * @param array $buttonBuilders
     */
    public function __construct(array $buttonBuilders)
    {
        $this->buttonBuilders = $buttonBuilders;
    }

    /**
     * Builds button of quick reply structure.
     *
     * @return array
     */
    public function buildQuickReply()
    {
        if (!empty($this->quickReply)) {
            return $this->quickReply;
        }

        $items = [];

        foreach ($this->buttonBuilders as $buttonBuilder) {
            $items[] = $buttonBuilder->buildQuickReplyButton();
        }

        $this->quickReply = ['items' => $items];

        return $this->quickReply;
    }
}
