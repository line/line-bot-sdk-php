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
    /** @var string[] */
    private $headers;

    /**
     * Response constructor.
     *
     * @param int $httpStatus HTTP status code of response.
     * @param string $body Request body.
     * @param string[] $headers
     */
    public function __construct($httpStatus, $body, $headers = [])
    {
        $this->httpStatus = $httpStatus;
        $this->body = $body;
        $this->headers = $headers;
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
     * Returns request is succeeded or not.
     *
     * @return bool Request is succeeded or not.
     */
    public function isSucceeded()
    {
        return 200 <= $this->httpStatus && $this->httpStatus <= 299;
    }

    /**
     * Returns raw response body.
     *
     * @return string Raw request body.
     */
    public function getRawBody()
    {
        return $this->body;
    }

    /**
     * Returns response body as array (it means, returns JSON decoded body).
     *
     * @return array Request body that is JSON decoded.
     */
    public function getJSONDecodedBody()
    {
        return json_decode($this->body, true);
    }

    /**
     * Returns the value of the specified response header.
     *
     * @param string $name A String specifying the header name.
     * @return string|null A response header string, or null if the response does not have a header of that name.
     */
    public function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }
        return null;
    }

    /**
     * Returns all of response headers.
     *
     * @return string[] All of the response headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
