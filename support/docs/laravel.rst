Laravel integration
===================

This SDK includes a Laravel service provider with facades.

Setup
-----

Add your channel access token to ``.env``:

.. code-block:: text

   LINE_BOT_CHANNEL_ACCESS_TOKEN=<Channel Access Token>

Then use the facades directly:

.. code-block:: php

   \LINEMessagingApi::pushMessage($request);

Custom configuration
--------------------

To customize the HTTP client configuration, publish the config file:

.. code-block:: bash

   php artisan vendor:publish --provider="LINE\Laravel\LINEBotServiceProvider" --tag=config

This creates ``config/line-bot.php``:

.. code-block:: php

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
