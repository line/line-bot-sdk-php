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
namespace LINE\LINEBot\HTTPClient;

interface HTTPClient
{
    /**
     * Send get request with credential headers.
     *
     * @param string $url Destination URL to send.
     * @return array
     */
    public function get($url);

    /**
     * Send post request with credential headers.
     *
     * @param string $url Destination URL to send.
     * @param array $data Request body
     * @return array
     */
    public function post($url, array $data);

    /**
     * Download contents.
     *
     * @param string $url Contents URL.
     * @param resource $fileHandler File handler to store contents temporally.
     */
    public function downloadContents($url, $fileHandler = null);
}
