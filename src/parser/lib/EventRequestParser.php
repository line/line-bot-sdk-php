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

namespace LINE\Parser;

use LINE\LINEBot\Event\MessageEvent\UnknownMessageContent;
use LINE\Webhook\Model\MessageEvent;
use LINE\Parser\Event\UnknownEvent;
use LINE\Parser\Exception\InvalidEventRequestException;
use LINE\Parser\Exception\InvalidSignatureException;
use LINE\Parser\Source\UnknownSource;
use LINE\Parser\Source\UnknownThingsContent;
use LINE\Webhook\Model\ContentProvider;
use LINE\Webhook\Model\MessageContent;
use LINE\Webhook\Model\Source;
use LINE\Webhook\Model\ThingsContent;
use LINE\Webhook\Model\ThingsEvent;

class EventRequestParser
{
    private static $eventType2class = [
        'message' => 'LINE\Webhook\Model\MessageEvent',
        'unsend' => 'LINE\Webhook\Model\UnsendEvent',
        'follow' => 'LINE\Webhook\Model\FollowEvent',
        'unfollow' => 'LINE\Webhook\Model\UnfollowEvent',
        'join' => 'LINE\Webhook\Model\JoinEvent',
        'leave' => 'LINE\Webhook\Model\LeaveEvent',
        'postback' => 'LINE\Webhook\Model\PostbackEvent',
        'videoPlayComplete' => 'LINE\Webhook\Model\VideoPlayCompleteEvent',
        'beacon' => 'LINE\Webhook\Model\BeaconEvent',
        'accountLink' => 'LINE\Webhook\Model\AccountLinkEvent',
        'memberJoined' => 'LINE\Webhook\Model\MemberJoinedEvent',
        'memberLeft' => 'LINE\Webhook\Model\MemberLeftEvent',
        'things' => 'LINE\Webhook\Model\ThingsEvent',
    ];

    private static $messageType2class = [
        'text' => 'LINE\Webhook\Model\TextMessageContent',
        'image' => 'LINE\Webhook\Model\ImageMessageContent',
        'video' => 'LINE\Webhook\Model\VideoMessageContent',
        'audio' => 'LINE\Webhook\Model\AudioMessageContent',
        'file' => 'LINE\Webhook\Model\FileMessageContent',
        'location' => 'LINE\Webhook\Model\LocationMessageContent',
        'sticker' => 'LINE\Webhook\Model\StickerMessageContent',
    ];

    private static $sourceType2class = [
        'user' => 'LINE\Webhook\Model\UserSource',
        'group' => 'LINE\Webhook\Model\GroupSource',
        'room' => 'LINE\Webhook\Model\RoomSource',
    ];
    
    private static $thingsContentType2class = [
        'link' => '\LINE\Webhook\Model\LinkThingsContent',
        'unlink' => '\LINE\Webhook\Model\UnlinkThingsContent',
        'scenarioResult' => '\LINE\Webhook\Model\ScenarioResultThingsContent',
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
            $eventType = $eventData['type'];

            if (!isset(self::$eventType2class[$eventType])) {
                # Unknown event has come
                $events[] = new UnknownEvent($eventData);
                continue;
            }

            $eventClass = self::$eventType2class[$eventType];
            $event = new $eventClass($eventData);

            if ($event instanceof MessageEvent) {
                $message = self::parseMessageContent($eventData);
                $event->setMessage($message);
            }

            if ($event instanceof ThingsEvent) {
                $content = self::parseThingsContent($eventData);
                $event->setThings($content);
            }

            $source = self::parseSource($eventData);
            $event->setSource($source);

            $events[] = $event;
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
     * @return MessageContent
     */
    private static function parseMessageContent($eventData): MessageContent
    {
        $messageType = $eventData['message']['type'];
        if (!isset(self::$messageType2class[$messageType])) {
            return new UnknownMessageContent($eventData['message']);
        }

        $messageClass = self::$messageType2class[$messageType];
        $message = new $messageClass($eventData['message']);
        if (\method_exists($message, 'setContentProvider')) {
            $contentProvider = new ContentProvider($eventData['message']['contentProvider']);
            $message->setContentProvider($contentProvider);
        }
        return $message;
    }

    /**
     * @param array $eventData
     * @return Source
     */
    private static function parseSource($eventData): Source
    {
        $sourceType = $eventData['source']['type'];
        if (!isset(self::$sourceType2class[$sourceType])) {
            return new UnknownSource($eventData['source']);
        }

        $sourceClass = self::$sourceType2class[$sourceType];
        return new $sourceClass($eventData['source']);
    }
 
    /**
     * @param array $eventData
     * @return ThingsContent
     */
    private static function parseThingsContent($eventData): ThingsContent
    {
        $thingsContentType = $eventData['event']['things']['type'];
        if (!isset(self::$thingsContentType2class[$thingsContentType])) {
            return new UnknownThingsContent($eventData['source']);
        }

        $thingsContentClass = self::$thingsContentType2class[$thingsContentType];
        return new $thingsContentClass($eventData['event']['things']);
    }
}
