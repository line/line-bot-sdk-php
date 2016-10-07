line-bot-sdk-tiny
==

Deadly simple SDK (subset) of the LINE Messaging API for PHP.

Description (and Motivation)
--

[line-bot-sdk-php](https://github.com/line/line-bot-sdk-php) is a full-stack implementation of the LINE Messaging API SDK.
That SDK uses serious OOP interface and functions. It provides API client, message builder, HTTP client, event parser and more useful components.

On the other hand, line-bot-sdk-tiny provides simple interface and functions. This SDK contains a part of the API function (not full function).

This SDK is provided by only one file, so it is easy to bring `LINEBotTiny.php` to your environment and require that from your script.
And of course you can also copy & paste this SDK.

Example
--

See [echo_bot](./echo_bot.php).

When you run this example, please never forget to set your channel access token and channel secret.

Requirements
--

PHP 5.4 or later

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

