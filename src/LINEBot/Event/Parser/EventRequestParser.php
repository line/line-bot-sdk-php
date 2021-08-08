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

use LINE\LINEBot\Event\BaseEvent;
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
        'unsend' => 'LINE\LINEBot\Event\UnsendEvent',
        'follow' => 'LINE\LINEBot\Event\FollowEvent',
        'unfollow' => 'LINE\LINEBot\Event\UnfollowEvent',
        'join' => 'LINE\LINEBot\Event\JoinEvent',
        'leave' => 'LINE\LINEBot\Event\LeaveEvent',
        'postback' => 'LINE\LINEBot\Event\PostbackEvent',
        'videoPlayComplete' => 'LINE\LINEBot\Event\VideoPlayCompleteEvent',
        'beacon' => 'LINE\LINEBot\Event\BeaconDetectionEvent',
        'accountLink' => 'LINE\LINEBot\Event\AccountLinkEvent',
        'memberJoined' => 'LINE\LINEBot\Event\MemberJoinEvent',
        'memberLeft' => 'LINE\LINEBot\Event\MemberLeaveEvent',
        'things' => 'LINE\LINEBot\Event\ThingsEvent',
    ];

    private static $messageType2class = [
        'text' => 'LINE\LINEBot\Event\MessageEvent\TextMessage',
        'image' => 'LINE\LINEBot\Event\MessageEvent\ImageMessage',
        'video' => 'LINE\LINEBot\Event\MessageEvent\VideoMessage',
        'audio' => 'LINE\LINEBot\Event\MessageEvent\AudioMessage',
        'file' => 'LINE\LINEBot\Event\MessageEvent\FileMessage',
        'location' => 'LINE\LINEBot\Event\MessageEvent\LocationMessage',
        'sticker' => 'LINE\LINEBot\Event\MessageEvent\StickerMessage',
    ];

    /**
     * @param string $body
     * @param string $channelSecret
     * @param string $signature
     * @return mixed
     * @throws InvalidEventRequestException
     * @throws InvalidSignatureException
     */
    public static function parseEventRequest($body, $channelSecret, $signature, $eventsOnly = true)
    {
        if (trim($signature) === '') {
            throw new InvalidSignatureException('Request does not contain signature');
        }

        if (!SignatureValidator::validateSignature($body, $channelSecret, $signature)) {
            throw new InvalidSignatureException('Invalid signature has given');
        }

        $events = [];

        $parsedReq = json_decode($body, true);
        if (!isset($parsedReq['events'])) {
            throw new InvalidEventRequestException();
        }

        foreach ($parsedReq['events'] as $eventData) {
            $events[] = self::parseEvent($eventData);
        }

        if ($eventsOnly) {
            return $events;
        }

        $parsedReq = json_decode($body, true);
        if (!isset($parsedReq['destination'])) {
            throw new InvalidEventRequestException();
        }

        return [$parsedReq['destination'], $events];
    }

    /**
     * @param array $eventData
     * @return BaseEvent
     */
    private static function parseEvent($eventData)
    {
        $eventType = $eventData['type'];

        if (!isset(self::$eventType2class[$eventType])) {
            # Unknown event has come
            return new UnknownEvent($eventData);
        }

        $eventClass = self::$eventType2class[$eventType];

        if ($eventType === 'message') {
            return self::parseMessageEvent($eventData);
        }

        return new $eventClass($eventData);
    }

    /**
     * @param array $eventData
     * @return MessageEvent
     */
    private static function parseMessageEvent($eventData)
    {
        $messageType = $eventData['message']['type'];
        if (!isset(self::$messageType2class[$messageType])) {
            return new UnknownMessage($eventData);
        }

        $messageClass = self::$messageType2class[$messageType];
        return new $messageClass($eventData);
    }
}
