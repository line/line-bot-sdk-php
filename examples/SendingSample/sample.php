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
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\GuzzleHTTPClient;
use LINE\LINEBot\Message\MultipleMessages;
use LINE\LINEBot\Message\RichMessage\Markup;

require_once __DIR__ . '/../../vendor/autoload.php';

error_reporting(-1);

$setting = require('settings.php');
$channelId = $setting['channelId'];
$channelSecret = $setting['channelSecret'];
$channelMid = $setting['channelMid'];
$targetMid = $setting['targetMid'];

$config = [
    'channelId' => $channelId,
    'channelSecret' => $channelSecret,
    'channelMid' => $channelMid,
];
$sdk = new LINEBot($config, new GuzzleHTTPClient($config));

// Send a text message
$sdk->sendText([$targetMid], 'hello!');

// Send an image
$sdk->sendImage([$targetMid], 'http://example.com/image.jpg', 'http://example.com/preview.jpg');

// Send an voice message
$sdk->sendAudio([$targetMid], 'http://example.com/audio.m4a', 5000);

// Send a video
$sdk->sendVideo([$targetMid], 'http://example.com/video.mp4', 'http://example.com/video_preview.jpg');

// Send a location
$sdk->sendLocation([$targetMid], '2 Chome-21-1 Shibuya Tokyo 150-0002, Japan', 35.658240, 139.703478);

// Send a sticker
$sdk->sendSticker([$targetMid], 1, 1, 100);

// Send a rich message
$markup = (new Markup(1040))
    ->setAction('SOMETHING', 'something', 'https://line.me')
    ->addListener('SOMETHING', 0, 0, 1040, 1040);
$sdk->sendRichMessage([$targetMid], 'https://example.com/image.jpg', "Alt text", $markup);

// Send multiple messages
$multipleMessages = (new MultipleMessages())
    ->addText('hello!')
    ->addImage('http://example.com/image.jpg', 'http://example.com/preview.jpg')
    ->addAudio('http://example.com/audio.m4a', 6000)
    ->addVideo('http://example.com/video.mp4', 'http://example.com/video_preview.jpg')
    ->addLocation('2 Chome-21-1 Shibuya Tokyo 150-0002, Japan', 35.658240, 139.703478)
    ->addSticker(1, 1, 100);
$sdk->sendMultipleMessages([$targetMid], $multipleMessages);
