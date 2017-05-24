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

namespace LINE\LINEBot\Event\Parser;

use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\UnknownMessage;
use LINE\LINEBot\Event\UnknownEvent;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\SignatureValidator;

class EventRequestParser
{
    private static $eventType2class = [
        'message' => 'LINE\LINEBot\Event\MessageEvent',
        'follow' => 'LINE\LINEBot\Event\FollowEvent',
        'unfollow' => 'LINE\LINEBot\Event\UnfollowEvent',
        'join' => 'LINE\LINEBot\Event\JoinEvent',
        'leave' => 'LINE\LINEBot\Event\LeaveEvent',
        'postback' => 'LINE\LINEBot\Event\PostbackEvent',
        'beacon' => 'LINE\LINEBot\Event\BeaconDetectionEvent',
    ];

    private static $messageType2class = [
        'text' => 'LINE\LINEBot\Event\MessageEvent\TextMessage',
        'image' => 'LINE\LINEBot\Event\MessageEvent\ImageMessage',
        'video' => 'LINE\LINEBot\Event\MessageEvent\VideoMessage',
        'audio' => 'LINE\LINEBot\Event\MessageEvent\AudioMessage',
        'location' => 'LINE\LINEBot\Event\MessageEvent\LocationMessage',
        'sticker' => 'LINE\LINEBot\Event\MessageEvent\StickerMessage',
    ];

    /**
     * @param string $body
     * @param string $channelSecret
     * @param string $signature
     * @return \LINE\LINEBot\Event\BaseEvent[] array
     * @throws InvalidEventRequestException
     * @throws InvalidSignatureException
     */
    public static function parseEventRequest($body, $channelSecret, $signature)
    {
        if (!isset($signature)) {
            throw new InvalidSignatureException('Request does not contain signature');
        }

        if (!SignatureValidator::validateSignature($body, $channelSecret, $signature)) {
            throw new InvalidSignatureException('Invalid signature has given');
        }

        $events = [];

        $parsedReq = json_decode($body, true);
        if (!array_key_exists('events', $parsedReq)) {
            throw new InvalidEventRequestException();
        }

        foreach ($parsedReq['events'] as $eventData) {
            $eventType = $eventData['type'];

            if (!array_key_exists($eventType, self::$eventType2class)) {
                # Unknown event has come
                $events[] = new UnknownEvent($eventData);
                continue;
            }

            $eventClass = self::$eventType2class[$eventType];

            if ($eventType === 'message') {
                $events[] = self::parseMessageEvent($eventData);
                continue;
            }

            $events[] = new $eventClass($eventData);
        }

        return $events;
    }

    /**
     * @param array $eventData
     * @return MessageEvent
     */
    private static function parseMessageEvent($eventData)
    {
        $messageType = $eventData['message']['type'];
        if (!array_key_exists($messageType, self::$messageType2class)) {
            return new UnknownMessage($eventData);
        }

        $messageClass = self::$messageType2class[$messageType];
        return new $messageClass($eventData);
    }
}
