line-bot-sample
==

A full-stack LINE Messaging API sample implementation. This sample shows you a practical use of the LINE Messaging API.

This project uses the [Slim framework](http://www.slimframework.com/).

Getting started
--

```
$ curl -sS https://getcomposer.org/installer | php # Install composer.phar
$ ./composer.phar install
$ $EDITOR ./src/LINEBot/KitchenSink/Setting.php # <= edit your bot information
$ ./run.sh 8080
```

Hints
--

### [public/index.php](./public/index.php)

Entry point of this application.

### [src/LINEBot/KitchenSink/Route.php](./src/LINEBot/KitchenSink/Route.php)

Core logic of this application using the LINE Messaging API.

### [Event handlers](./src/LINEBot/KitchenSink/EventHandler)

Handlers for LINE Messaging API events.

Notes
--

### Temporary directory

This application downloads multimedia files to `./public/static/tmpdir/`.
The `./run.sh` wrapper removes this content on shut down of the PHP server.

### Base URL

This application serves downloaded multimedia files.

By default, this app constructs URLs for the content with `\Slim\Http\Request->getUri()->getBaseUrl()` as the base URL.
Unfortunately this process doesn't work correctly if this app runs on a reverse-proxied environment.

If you encounter this problem, configure the base URL to whatever you like using [UrlBuilder](./src/LINEBot/KitchenSink/EventHandler/MessageHandler/Util/UrlBuilder.php)

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
