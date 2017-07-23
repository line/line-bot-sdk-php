line-bot-sdk-php
==

[![Build Status](https://travis-ci.org/line/line-bot-sdk-php.svg?branch=master)](https://travis-ci.org/line/line-bot-sdk-php)

SDK of the LINE Messaging API for PHP.

About the LINE Messaging API
--

See the official API documentation for more information.

English: [https://devdocs.line.me/en/](https://devdocs.line.me/en/)<br>
Japanese: [https://devdocs.line.me/ja/](https://devdocs.line.me/ja/)

Installation
--

Install the LINE Messaging API SDK using [Composer](https://getcomposer.org/).

```
$ composer require linecorp/line-bot-sdk
```

Getting started
--

### Create the bot client instance

The bot client instance is a handler of the Messaging API.

```php
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('<channel access token>');
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '<channel secret>']);
```

The constructor of the bot client requires an instance of `HTTPClient`.
This library provides `CurlHTTPClient` by default.

### Call API

You can call an API through the bot client instance.

A very simple example:

```php
$response = $bot->replyText('<reply token>', 'hello!');
```

This procedure sends a message to the destination that is associated with `<reply token>`.

A more advanced example:

```php
$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');
$response = $bot->replyMessage('<reply token>', $textMessageBuilder);
if ($response->isSucceeded()) {
    echo 'Succeeded!';
    return;
}

// Failed
echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
```

`LINEBot#replyMessage()` takes the reply token and `MessageBuilder`.
The method sends a message that is built by `MessageBuilder` to the destination.

Components
--

### MessageBuilder

The type of message that is sent depends on the type of instance of `MessageBuilder`.
For example, the method sends a text message if you pass `TextMessageBuilder` and it sends an image message if you pass `ImageMessageBuilder`.

For more detailed information on `MessageBuilder`, see `\LINE\LINEBot\MessageBuilder` and the namespace.

Other methods that take `MessageBuilder` behave in the same way.

### Response

Methods that call API returns `Response`. Response has three methods;

- `Response#isSucceeded()`
- `Response#getHTTPStatus()`
- `Response#getRawBody()`
- `Response#getJSONDecodedBody()`
- `Response#getHeader($name)`
- `Response#getHeaders()`

You can use these methods to check the response status and take response body.

#### `Response#isSucceeded()`

Returns a Boolean value. The return value represents whether the request succeeded or not.

#### `Response#getHTTPStatus()`

Returns the HTTP status code of a response.

#### `Response#getRawBody()`

Returns the body of the response as raw data (a byte string).

#### `Response#getJSONDecodedBody()`

Returns the body that is decoded in JSON. This body is an array.

#### `Response#getHeader($name)`

This method returns a response header string, or null if the response does not have a header of that name.

#### `Response#getHeaders()`

This method returns all of the response headers as string array.

### Webhook

LINE's server sends user actions (such as a message, image, or location) to your bot server.
Request of that contains event(s); event is action of the user.

The following shows how the webhook is handled:

1. Receive webhook from LINE's server.
2. Parse request payload by `LINEBot#parseEventRequest($body, $signature)`.
3. Iterate parsed events and some react as you like.

The following examples show how webhooks are handled:

- [EchoBot: Route.php](/examples/EchoBot/src/LINEBot/EchoBot/Route.php)
- [KitchenSink: Route.php](/examples/KitchenSink/src/LINEBot/KitchenSink/Route.php)

More information
--

For more information, see the [official API documents](#about-the-line-messaging-api) and PHPDoc.
If it's your first time using this library, we recommend taking a look at `examples` and the PHPDoc of `\LINE\LINEBot`.

Hints
--

### Examples

This repository contains two examples of how to use the LINE Messaging API.

#### [EchoBot](/examples/EchoBot)

A simple sample implementation. This application reacts to text messages that are sent from users.

#### [KitchenSink](/examples/KitchenSink)

A full-stack (and slightly complex) sample implementation. This application demonstrates a practical use of the LINE Messaging API.

### PHPDoc

[https://line.github.io/line-bot-sdk-php/](https://line.github.io/line-bot-sdk-php/)

This library provides PHPDoc to describe how to use the methods. You can generate the documentation using [apigen](http://www.apigen.org/) using the following command.

```
$ make doc
```

The HTML files are generated in `docs/`.

### Official API documentation

[Official API documents](#about-the-line-messaging-api) shows the detail of  Messaging API and fundamental usage of SDK.

Notes
--

### How to switch the HTTP client implementation

1. Implement `\LINE\LINEBot\HTTPClient`
2. Pass the implementation to the constructor of `\LINE\LINEBot`

Please refer [CurlHTTPClient](/src/LINEBot/HTTPClient/CurlHTTPClient.php) that is the default HTTP client implementation.

Requirements
--

- PHP 5.4 or later

For SDK developers
--

Please refer [HACKING.md](/HACKING.md).

See also
--

### [line-bot-sdk-tiny](./line-bot-sdk-tiny)

A very simple SDK (subset) for the LINE Messaging API for PHP.
line-bot-sdk-tiny provides a simple interface and functions which makes it a good way to learn how to use the LINE Messaging API.

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
