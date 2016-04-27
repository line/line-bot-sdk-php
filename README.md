line-bot-sdk-php
==

[![Build Status](https://travis-ci.org/line/line-bot-sdk-php.svg?branch=master)](https://travis-ci.org/line/line-bot-sdk-php)
[![Latest Stable Version](https://poser.pugx.org/linecorp/line-bot-sdk/v/stable.svg)](https://packagist.org/packages/linecorp/line-bot-sdk)
[![License](https://poser.pugx.org/linecorp/line-bot-sdk/license.svg)](https://packagist.org/packages/linecorp/line-bot-sdk)

SDK of the LINE BOT API Trial for PHP.

Installation
--

The LINE BOT API SDK can be installed with [Composer](https://getcomposer.org/).

```
composer require linecorp/line-bot-sdk
```

Note
--

If you use __PHP 5.5 or lower__, please use this SDK with polyfill of [hash_equals()](http://php.net/manual/function.hash-equals.php).

e.g.

- [indigophp/hash-compat](https://packagist.org/packages/indigophp/hash-compat)

Methods
--

### Constructor

#### new LINEBot(array $args, HTTPClient $client)

Create a `LINEBot` constructor.

```php
$config = [
    'channelId' => '<your channel ID>',
    'channelSecret' => '<your channel secret>',
    'channelMid' => '<your channel MID>',
];
$bot = new LINEBot($config, new GuzzleHTTPClient($config));
```

### Sending Message

#### LINEBot#sendText($mid, $text)

Send a text message to mid(s).  
[https://developers.line.me/bot-api/api-reference#sending_message_text](https://developers.line.me/bot-api/api-reference#sending_message_text)

```php
$res = $bot->sendText(['TARGET_MID'], 'Message');
```

#### LINEBot#sendImage($mid, $imageURL, $previewURL)

Send an image to mid(s).  
[https://developers.line.me/bot-api/api-reference#sending_message_image](https://developers.line.me/bot-api/api-reference#sending_message_image)

```php
$bot->sendImage(['TARGET_MID'] 'http://example.com/image.jpg', 'http://example.com/preview.jpg');
```

#### LINEBot#sendVideo($mid, $videoURL, $previewImageURL)

Send a video to mid(s).  
[https://developers.line.me/bot-api/api-reference#sending_message_video](https://developers.line.me/bot-api/api-reference#sending_message_video)

```php
$bot->sendVideo(['TARGET_MID'], 'http://example.com/video.mp4', 'http://example.com/video_preview.jpg');
```

#### LINEBot#sendAudio($mid, $audioURL, $durationMillis)

Send a voice message to mid(s).  
[https://developers.line.me/bot-api/api-reference#sending_message_audio](https://developers.line.me/bot-api/api-reference#sending_message_audio)

```php
$bot->sendAudio(['TARGET_MID'], 'http://example.com/audio.m4a', 5000);
```

#### LINEBot#sendLocation($mid, $text, $latitude, $longitude)

Send location information to mid(s).  
[https://developers.line.me/bot-api/api-reference#sending_message_location](https://developers.line.me/bot-api/api-reference#sending_message_location)

```php
$bot->sendLocation(['TARGET_MID'], '2 Chome-21-1 Shibuya Tokyo 150-0002, Japan', 35.658240, 139.703478);
```

#### LINEBot#sendSticker($mid, $stkid, $stkpkgid, $stkver)

Send a sticker to mid(s).  
[https://developers.line.me/bot-api/api-reference#sending_message_sticker](https://developers.line.me/bot-api/api-reference#sending_message_sticker)

```php
$bot->sendSticker(['TARGET_MID'], 1, 1, 100);
```

#### LINEBot#sendRichMessage($mid, $imageURL, $altText, Markup $markup)

Send a rich message to mid(s).  
[https://developers.line.me/bot-api/api-reference#sending_rich_content_message_request](https://developers.line.me/bot-api/api-reference#sending_rich_content_message_request)

```php
$markup = (new Markup(1040))
    ->setAction('SOMETHING', 'something', 'https://line.me')
    ->addListener('SOMETHING', 0, 0, 1040, 1040);
$bot->sendRichMessage(['TARGET_MID'], 'https://example.com/image.jpg', "Alt text", $markup);
```

#### LINEBot#sendMultipleMessages($mid, MultipleMessages $multipleMessages)

Send multiple messages to mids(s).  
[https://developers.line.me/bot-api/api-reference#sending_multiple_messages_request](https://developers.line.me/bot-api/api-reference#sending_multiple_messages_request)

```php
$multipleMessages = (new \LINE\LINEBot\Message\MultipleMessages())
    ->addText('hello!')
    ->addImage('http://example.com/image.jpg', 'http://example.com/preview.jpg')
    ->addAudio('http://example.com/audio.m4a', 6000)
    ->addVideo('http://example.com/video.mp4', 'http://example.com/video_preview.jpg')
    ->addLocation('2 Chome-21-1 Shibuya Tokyo 150-0002, Japan', 35.658240, 139.703478)
    ->addSticker(1, 1, 100);
$bot->sendMultipleMessages(['TARGET_MID'], $multipleMessages);
```

### Getting Message Contents

#### LINEBot#getMessageContent($messageId, $fileHandler = null)

Retrieve the content of a user's message which is an image or video file.  
[https://developers.line.me/bot-api/api-reference#getting_message_content_request](https://developers.line.me/bot-api/api-reference#getting_message_content_request)

```php
$content = $bot->getMessageContent('1234567890');
```

#### LINEBot#getMessageContentPreview($messageId, $fileHandler = null)

Retrieve thumbnail preview of the message.  
[https://developers.line.me/bot-api/api-reference#getting_message_content_preview_request](https://developers.line.me/bot-api/api-reference#getting_message_content_preview_request)

```php
$content = $bot->getMessageContentPreview('1234567890');
```

### Getting User Profile

#### LINEBot#getUserProfile($mid)

Retrieve user profile(s) that is associated with mid(s).  
[https://developers.line.me/bot-api/api-reference#getting_user_profile_information_request](https://developers.line.me/bot-api/api-reference#getting_user_profile_information_request)

```php
$profile = $bot->getUserProfile(['TARGET_MID']);
```

### Signature Validation

#### LINEBot#validateSignature($json, $signature)

Validate signature.

```php
$isValid = $bot->validateSignature($requestJSON, 'expected-signature');
```

Run Tests
--

### Execute with `phpunit`

```
composer install
./vendor/bin/phpunit ./tests
```

### Execute `make test`

```
composer install
make
```

Hints
--

You can find some implementation examples [here](./examples).

License
--

```
Copyright 2016 LINE Corporation

LINE Corporation licenses this file to you under the Apache License,
version 2.0 (the "License"); you may not use this file except in compliance
with the License. You may obtain a copy of the License at:

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
License for the specific language governing permissions and limitations
under the License.
```

See Also
--

- [https://business.line.me/](https://business.line.me/)
- [https://developers.line.me/bot-api/overview](https://developers.line.me/bot-api/overview)
- [https://developers.line.me/bot-api/getting-started-with-bot-api-trial](https://developers.line.me/bot-api/getting-started-with-bot-api-trial)

