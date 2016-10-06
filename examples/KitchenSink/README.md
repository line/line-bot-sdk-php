line-bot-sample
==

A full-stack LINE Messaging API sample implementation. This sample will show you practical usage of LINE Messaging API.

This project is using [Slim framework](http://www.slimframework.com/).

Getting Started
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

Core logic of this application that uses LINE BOT API.

### [Event handlers](./src/LINEBot/KitchenSink/EventHandler)

Handlers for LINE Messaging API events.

Notes
--

### Temporary directory

This application downloads multimedia files on `./public/static/tmpdir/`.
`./run.sh` wrapper removes such contents on shutting down the PHP server.

### Base URL

This application serves downloaded multimedia files.

Default, this app constructs URL of such content with `\Slim\Http\Request->getUri()->getBaseUrl()` as base URL.
Unfortunately this processing doesn't work correctly if this app runs on reverse-proxied environment.

If you get such symptom, please configure base URL as you like => [UrlBuilder](./src/LINEBot/KitchenSink/EventHandler/MessageHandler/Util/UrlBuilder.php)

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

