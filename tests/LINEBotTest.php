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
namespace LINE\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\GuzzleHTTPClient;

class LINEBotTest extends \PHPUnit_Framework_TestCase
{
    private static $config = [
        'channelId' => '1000000000',
        'channelSecret' => 'testsecret',
        'channelMid' => 'TEST_MID',
    ];

    /**
     * @expectedException \LINE\LINEBot\Exception\LINEBotAPIException
     */
    public function testLINEBotAPIExceptionCausedByEmptyResponseBody()
    {
        $mock = new MockHandler([
            new Response(200, []), // missing body
        ]);
        $mockHandler = HandlerStack::create($mock);

        $sdk = new LINEBot(
            $this::$config,
            new GuzzleHTTPClient(array_merge($this::$config, ['handler' => $mockHandler]))
        );

        $sdk->sendText(['DUMMY_MID'], 'hello!');
    }

    /**
     * @expectedException \LINE\LINEBot\Exception\LINEBotAPIException
     */
    public function textLINEBotAPIExceptionCausedByInvalidResponseBody()
    {
        $mock = new MockHandler([
            new Response(200, [], 'I AM NOT A JSON'), // not JSON
        ]);
        $mockHandler = HandlerStack::create($mock);

        $sdk = new LINEBot(
            $this::$config,
            new GuzzleHTTPClient(array_merge($this::$config, ['handler' => $mockHandler]))
        );

        $sdk->sendText(['DUMMY_MID'], 'hello!');
    }
}
