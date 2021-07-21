<?php

/**
 * Copyright 2021 LINE Corporation
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

namespace LINE\LINEBot\Event\MessageEvent;

class MentioneeInfo
{
    /** @var int */
    private $index;
    /** @var int */
    private $length;
    /** @var string */
    private $userId;

    /**
     * Mentionee Info Constructor
     *
     * @param array $mentioneeInfo
     */
    public function __construct($mentioneeInfo)
    {
        $this->index = $mentioneeInfo['index'];
        $this->length = $mentioneeInfo['length'];
        if (isset($mentioneeInfo['userId'])) {
            $this->userId = $mentioneeInfo['userId'];
        }
    }

    /**
     * Returns Index position of the user mention for a character.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Returns length of the text of the mentioned user.
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Returns User ID of the mentioned user.
     *
     * @return string|null
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
