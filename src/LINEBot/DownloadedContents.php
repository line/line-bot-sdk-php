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

class DownloadedContents
{
    /** @var resource */
    private $fileHandler;
    /** @var array */
    private $headers;

    /**
     * DownloadedContents constructor.
     *
     * @param resource $fileHandler
     * @param array $headers
     */
    public function __construct($fileHandler, $headers)
    {
        $this->fileHandler = $fileHandler;
        $this->headers = $headers;
    }

    /**
     * Get file handler.
     *
     * @return resource
     */
    public function getFileHandle()
    {
        return $this->fileHandler;
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
