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

namespace LINE\LINEBot;

/**
 * A class represents API response.
 *
 * @package LINE\LINEBot
 */
class Response
{
    /** @var int */
    private $httpStatus;

    /** @var string */
    private $body;

    /** @var string */
    private $contentType;

    /**
     * Response constructor.
     *
     * @param int $httpStatus HTTP status code of response.
     * @param string $body Request body.
     * @param string $contentType The Content-Type header of the response
     */
    public function __construct($httpStatus, $body, $contentType)
    {
        $this->httpStatus = $httpStatus;
        $this->body = $body;
        $this->contentType = $contentType;
    }

    /**
     * Returns HTTP status code of response.
     *
     * @return int HTTP status code of response.
     */
    public function getHTTPStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Returns the Content-Type header of the response
     *
     * @return string The content type of the response, or null if not set
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Returns request is succeeded or not.
     *
     * @return bool Request is succeeded or not.
     */
    public function isSucceeded()
    {
        return $this->httpStatus === 200;
    }

    /**
     * Returns raw request body.
     *
     * @return string Raw request body.
     */
    public function getRawBody()
    {
        return $this->body;
    }

    /**
     * Returns request body as array (it means, returns JSON decoded body).
     *
     * @return array Request body that is JSON decoded.
     */
    public function getJSONDecodedBody()
    {
        return json_decode($this->body, true);
    }
}
