line-bot-sdk-php
==

[![Build Status](https://travis-ci.org/line/line-bot-sdk-php.svg?branch=master)](https://travis-ci.org/line/line-bot-sdk-php)

SDK of the LINE Messaging API for PHP.

About LINE Messaging API
--

Please refer to the official API documents for details.

en: [https://devdocs.line.me/en/](https://devdocs.line.me/en/)

ja: [https://devdocs.line.me/ja/](https://devdocs.line.me/ja/)

Installation
--

The LINE messaging API SDK can be installed with [Composer](https://getcomposer.org/).

```
$ composer require linecorp/line-bot-sdk
```

Getting started
--

### Create the bot client instance

Instance of bot client is a handler of the Messaging API.

```php
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('<channel access token>');
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '<channel secret>']);
```

The constructor of bot client requires an instance of `HTTPClient`.
This library provides `CurlHTTPClient` as default.

### Call API

You can call API through the bot client instance.

Deadly simple sample is following;

```php
$response = $bot->replyText('<reply token>', 'hello!');
```

This procedure sends a message to the destination that is associated with `<reply token>`.

More advanced sample is below;

```php
$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');
$response = $bot->replyMessage('<reply token>', $textMessageBuilder);
if ($response->isSucceeded()) {
    echo 'Succeeded!';
    return;
}

// Failed
echo $response->getHTTPStatus . ' ' . $response->getRawBody();
```

`LINEBot#replyMessage()` takes reply token and `MessageBuilder`.
This method sends message that is built by `MessageBuilder` to the destination.

Components
--

### MessageBuilder

Type of message depends on the type of instance of `MessageBuilder`.
That means this method sends text message if you pass `TextMessageBuilder`,
on the other hand it sends image message if you pass `ImaageMessageBuilder`.

If you want detail information of `MessageBuilder`, please refer `\LINE\LINEBot\MessageBuilder` and the namespace.

Other methods that take `MessageBuilder` behave the same.

### Response

Methods that call API returns `Response`.  Response has three methods;

- `Response#isSucceeded()`
- `Response#getHTTPStatus()`
- `Response#getRawBody()`
- `Response#getJSONDecodedBody()`
- `Response#getHeader($name)`
- `Response#getHeaders()`

You can use these method to check response status and take response body.

#### `Response#isSucceeded()`

This method returns the boolean value. Return value represents "request is succeeded or not".

#### `Response#getHTTPStatus()`

This method returns the HTTP status code of response.

#### `Response#getRawBody()`

This method returns the body of response as raw (i.e. byte string).

#### `Response#getJSONDecodedBody()`

This method returns the body that is decoded as JSON. This body is an array.

#### `Response#getHeader($name)`

This method returns a response header string, or null if the response does not have a header of that name.

#### `Response#getHeaders()`

This method returns all of the response headers as string array.

### Webhook

LINE's server sends user action (message, image, location and etc.) to your bot server.
Request of that contains event(s); event is action of the user.

Flow of webhook handling is like following;

1. Receive webhook from LINE's server.
2. Parse request payload by `LINEBot#parseEventRequest($body, $signature)`.
3. Iterate parsed events and some react as you like.

We provides examples of this flow:

- [EchoBot: Route.php](/examples/EchoBot/src/LINEBot/EchoBot/Route.php)
- [KitchenSink: Route.php](/examples/KitchenSink/src/LINEBot/KitchenSink/Route.php)

More information
--

Please check [official API documents](#about-line-messaging-api) and PHPDoc.
If you first time to use this library, we recommend to see `examples` and PHPDoc of `\LINE\LINEBot`.

Hints
--

### Examples

This repository contains two examples of LINE Messaging API.

#### [EchoBot](/examples/EchoBot)

A simple sample implementation. This application reacts to text message that is from user.

#### [KitchenSink](/examples/KitchenSink)

A full-stack (and a bit complex) sample implementation. That will show you practical usage of LINE Messaging API.

### PHPDoc

This library provides PHPDoc. That will helps you to know usages of methods.

This library can generate pretty documents by [apigen](http://www.apigen.org/). Please try:

```
$ make doc
```

When HTML documents will be put on `docs/`.

### Official API documents

[Official API documents](#about-line-messaging-api) shows the detail of Messaging API and fundamental usage of SDK.

Notes
--

### How to switch HTTP client implementation?

1. Implement `\LINE\LINEBot\HTTPClient`
2. Pass the implementation to the constructor of `\LINE\LINEBot`

Please refer [CurlHTTPClient](/src/LINEBot/HTTPClient/CurlHTTPClient.php) that is the default HTTP client implementation.

Requirements
--

- PHP 5.6 or later

For SDK developers
--

### How to run tests?

Please use `make test`.

### How to execute [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)?

Please use `make phpcs`.

### How to execute [PHPMD](https://phpmd.org/)?

Please use `make phpmd`.

### How to execute them all??

`make`

See Also
--

### [line-bot-sdk-tiny](./line-bot-sdk-tiny)

Deadly simple SDK (subset) of the LINE Messaging API for PHP.
line-bot-sdk-tiny provides simple interface and functions so it is useful to try and learn the LINE Messaging API.

License
--

```
Copyright 2016 LINE Corporation

LINE Corporation licenses this file to you under the Apache License,
version 2.0 (the "License"); you may not use this file except in compliance
with the License. You may obtain a copy of the License at:

  https://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
License for the specific language governing permissions and limitations
under the License.
```

