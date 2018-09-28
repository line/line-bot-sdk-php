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

namespace LINE\LINEBot\Event;

use LINE\LINEBot\Constant\ResultType;

class AccountLinkEvent extends BaseEvent
{

    /**
     * AccountLinkEvent constructor.
     * @param array $event
     */
    public function __construct(array $event)
    {
        parent::__construct($event);
    }

    /**
     * Return result of account link request
     * is it success or failed
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->event["link"]["result"];
    }

    /**
     * Get the user nonce
     *
     * @return mixed
     */
    public function getNonce()
    {
        return $this->event["link"]["nonce"];
    }

    /**
     * Check is the account link success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getResult() == ResultType::OK;
    }

    /**
     * Check is the account link failed
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->getResult() == ResultType::FAILED;
    }
}
