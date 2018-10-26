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

namespace LINE\LINEBot\Event;

/**
 * A class that represents the event of account link.
 *
 * Event object for when a user has linked his/her LINE account with a provider's service account.
 * You can reply to account link events.  If the link token has expired or has already been used,
 *  no webhook event will be sent and the user will be shown an error.
 *
 * @package LINE\LINEBot\Event
 */
class AccountLinkEvent extends BaseEvent
{
    const RESULT_OK = 'ok';
    const RESULT_FAILED = 'failed';

    /**
     * AccountLinkEvent constructor.
     *
     * @param array $event
     */
    public function __construct($event)
    {
        parent::__construct($event);
    }

    /**
     * Gets the result of the link event
     * One of the following values to indicate whether the link was successful or not.
     *   ok: Indicates the link was successful.
     *   failed: Indicates the link failed for any reason, such as due to a user impersonation.
     *
     * @see AccountLinkEvent::isSuccess()  To get result of this event has succeed or not
     * @see AccountLinkEvent::isFailed()   To get result of this event has failed or not
     * @return string
     */
    public function getResult()
    {
        return $this->event['link']['result'];
    }

    /**
     * Gets the nonce generated from the user ID on the provider's service.
     *
     * @return string
     */
    public function getNonce()
    {
        return $this->event['link']['nonce'];
    }

    /**
     * Returns the account link has success or not
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getResult() == self::RESULT_OK;
    }

    /**
     * Returns the account link has failed or not
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->getResult() == self::RESULT_FAILED;
    }
}
