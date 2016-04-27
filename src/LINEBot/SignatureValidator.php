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
namespace LINE\LINEBot;

use LINE\LINEBot\Exception\InvalidSignatureException;

class SignatureValidator
{
    /**
     * Validate request with signature.
     *
     * @param string $json
     * @param string $channelSecret
     * @param string $signature
     * @return bool
     * @throws InvalidSignatureException When empty signature is given
     */
    public static function validateSignature($json, $channelSecret, $signature)
    {
        if (empty($signature)) {
            throw new InvalidSignatureException('Signature must not be empty');
        }
        return hash_equals(base64_encode(hash_hmac('sha256', $json, $channelSecret, true)), $signature);
    }
}
