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

namespace LINE\LINEBot\HTTPClient;

/**
 * cURL session manager
 *
 * @package LINE\LINEBot\HTTPClient
 */
class Curl
{
    /** @var resource */
    private $ch;

    /**
     * Initialize a cURL session
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->ch = curl_init($url);
    }

    /**
     * Set multiple options for a cURL transfer
     *
     * @param array $options Returns TRUE if all options were successfully set. If an option could not be
     * successfully set, FALSE is immediately returned, ignoring any future options in the options array.
     * @return bool
     */
    public function setoptArray(array $options)
    {
        return curl_setopt_array($this->ch, $options);
    }

    /**
     * Perform a cURL session
     *
     * @return bool Returns TRUE on success or FALSE on failure. However, if the CURLOPT_RETURNTRANSFER
     * option is set, it will return the result on success, FALSE on failure.
     */
    public function exec()
    {
        return curl_exec($this->ch);
    }

    /**
     * Gets information about the last transfer.
     *
     * @return array
     */
    public function getinfo()
    {
        return curl_getinfo($this->ch);
    }

    /**
     * @return int Returns the error number or 0 (zero) if no error occurred.
     */
    public function errno()
    {
        return curl_errno($this->ch);
    }

    /**
     * @return string Returns the error message or '' (the empty string) if no error occurred.
     */
    public function error()
    {
        return curl_error($this->ch);
    }

    /**
     * Closes a cURL session and frees all resources. The cURL handle, ch, is also deleted.
     */
    public function __destruct()
    {
        curl_close($this->ch);
    }
}
