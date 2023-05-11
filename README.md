# LINE Messaging API SDK for PHP

[![Build Status](https://github.com/line/line-bot-sdk-php/actions/workflows/php-checks.yml/badge.svg?branch=master)](https://github.com/line/line-bot-sdk-php/actions)


## Introduction

The LINE Messaging API SDK for PHP makes it easy to develop bots using LINE Messaging API, and you can create a sample bot within minutes.


## Documentation

See the official API documentation for more information.

- English: https://developers.line.biz/en/docs/messaging-api/overview/
- Japanese: https://developers.line.biz/ja/docs/messaging-api/overview/


## Requirements

- PHP 8.1 or later


## Installation

Install the LINE Messaging API SDK using [Composer](https://getcomposer.org/).

```
$ composer require linecorp/line-bot-sdk
```

## Getting started

### Create the bot client instance

The bot client instance is a handler of the Messaging API.

```php
$client = new \GuzzleHttp\Client();
$config = new \LINE\Clients\MessagingApi\Configuration();
$config->setAccessToken('<channel access token>');
$messagingApi = new \LINE\Clients\MessagingApi\Api\MessagingApiApi(
  client: $client,
  config: $config,
);
```

You must use the Client with `GuzzleHttp\ClientInterface` implementation.

### Call API

You can call an API through the messagingApi instance.

A very simple example:

```php
$message = new TextMessage(['type' => 'text','text' => 'hello!']);
$request = new ReplyMessageRequest([
    'replyToken' => '<reply token>',
    'messages' => [$message],
]);
$response = $messagingApi->replyMessage($request);
```

This procedure sends a message to the destination that is associated with `<reply token>`.

We also support setter style.

```php
$message = (new TextMessage())
  ->setType(\LINE\Constants\MessageType::TEXT)
  ->setText('hello!');
$request = (new ReplyMessageRequest)
  ->setReplyToken('<reply token>')
  ->setMessages([$message]);
try {
  $messagingApi->replyMessage($request);
  // Success
} catch (\LINE\Clients\MessagingApi $e) {
  // Failed
  echo $e->getCode() . ' ' . $e->getResponseBody();
}
```

## Components

### Webhook

LINE's server sends user actions (such as a message, image, or location) to your bot server.
Request of that contains event(s); event is action of the user.

The following shows how the webhook is handled:

1. Receive webhook from LINE's server.
2. Parse request payload by `EventRequestParser#parseEventRequest($body, $channelSecret, $signature)`.
3. Iterate parsed events and some react as you like.

The following examples show how webhooks are handled:

- [EchoBot: Route.php](/examples/EchoBot/src/LINEBot/EchoBot/Route.php)
- [KitchenSink: Route.php](/examples/KitchenSink/src/LINEBot/KitchenSink/Route.php)

More information
--

For more information, see the [official API documents](#documentation) and PHPDoc.
If it's your first time using this library, we recommend taking a look at `examples` and the PHPDoc of `\LINE` .

Hints
--

### Examples

This repository contains two examples of how to use the LINE Messaging API.

#### [EchoBot](/examples/EchoBot)

A simple sample implementation. This application reacts to text messages that are sent from users.

#### [KitchenSink](/examples/KitchenSink)

A full-stack (and slightly complex) sample implementation. This application demonstrates a practical use of the LINE Messaging API.

### PHPDoc
https://line.github.io/line-bot-sdk-php/

This library provides PHPDoc to describe how to use the methods. You can generate the documentation using phpDocumenter using the following command.

$ wget https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.0.0/phpDocumentor.phar
$ php phpDocumentor.phar run -d src -t docs
The HTML files are generated in docs/.

### Official API documentation

[Official API documents](#documentation) shows the detail of  Messaging API and fundamental usage of SDK.

See also
--

### Laravel Support
Easy to use from Laravel.
After installed, add `LINE_BOT_CHANNEL_ACCESS_TOKEN` to `.env`

```
LINE_BOT_CHANNEL_ACCESS_TOKEN=<Channel Access Token>
```

then you can use facades like following.

```
$profile = \LINEMessagingApi::pushMessage(....);
```

Facade uses `\GuzzleHttp\Client` by default. If you want to change the config, run 

```bash
$ php artisan vendor:publish --tag=config
```

Then `line-bot.php` will be published to `config/` dir.
If you want to configure a custom header, do the following.

```php
return [
    'channel_access_token' => env('LINE_BOT_CHANNEL_ACCESS_TOKEN'),
    'channel_id' => env('LINE_BOT_CHANNEL_ID'),
    'channel_secret' => env('LINE_BOT_CHANNEL_SECRET'),
    'client' => [
        'config' => [
          'headers' => ['X-Foo' => 'Bar'],
        ],
    ],
];
```


## Help and media

FAQ: https://developers.line.biz/en/faq/

Community Q&A: https://www.line-community.me/questions

News: https://developers.line.biz/en/news/

Twitter: [@LINE_DEV](https://twitter.com/LINE_DEV)


## Versioning

This project respects semantic versioning.

See http://semver.org/


## Contributing

Please check [CONTRIBUTING](CONTRIBUTING.md) before making a contribution.

For hacking instructions, please refer [HACKING.md](/HACKING.md).


## License

```
Copyright 2016 LINE Corporation

Licensed under the Apache License, version 2.0 (the "License"); 
you may not use this file except in compliance with the License. 
You may obtain a copy of the License at

  https://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS, 
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
See the License for the specific language governing permissions and 
limitations under the License.
```
