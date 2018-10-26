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

namespace LINE\Tests\LINEBot\Util;

use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\Response;
use PHPUnit\Framework\TestCase;

class DummyHttpClient implements HTTPClient
{
    /** @var \PHPUnit\Framework\TestCase */
    private $testRunner;
    /** @var \Closure */
    private $mock;

    public function __construct(TestCase $testRunner, \Closure $mock)
    {
        $this->testRunner = $testRunner;
        $this->mock = $mock;
    }

    /**
     * @param string $url
     * @param array $data Optional
     * @param array $headers
     * @return Response
     */
    public function get($url, array $data = [], array $headers = [])
    {
        $ret = call_user_func($this->mock, $this->testRunner, 'GET', $url, is_null($data) ? [] : $data);
        return new Response(200, json_encode($ret));
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $headers Optional
     * @return Response
     */
    public function post($url, array $data, array $headers = null)
    {
        $ret = call_user_func($this->mock, $this->testRunner, 'POST', $url, $data, $headers);
        return new Response(200, json_encode($ret));
    }

    /**
     * @param string $url
     * @param array|null $data
     * @return Response
     */
    public function delete($url, $data = null)
    {
        $ret = call_user_func($this->mock, $this->testRunner, 'DELETE', $url, is_null($data) ? [] : $data);
        return new Response(200, json_encode($ret));
    }
}
