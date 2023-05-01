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

use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Constants\MentioneeType;
use LINE\Webhook\Model\MessageEvent;
use LINE\Parser\Event\UnknownEvent;
use LINE\Parser\Exception\InvalidEventRequestException;
use LINE\Parser\Exception\InvalidSignatureException;
use LINE\Parser\MessageContent\UnknownMessageContent;
use LINE\Parser\Source\UnknownSource;
use LINE\Parser\Source\UnknownThingsContent;
use LINE\Webhook\Model\AccountLinkEvent;
use LINE\Webhook\Model\ActionResult;
use LINE\Webhook\Model\AllMentionee;
use LINE\Webhook\Model\BeaconContent;
use LINE\Webhook\Model\BeaconEvent;
use LINE\Webhook\Model\ContentProvider;
use LINE\Webhook\Model\DeliveryContext;
use LINE\Webhook\Model\Emoji;
use LINE\Webhook\Model\ImageMessageContent;
use LINE\Webhook\Model\ImageSet;
use LINE\Webhook\Model\JoinedMembers;
use LINE\Webhook\Model\LeftMembers;
use LINE\Webhook\Model\LinkContent;
use LINE\Webhook\Model\MemberJoinedEvent;
use LINE\Webhook\Model\MemberLeftEvent;
use LINE\Webhook\Model\Mention;
use LINE\Webhook\Model\Mentionee;
use LINE\Webhook\Model\MessageContent;
use LINE\Webhook\Model\PostbackContent;
use LINE\Webhook\Model\PostbackEvent;
use LINE\Webhook\Model\ScenarioResult;
use LINE\Webhook\Model\ScenarioResultThingsContent;
use LINE\Webhook\Model\Source;
use LINE\Webhook\Model\TextMessageContent;
use LINE\Webhook\Model\ThingsContent;
use LINE\Webhook\Model\ThingsEvent;
use LINE\Webhook\Model\UnsendDetail;
use LINE\Webhook\Model\UnsendEvent;
use LINE\Webhook\Model\UserMentionee;
use LINE\Webhook\Model\VideoPlayComplete;
use LINE\Webhook\Model\VideoPlayCompleteEvent;

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

            if (isset(self::$eventType2class[$eventType])) {
                $eventClass = self::$eventType2class[$eventType];
                $event = new $eventClass($eventData);
            } else {
                # Unknown event has come
                $event = new UnknownEvent($eventData);
            }


            if ($event instanceof MessageEvent) {
                $message = self::parseMessageContent($eventData);
                $event->setMessage($message);
            }

            if ($event instanceof ThingsEvent) {
                $content = self::parseThingsContent($eventData);
                $event->setThings($content);
            }

            if ($event instanceof PostbackEvent) {
                $content = new PostbackContent($eventData['postback']);
                $event->setPostback($content);
            }

            if ($event instanceof BeaconEvent) {
                $content = new BeaconContent($eventData['beacon']);
                $event->setBeacon($content);
            }

            if ($event instanceof AccountLinkEvent) {
                $content = new LinkContent($eventData['link']);
                $event->setLink($content);
            }

            if ($event instanceof MemberJoinedEvent) {
                $content = new JoinedMembers($eventData['joined']);
                $event->setJoined($content);
            }

            if ($event instanceof MemberLeftEvent) {
                $content = new LeftMembers($eventData['left']);
                $event->setLeft($content);
            }

            if ($event instanceof UnsendEvent) {
                $content = new UnsendDetail($eventData['unsend']);
                $event->setUnsend($content);
            }

            if ($event instanceof VideoPlayCompleteEvent) {
                $content = new VideoPlayComplete($eventData['videoPlayComplete']);
                $event->setVideoPlayComplete($content);
            }

            $source = self::parseSource($eventData);
            $event->setSource($source);
            $deliveryContext = new DeliveryContext($eventData['deliveryContext']);
            $event->setDeliveryContext($deliveryContext);

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

        if ($message instanceof TextMessageContent) {
            $emojis = array_map(function ($emoji) {
                return new Emoji($emoji);
            }, $eventData['message']['emojis'] ?? []);
            $message->setEmojis($emojis);
            $mentionData = $eventData['message']['mention'] ?? null;
            if (isset($mentionData)) {
                $mention = new Mention($mentionData);
                $mentionees = array_map(function ($mentionee) {
                    if ($mentionee['type'] == MentioneeType::TYPE_USER) {
                        return new UserMentionee($mentionee);
                    }
                    if ($mentionee['type'] == MentioneeType::TYPE_ALL) {
                        return new AllMentionee($mentionee);
                    }
                    return new Mentionee($mentionee);
                }, $mentionData['mentionees']);
                $mention->setMentionees($mentionees);
                $message->setMention($mention);
            }
        }

        if ($message instanceof ImageMessageContent) {
            $imageSet = $eventData['message']['imageSet'] ?? null;
            if (isset($imageSet)) {
                $message->setImageSet(new ImageSet($imageSet));
            }
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
        $thingsContentType = $eventData['things']['type'];
        if (!isset(self::$thingsContentType2class[$thingsContentType])) {
            return new UnknownThingsContent($eventData['source']);
        }

        $thingsContentClass = self::$thingsContentType2class[$thingsContentType];
        $content = new $thingsContentClass($eventData['things']);
        if ($content instanceof ScenarioResultThingsContent) {
            $result = new ScenarioResult($eventData['things']['result']);
            $actionResults = array_map(function ($actionResult) {
                return new ActionResult($actionResult);
            }, $eventData['things']['result']['actionResults']);
            $result->setActionResults($actionResults);
            $content->setResult($result);
        }
        return $content;
    }
}
