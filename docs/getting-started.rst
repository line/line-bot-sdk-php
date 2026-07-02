Getting started
===============

Requirements
------------

- PHP 8.2 or later
- `Composer <https://getcomposer.org/>`_

Installation
------------

.. code-block:: bash

   composer require linecorp/line-bot-sdk

Create a client
---------------

.. code-block:: php

   use GuzzleHttp\Client;
   use LINE\Clients\MessagingApi\Configuration;
   use LINE\Clients\MessagingApi\Api\MessagingApiApi;

   $client = new Client();
   $config = new Configuration();
   $config->setAccessToken('<channel access token>');
   $messagingApi = new MessagingApiApi(
       client: $client,
       config: $config,
   );

Send a reply
------------

.. code-block:: php

   use LINE\Clients\MessagingApi\Model\TextMessage;
   use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;

   $message = new TextMessage(['text' => 'hello!']);
   $request = new ReplyMessageRequest([
       'replyToken' => '<reply token>',
       'messages' => [$message],
   ]);
   $messagingApi->replyMessage($request);

Setter style is also supported:

.. code-block:: php

   $message = (new TextMessage())
       ->setText('hello!');
   $request = (new ReplyMessageRequest())
       ->setReplyToken('<reply token>')
       ->setMessages([$message]);
   $messagingApi->replyMessage($request);

Examples
--------

- `EchoBot <https://github.com/line/line-bot-sdk-php/tree/master/examples/EchoBot>`_ — Replies to text messages from users.
- `KitchenSink <https://github.com/line/line-bot-sdk-php/tree/master/examples/KitchenSink>`_ — Demonstrates practical use of the Messaging API.
