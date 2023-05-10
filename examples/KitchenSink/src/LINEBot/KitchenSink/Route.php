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

namespace LINE\LINEBot\KitchenSink;

use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;
use LINE\LINEBot\Event\MessageEvent\UnknownMessageContent;
use LINE\LINEBot\KitchenSink\AccountLinkEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\BeaconEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\FollowEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\JoinEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\LeaveEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\AudioMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\ImageMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\LocationMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\StickerMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\TextMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\VideoMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\PostbackEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\ThingsEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\UnfollowEventHandler;
use LINE\Constants\HTTPHeader;
use LINE\Parser\Event\UnknownEvent;
use LINE\Parser\EventRequestParser;
use LINE\Webhook\Model\MessageEvent;
use LINE\Parser\Exception\InvalidEventRequestException;
use LINE\Parser\Exception\InvalidSignatureException;
use LINE\Webhook\Model\AccountLinkEvent;
use LINE\Webhook\Model\AudioMessageContent;
use LINE\Webhook\Model\BeaconEvent;
use LINE\Webhook\Model\FollowEvent;
use LINE\Webhook\Model\ImageMessageContent;
use LINE\Webhook\Model\JoinEvent;
use LINE\Webhook\Model\LeaveEvent;
use LINE\Webhook\Model\LocationMessageContent;
use LINE\Webhook\Model\PostbackEvent;
use LINE\Webhook\Model\StickerMessageContent;
use LINE\Webhook\Model\TextMessageContent;
use LINE\Webhook\Model\ThingsEvent;
use LINE\Webhook\Model\UnfollowEvent;
use LINE\Webhook\Model\VideoMessageContent;

class Route
{
    public function register(\Slim\App $app)
    {
        $app->post('/callback', function (\Psr\Http\Message\RequestInterface $req, \Psr\Http\Message\ResponseInterface $res) {
            /** @var \LINE\Clients\MessagingApi\Api\MessagingApiApi $bot */
            $bot = $this->get(MessagingApiApi::class);
            $botBlob = $this->get(MessagingApiBlobApi::class);
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
                /** @var EventHandler $handler */
                $handler = null;

                if ($event instanceof MessageEvent) {
                    $message = $event->getMessage();
                    if ($message instanceof TextMessageContent) {
                        $handler = new TextMessageHandler($bot, $logger, $req, $event);
                    } elseif ($message instanceof StickerMessageContent) {
                        $handler = new StickerMessageHandler($bot, $logger, $event);
                    } elseif ($message instanceof LocationMessageContent) {
                        $handler = new LocationMessageHandler($bot, $logger, $event);
                    } elseif ($message instanceof ImageMessageContent) {
                        $handler = new ImageMessageHandler($bot, $botBlob, $logger, $req, $event);
                    } elseif ($message instanceof AudioMessageContent) {
                        $handler = new AudioMessageHandler($bot, $botBlob, $logger, $req, $event);
                    } elseif ($message instanceof VideoMessageContent) {
                        $handler = new VideoMessageHandler($bot, $botBlob, $logger, $req, $event);
                    } elseif ($message instanceof UnknownMessageContent) {
                        $logger->info(sprintf(
                            'Unknown message type has come [message type: %s]',
                            $message->getType()
                        ));
                    } else {
                        // Unexpected behavior (just in case)
                        // something wrong if reach here
                        $logger->info(sprintf(
                            'Unexpected message type has come, something wrong [class name: %s]',
                            get_class($event)
                        ));
                        continue;
                    }
                } elseif ($event instanceof UnfollowEvent) {
                    $handler = new UnfollowEventHandler($bot, $logger, $event);
                } elseif ($event instanceof FollowEvent) {
                    $handler = new FollowEventHandler($bot, $logger, $event);
                } elseif ($event instanceof JoinEvent) {
                    $handler = new JoinEventHandler($bot, $logger, $event);
                } elseif ($event instanceof LeaveEvent) {
                    $handler = new LeaveEventHandler($bot, $logger, $event);
                } elseif ($event instanceof PostbackEvent) {
                    $handler = new PostbackEventHandler($bot, $logger, $event);
                } elseif ($event instanceof BeaconEvent) {
                    $handler = new BeaconEventHandler($bot, $logger, $event);
                } elseif ($event instanceof AccountLinkEvent) {
                    $handler = new AccountLinkEventHandler($bot, $logger, $event);
                } elseif ($event instanceof ThingsEvent) {
                    $handler = new ThingsEventHandler($bot, $logger, $event);
                } elseif ($event instanceof UnknownEvent) {
                    $logger->info(sprintf('Unknown message type has come [type: %s]', $event->getType()));
                } else {
                    // Unexpected behavior (just in case)
                    // something wrong if reach here
                    $logger->info(sprintf(
                        'Unexpected event type has come, something wrong [class name: %s]',
                        get_class($event)
                    ));
                    continue;
                }

                $handler->handle();
            }

            $res->withStatus(200, 'OK');
            return $res;
        });
    }
}
