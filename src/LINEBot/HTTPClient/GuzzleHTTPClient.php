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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Stream\Stream;
use LINE\LINEBot;
use LINE\LINEBot\DownloadedContents;
use LINE\LINEBot\Exception\ContentsDownloadingFailedException;
use LINE\LINEBot\Exception\JSONEncodingException;
use LINE\LINEBot\Exception\LINEBotAPIException;

class GuzzleHTTPClient implements HTTPClient
{
    private $channelId;
    private $channelSecret;
    private $channelMid;
    /** @var Client */
    private $guzzle;

    /**
     * Client constructor.
     *
     * @param array $args Parameters of bot and client.
     * You can also control {@link https://github.com/guzzle/guzzle guzzle} parameter through this argument.
     */
    public function __construct(array $args)
    {
        $guzzleConfig = isset($args['httpClientConfig']) ? $args['httpClientConfig'] : [];

        if (!isset($guzzleConfig['headers']['User-Agent'])) {
            $guzzleConfig['headers']['User-Agent'] = 'LINE-BotSDK/' . LINEBot::VERSION;
        }

        if (!isset($guzzleConfig['timeout'])) {
            $guzzleConfig['timeout'] = 3;
        }

        $this->channelId = $args['channelId'];
        $this->channelSecret = $args['channelSecret'];
        $this->channelMid = $args['channelMid'];

        $this->guzzle = new Client($args);
    }

    /**
     * Send get request with credential headers.
     *
     * @param string $url Destination URL to send.
     * @return array
     * @throws LINEBotAPIException When request is failed or received invalid response.
     */
    public function get($url)
    {
        try {
            $res = $this->guzzle->get($url, ['headers' => $this->credentials()]);
        } catch (BadResponseException $e) {
            $res = $e->getResponse();
        }

        $resContent = $res->getBody();
        $resStatus = $res->getStatusCode();

        if (!$resContent || !preg_match('/\A{.+}\z/u', $resContent)) {
            throw new LINEBotAPIException("LINE BOT API error: $resStatus");
        }

        $ret = json_decode($resContent, true);
        if ($ret === null) {
            throw new LINEBotAPIException("LINE BOT API error: $resStatus");
        }
        return $ret;
    }

    /**
     * Send post request with credential headers.
     *
     * @param string $url Destination URL to send.
     * @param array $data Request body
     * @return array
     * @throws JSONEncodingException When invalid request has come.
     * @throws LINEBotAPIException When request is failed or received invalid response.
     */
    public function post($url, array $data)
    {
        $json = json_encode($data);
        if ($json === false) {
            throw new JSONEncodingException("Failed to encode request JSON");
        }

        $headers = array_merge($this->credentials(), [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => strlen($json),
        ]);

        try {
            $res = $this->guzzle->post($url, [
                'headers' => $headers,
                'body' => $json,
            ]);
        } catch (BadResponseException $e) {
            $res = $e->getResponse();
        }

        $resContent = $res->getBody();
        $resStatus = $res->getStatusCode();

        if (!$resContent || !preg_match('/\A{.+}\z/u', $resContent)) {
            throw new LINEBotAPIException("LINE BOT API error: $resStatus");
        }

        $ret = json_decode($resContent, true);
        if ($ret === null) {
            throw new LINEBotAPIException("LINE BOT API error: $resStatus");
        }

        $ret['httpStatus'] = $resStatus;
        return $ret;
    }

    /**
     * Download contents.
     *
     * @param string $url Contents URL.
     * @param resource $fileHandler File handler to store contents temporally.
     * @return DownloadedContents
     * @throws ContentsDownloadingFailedException When failed to download contents.
     */
    public function downloadContents($url, $fileHandler = null)
    {
        if ($fileHandler === null) {
            $fileHandler = tmpfile();
        }
        $stream = Stream::factory($fileHandler);

        try {
            $res = $this->guzzle->get($url, [
                'save_to' => $stream,
                'headers' => $this->credentials(),
            ]);
        } catch (BadResponseException $e) {
            $res = $e->getResponse();
        }

        $resStatus = $res->getStatusCode();
        if ($resStatus !== 200) {
            $resContent = $res->getBody();
            throw new ContentsDownloadingFailedException(
                "LINE BOT API contents_download error: $resStatus $url\ncontent=$resContent"
            );
        }

        return new DownloadedContents($stream->detach(), $res->getHeaders());
    }

    private function credentials()
    {
        return [
            'X-Line-ChannelID' => $this->channelId,
            'X-Line-ChannelSecret' => $this->channelSecret,
            'X-Line-Trusted-User-With-ACL' => $this->channelMid,
        ];
    }
}
