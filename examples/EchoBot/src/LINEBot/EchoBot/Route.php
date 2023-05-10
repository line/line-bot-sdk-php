<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace LINE\LINEBot\EchoBot;

use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Constants\HTTPHeader;
use LINE\Parser\EventRequestParser;
use LINE\Webhook\Model\MessageEvent;
use LINE\Parser\Exception\InvalidEventRequestException;
use LINE\Parser\Exception\InvalidSignatureException;
use LINE\Webhook\Model\TextMessageContent;

class Route
{
    public function register(\Slim\App $app)
    {
        $app->post('/callback', function (\Psr\Http\Message\RequestInterface $req, \Psr\Http\Message\ResponseInterface $res) {
            $bot = $this->get(\LINE\Clients\MessagingApi\Api\MessagingApiApi::class);
            $logger = $this->get(\Psr\Log\LoggerInterface::class);

            $signature = $req->getHeader(HTTPHeader::LINE_SIGNATURE);
            if (empty($signature)) {
                return $res->withStatus(400, 'Bad Request');
            }

            // Check request with signature and parse request
            try {
                $secret = $this->get('settings')['bot']['channelSecret'];
                $parsedEvents = EventRequestParser::parseEventRequest($req->getBody(), $secret, $signature[0]);
            } catch (InvalidSignatureException $e) {
                return $res->withStatus(400, 'Invalid signature');
            } catch (InvalidEventRequestException $e) {
                return $res->withStatus(400, "Invalid event request");
            }

            foreach ($parsedEvents->getEvents() as $event) {
                if (!($event instanceof MessageEvent)) {
                    $logger->info('Non message event has come');
                    continue;
                }

                $message = $event->getMessage();
                if (!($message instanceof TextMessageContent)) {
                    $logger->info('Non text message has come');
                    continue;
                }

                $replyText = $message->getText();
                $logger->info('Reply text: ' . $replyText);
                $bot->replyMessage(new ReplyMessageRequest([
                    'replyToken' => $event->getReplyToken(),
                    'messages' => [
                        (new TextMessage(['text' => $replyText]))->setType('text'),
                    ],
                ]));
            }

            $res->withStatus(200, 'OK');
            return $res;
        });
    }
}
