Webhook
=======

LINE's server sends user actions (messages, images, locations, etc.) to your bot server as webhook events.

Handling webhooks
-----------------

1. Receive the webhook request from LINE.
2. Parse the request body with ``EventRequestParser``.
3. Handle each event.

.. code-block:: php

   use LINE\Parser\EventRequestParser;

   $parsedEvents = EventRequestParser::parseEventRequest(
       $body,
       $channelSecret,
       $signature,
   );

   foreach ($parsedEvents->getEvents() as $event) {
       // Handle event
   }

See the example implementations:

- `EchoBot: Route.php <https://github.com/line/line-bot-sdk-php/blob/master/examples/EchoBot/src/LINEBot/EchoBot/Route.php>`_
- `KitchenSink: Route.php <https://github.com/line/line-bot-sdk-php/blob/master/examples/KitchenSink/src/LINEBot/KitchenSink/Route.php>`_
