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

namespace LINE\Tests\LINEBot;

use LINE\LINEBot\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testGetHeader()
    {
        $response = new Response(200, '{"body":"text"}', [
            'Content-Type' => 'application/json',
            'Content-Length' => '15',
        ]);
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->assertEquals('15', $response->getHeader('Content-Length'));
        $this->assertNull($response->getHeader('Not-Exists'));
    }

    public function testGetHeaders()
    {
        $response = new Response(200, '{"body":"text"}', [
            'Content-Type' => 'application/json',
            'Content-Length' => '15',
        ]);
        $headers = $response->getHeaders();
        $this->assertEquals(2, count($headers));
        $this->assertEquals('application/json', $headers['Content-Type']);
        $this->assertEquals('15', $headers['Content-Length']);
    }
}
