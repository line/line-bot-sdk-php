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

namespace LINE\Tests\LINEBot\HTTPClient;

use LINE\LINEBot\Constant\Meta;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class CurlHTTPClientTest extends \PHPUnit_Framework_TestCase
{
    private static $reqMirrorPath;
    private static $reqMirrorPort;
    private static $reqMirrorPID;

    public static function setUpBeforeClass()
    {
        CurlHTTPClientTest::$reqMirrorPath = __DIR__ . '/../../../devtool/req_mirror';

        if (file_exists(CurlHTTPClientTest::$reqMirrorPath)) {
            if (empty(CurlHTTPClientTest::$reqMirrorPort)) {
                $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                socket_bind($sock, '127.0.0.1', 0);
                socket_getsockname($sock, $address, CurlHTTPClientTest::$reqMirrorPort);
                socket_close($sock);
            }

            if (empty(CurlHTTPClientTest::$reqMirrorPID)) {
                $out = [];
                $cmd = sprintf(
                    'nohup %s --port %d > /dev/null & echo $!',
                    CurlHTTPClientTest::$reqMirrorPath,
                    CurlHTTPClientTest::$reqMirrorPort
                );
                exec($cmd, $out);
                CurlHTTPClientTest::$reqMirrorPID = $out[0];
                sleep(1); // XXX
            }
        }
    }

    public static function tearDownAfterClass()
    {
        if (!empty(CurlHTTPClientTest::$reqMirrorPID)) {
            posix_kill(CurlHTTPClientTest::$reqMirrorPID, SIGKILL);
        }
    }

    protected function setUp()
    {
        if (!file_exists(CurlHTTPClientTest::$reqMirrorPath) || empty(CurlHTTPClientTest::$reqMirrorPID)) {
            $this->fail('req_mirror server is not available. Please try to execute `make install-devtool`');
        }
    }

    public function testGet()
    {
        $curl = new CurlHTTPClient("channel-token");
        $res = $curl->get('127.0.0.1:' . CurlHTTPClientTest::$reqMirrorPort . '/foo/bar?buz=qux');
        $body = $res->getJSONDecodedBody();

        $this->assertEquals('GET', $body['Method']);
        $this->assertEquals('/foo/bar', $body['URL']['Path']);
        $this->assertEquals('buz=qux', $body['URL']['RawQuery']);
        $this->assertEquals('Bearer channel-token', $body['Header']['Authorization'][0]);
        $this->assertEquals('LINE-BotSDK-PHP/' . Meta::VERSION, $body['Header']['User-Agent'][0]);
    }

    public function testPost()
    {
        $curl = new CurlHTTPClient("channel-token");
        $res = $curl->post('127.0.0.1:' . CurlHTTPClientTest::$reqMirrorPort, ['foo' => 'bar']);
        $body = $res->getJSONDecodedBody();
        $this->assertEquals('POST', $body['Method']);
        $this->assertEquals('/', $body['URL']['Path']);
        $this->assertEquals('{"foo":"bar"}', $body['Body']);
        $this->assertEquals(13, $body['Header']['Content-Length'][0]);
        $this->assertEquals('Bearer channel-token', $body['Header']['Authorization'][0]);
        $this->assertEquals('LINE-BotSDK-PHP/' . Meta::VERSION, $body['Header']['User-Agent'][0]);
    }

    public function testPostWithEmptyBody()
    {
        $curl = new CurlHTTPClient("channel-token");
        $res = $curl->post('127.0.0.1:' . CurlHTTPClientTest::$reqMirrorPort, []);
        $body = $res->getJSONDecodedBody();
        $this->assertEquals('POST', $body['Method']);
        $this->assertEquals('/', $body['URL']['Path']);
        $this->assertEquals('', $body['Body']);
        $this->assertEquals(0, $body['Header']['Content-Length'][0]);
        $this->assertEquals('Bearer channel-token', $body['Header']['Authorization'][0]);
        $this->assertEquals('LINE-BotSDK-PHP/' . Meta::VERSION, $body['Header']['User-Agent'][0]);
    }
}
