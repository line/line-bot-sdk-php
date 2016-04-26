<?php
/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
namespace LINE\LINEBot\Receive;

use LINE\LINEBot\Constant\BotAPIChannel;
use LINE\LINEBot\SignatureValidator;

trait Receive
{
    public function isMessage()
    {
        return false;
    }

    public function isOperation()
    {
        return false;
    }

    public function isValidEvent()
    {
        $result = $this->getResult();
        $config = $this->getConfig();

        return $result['toChannel'] == $config['channelId'] &&
        $result['fromChannel'] == BotAPIChannel::RECEIVING_CHANNEL_ID &&
        $result['from'] == BotAPIChannel::RECEIVING_CHANNEL_MID;
    }

    public function getId()
    {
        return $this->getResult()['id'];
    }

    /**
     * Validate request with signature.
     *
     * @param string $json JSON request.
     * @param string $signature
     * @return bool
     */
    public function validateSignature($json, $signature)
    {
        return SignatureValidator::validateSignature($json, $this->getConfig()['channelSecret'], $signature);
    }

    abstract function getResult();

    abstract function getConfig();
}
