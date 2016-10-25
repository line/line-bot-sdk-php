line-bot-sdk-tiny
==

A very simple SDK (subset) for the LINE Messaging API for PHP.

Description (and motivation)
--

[line-bot-sdk-php](https://github.com/line/line-bot-sdk-php) is a full-stack implementation of the LINE Messaging API SDK, which uses an OOP interface and functions. It provides an API client, a message builder, an HTTP client, an event parser and other useful components.

On the other hand, line-bot-sdk-tiny provides a simple interface and functions. It contains a part of the API functions (not full function).

This SDK contains only one file, so it is easy to add `LINEBotTiny.php` to your environment and require that from your script.
And of course, you can also copy and paste this SDK.

Example
--

See [echo_bot](./echo_bot.php).

When running this example, make sure that you have set your Channel access token and Channel secret.

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
