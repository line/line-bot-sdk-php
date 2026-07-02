Error handling
==============

Catching exceptions
-------------------

Each API client defines its own exception class.
For ``MessagingApiApi``, catch ``\LINE\Clients\MessagingApi\ApiException``:

.. code-block:: php

   use LINE\Clients\MessagingApi\ApiException;

   try {
       $profile = $messagingApi->getProfile($userId);
   } catch (ApiException $e) {
       $httpStatusCode = $e->getCode();
       $errorMessage = $e->getResponseBody();
       $headers = $e->getResponseHeaders();
       $requestId = $headers['x-line-request-id'][0] ?? null;
   }

Getting response headers
------------------------

Use ``*WithHttpInfo`` methods to access response headers and status codes:

.. code-block:: php

   [$body, $statusCode, $headers] = $messagingApi->replyMessageWithHttpInfo($request);

   $requestId = $headers['x-line-request-id'][0];
