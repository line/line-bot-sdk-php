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

class DummyHttpClient implements HTTPClient
{
    /** @var \PHPUnit_Framework_TestCase */
    private $testRunner;
    /** @var \Closure */
    private $mock;

    public function __construct(\PHPUnit_Framework_TestCase $testRunner, \Closure $mock)
    {
        $this->testRunner = $testRunner;
        $this->mock = $mock;
    }

    /**
     * @param string $url
     * @return Response
     */
    public function get($url)
    {
        $ret = call_user_func($this->mock, $this->testRunner, 'GET', $url, []);
        return new Response(200, json_encode($ret));
    }

    /**
     * @param string $url
     * @param array $data
     * @return Response
     */
    public function post($url, array $data)
    {
        $ret = call_user_func($this->mock, $this->testRunner, 'POST', $url, $data);
        return new Response(200, json_encode($ret));
    }
}
