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
use PHPUnit\Framework\TestCase;

class CurlHTTPClientTest extends TestCase
{
    private static $reqMirrorPort;
    private static $reqMirrorPID;

    public static function setUpBeforeClass()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            return;
        }

        if (empty(CurlHTTPClientTest::$reqMirrorPort)) {
            $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_bind($sock, '127.0.0.1', 0);
            socket_getsockname($sock, $address, CurlHTTPClientTest::$reqMirrorPort);
            socket_close($sock);
        }

        if (empty(CurlHTTPClientTest::$reqMirrorPID)) {
            $out = [];
            $cmd = sprintf(
                'nohup %s:%d %s > /dev/null & echo $!',
                'php -S 127.0.0.1',
                CurlHTTPClientTest::$reqMirrorPort,
                __DIR__ . '/../../req_mirror.php'
            );
            exec($cmd, $out);
            CurlHTTPClientTest::$reqMirrorPID = $out[0];
            sleep(1); // Need to wait server to be ready to accept connection
        }
    }

    public static function tearDownAfterClass()
    {
        if (!empty(CurlHTTPClientTest::$reqMirrorPID)) {
            posix_kill(CurlHTTPClientTest::$reqMirrorPID, 9);
        }
    }

    protected function setUp()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped("These tests don't support Windows environment for now.");
        }

        if (empty(CurlHTTPClientTest::$reqMirrorPID)) {
            $this->fail('Mirror server looks dead');
        }
    }

    public function testGet()
    {
        $curl = new CurlHTTPClient("channel-token");
        $res = $curl->get('127.0.0.1:' . CurlHTTPClientTest::$reqMirrorPort . '/foo/bar?buz=qux');
        $body = $res->getJSONDecodedBody();
        $this->assertNotNull($body);
        $this->assertEquals('GET', $body['_SERVER']['REQUEST_METHOD']);
        $this->assertEquals('/foo/bar', $body['_SERVER']['SCRIPT_NAME']);
        $this->assertEquals('', $body['Body']);
        $this->assertEquals('buz=qux', $body['_SERVER']['QUERY_STRING']);
        $this->assertEquals('Bearer channel-token', $body['_SERVER']['HTTP_AUTHORIZATION']);
        $this->assertEquals('LINE-BotSDK-PHP/' . Meta::VERSION, $body['_SERVER']['HTTP_USER_AGENT']);
    }

    public function testPost()
    {
        $curl = new CurlHTTPClient("channel-token");
        $res = $curl->post('127.0.0.1:' . CurlHTTPClientTest::$reqMirrorPort, ['foo' => 'bar']);
        $body = $res->getJSONDecodedBody();
        $this->assertNotNull($body);
        $this->assertEquals('POST', $body['_SERVER']['REQUEST_METHOD']);
        $this->assertEquals('/', $body['_SERVER']['SCRIPT_NAME']);
        $this->assertEquals('{"foo":"bar"}', $body['Body']);
        $this->assertEquals(13, $body['_SERVER']['HTTP_CONTENT_LENGTH']);
        $this->assertEquals('Bearer channel-token', $body['_SERVER']['HTTP_AUTHORIZATION']);
        $this->assertEquals('LINE-BotSDK-PHP/' . Meta::VERSION, $body['_SERVER']['HTTP_USER_AGENT']);
    }

    public function testDelete()
    {
        $curl = new CurlHTTPClient("channel-token");
        $res = $curl->delete('127.0.0.1:' . CurlHTTPClientTest::$reqMirrorPort);
        $body = $res->getJSONDecodedBody();
        $this->assertNotNull($body);
        $this->assertEquals('DELETE', $body['_SERVER']['REQUEST_METHOD']);
        $this->assertEquals('/', $body['_SERVER']['SCRIPT_NAME']);
        $this->assertEquals('', $body['Body']);
        $this->assertEquals('Bearer channel-token', $body['_SERVER']['HTTP_AUTHORIZATION']);
        $this->assertEquals('LINE-BotSDK-PHP/' . Meta::VERSION, $body['_SERVER']['HTTP_USER_AGENT']);
    }

    public function testPostWithEmptyBody()
    {
        $curl = new CurlHTTPClient("channel-token");
        $res = $curl->post('127.0.0.1:' . CurlHTTPClientTest::$reqMirrorPort, []);
        $body = $res->getJSONDecodedBody();
        $this->assertNotNull($body);
        $this->assertEquals('POST', $body['_SERVER']['REQUEST_METHOD']);
        $this->assertEquals('/', $body['_SERVER']['SCRIPT_NAME']);
        $this->assertEquals('', $body['Body']);
        $this->assertEquals(0, $body['_SERVER']['HTTP_CONTENT_LENGTH']);
        $this->assertEquals('Bearer channel-token', $body['_SERVER']['HTTP_AUTHORIZATION']);
        $this->assertEquals('LINE-BotSDK-PHP/' . Meta::VERSION, $body['_SERVER']['HTTP_USER_AGENT']);
    }

    public function testPostImage()
    {
        $base64EncodedImage = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEX/TQBcNTh/AAAAAXRSTlPM0';
        $base64EncodedImage .= 'jRW/QAAAApJREFUeJxjYgAAAAYAAzY3fKgAAAAASUVORK5CYII=';
        $tmpfile = tmpfile();
        $metaData = stream_get_meta_data($tmpfile);
        fwrite($tmpfile, base64_decode($base64EncodedImage));
        $curl = new CurlHTTPClient("channel-token");
        $res = $curl->post(
            '127.0.0.1:' . CurlHTTPClientTest::$reqMirrorPort,
            [
                '__file' => $metaData['uri'],
                '__type' => 'image/png'
            ],
            [ 'Content-Type: image/png' ]
        );
        $body = $res->getJSONDecodedBody();
        $this->assertNotNull($body);
        $this->assertEquals('POST', $body['_SERVER']['REQUEST_METHOD']);
        $this->assertEquals('/', $body['_SERVER']['SCRIPT_NAME']);
        $this->assertEquals($base64EncodedImage, $body['Body']);
        $this->assertEquals(95, $body['_SERVER']['HTTP_CONTENT_LENGTH']);
        $this->assertEquals('Bearer channel-token', $body['_SERVER']['HTTP_AUTHORIZATION']);
        $this->assertEquals('LINE-BotSDK-PHP/' . Meta::VERSION, $body['_SERVER']['HTTP_USER_AGENT']);
        fclose($tmpfile);
    }
}
