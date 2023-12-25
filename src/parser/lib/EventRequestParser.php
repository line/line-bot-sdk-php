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

use LINE\Constants\MentioneeType;
use LINE\Webhook\Model\MessageEvent;
use LINE\Parser\Exception\InvalidEventRequestException;
use LINE\Parser\Exception\InvalidSignatureException;
use LINE\Webhook\Model\ActionResult;
use LINE\Webhook\Model\AllMentionee;
use LINE\Webhook\Model\ContentProvider;
use LINE\Webhook\Model\DeliveryContext;
use LINE\Webhook\Model\Emoji;
use LINE\Webhook\Model\Event;
use LINE\Webhook\Model\ImageMessageContent;
use LINE\Webhook\Model\ImageSet;
use LINE\Webhook\Model\Mention;
use LINE\Webhook\Model\Mentionee;
use LINE\Webhook\Model\MessageContent;
use LINE\Webhook\Model\ModuleContent;
use LINE\Webhook\Model\ModuleEvent;
use LINE\Webhook\Model\ScenarioResult;
use LINE\Webhook\Model\ScenarioResultThingsContent;
use LINE\Webhook\Model\Source;
use LINE\Webhook\Model\TextMessageContent;
use LINE\Webhook\Model\ThingsContent;
use LINE\Webhook\Model\ThingsEvent;
use LINE\Webhook\Model\UserMentionee;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EventRequestParser
{
    private static $eventType2class = [
        'message' => \LINE\Webhook\Model\MessageEvent::class,
        'unsend' => \LINE\Webhook\Model\UnsendEvent::class,
        'follow' => \LINE\Webhook\Model\FollowEvent::class,
        'unfollow' => \LINE\Webhook\Model\UnfollowEvent::class,
        'join' => \LINE\Webhook\Model\JoinEvent::class,
        'leave' => \LINE\Webhook\Model\LeaveEvent::class,
        'postback' => \LINE\Webhook\Model\PostbackEvent::class,
        'videoPlayComplete' => \LINE\Webhook\Model\VideoPlayCompleteEvent::class,
        'beacon' => \LINE\Webhook\Model\BeaconEvent::class,
        'accountLink' => \LINE\Webhook\Model\AccountLinkEvent::class,
        'memberJoined' => \LINE\Webhook\Model\MemberJoinedEvent::class,
        'memberLeft' => \LINE\Webhook\Model\MemberLeftEvent::class,
        'things' => \LINE\Webhook\Model\ThingsEvent::class,
        'module' => \LINE\Webhook\Model\ModuleEvent::class,
        'activated' => \LINE\Webhook\Model\ActivatedEvent::class,
        'deactivated' => \LINE\Webhook\Model\DeactivatedEvent::class,
        'botSuspended' => \LINE\Webhook\Model\BotSuspendedEvent::class,
        'botResumed' => \LINE\Webhook\Model\BotResumedEvent::class,
        'delivery' => \LINE\Webhook\Model\PnpDeliveryCompletionEvent::class,
    ];

    private static $messageType2class = [
        'text' => \LINE\Webhook\Model\TextMessageContent::class,
        'image' => \LINE\Webhook\Model\ImageMessageContent::class,
        'video' => \LINE\Webhook\Model\VideoMessageContent::class,
        'audio' => \LINE\Webhook\Model\AudioMessageContent::class,
        'file' => \LINE\Webhook\Model\FileMessageContent::class,
        'location' => \LINE\Webhook\Model\LocationMessageContent::class,
        'sticker' => \LINE\Webhook\Model\StickerMessageContent::class,
    ];

    private static $sourceType2class = [
        'user' => \LINE\Webhook\Model\UserSource::class,
        'group' => \LINE\Webhook\Model\GroupSource::class,
        'room' => \LINE\Webhook\Model\RoomSource::class,
    ];

    private static $thingsContentType2class = [
        'link' => \LINE\Webhook\Model\LinkThingsContent::class,
        'unlink' => \LINE\Webhook\Model\UnlinkThingsContent::class,
        'scenarioResult' => \LINE\Webhook\Model\ScenarioResultThingsContent::class,
    ];

    private static $contentType2class = [
        'postback' => \LINE\Webhook\Model\PostbackContent::class,
        'beacon' => \LINE\Webhook\Model\BeaconContent::class,
        'link' => \LINE\Webhook\Model\LinkContent::class,
        'joined' => \LINE\Webhook\Model\JoinedMembers::class,
        'left' => \LINE\Webhook\Model\LeftMembers::class,
        'unsend' => \LINE\Webhook\Model\UnsendDetail::class,
        'videoPlayComplete' => \LINE\Webhook\Model\VideoPlayComplete::class,
        'chatControl' => \LINE\Webhook\Model\ChatControl::class,
        'delivery' => \LINE\Webhook\Model\PnpDelivery::class,
    ];

    private static $moduleContentType2class = [
        'attached' => \LINE\Webhook\Model\AttachedModuleContent::class,
        'detached' => \LINE\Webhook\Model\DetachedModuleContent::class,
    ];

    /**
     * @param string $body
     * @param string $channelSecret
     * @param string $signature
     * @return ParsedEvents
     * @throws InvalidEventRequestException
     * @throws InvalidSignatureException
     */
    public static function parseEventRequest(string $body, string $channelSecret, string $signature): ParsedEvents
    {
        if (trim($signature) === '') {
            throw new InvalidSignatureException('Request does not contain signature');
        }

        if (!SignatureValidator::validateSignature($body, $channelSecret, $signature)) {
            throw new InvalidSignatureException('Invalid signature has given');
        }

        $parsedReq = json_decode($body, true);
        if (!isset($parsedReq['events'])) {
            throw new InvalidEventRequestException();
        }

        $events = [];
        foreach ($parsedReq['events'] as $eventData) {
            $events[] = self::parseEvent($eventData);
        }

        return new ParsedEvents($parsedReq['destination'] ?? null, $events);
    }

    private static function parseEvent($eventData): Event
    {
        $eventType = $eventData['type'];
        $eventClass = self::$eventType2class[$eventType] ?? \LINE\Webhook\Model\Event::class;
        $event = new $eventClass($eventData);

        if ($event instanceof MessageEvent) {
            $message = self::parseMessageContent($eventData);
            $event->setMessage($message);
        }

        if ($event instanceof ThingsEvent) {
            $content = self::parseThingsContent($eventData);
            $event->setThings($content);
        }

        if ($event instanceof ModuleEvent) {
            $content = self::parseModuleContent($eventData);
            $event->setModule($content);
        }

        foreach (array_keys($eventData) as $key) {
            $contentClass = self::$contentType2class[$key] ?? null;
            if (!isset($contentClass)) {
                continue;
            }
            $content = new $contentClass($eventData[$key]);
            $setter = 'set' . ucfirst($key);
            $event->$setter($content);
        }

        $source = self::parseSource($eventData);
        $event->setSource($source);
        $deliveryContext = new DeliveryContext($eventData['deliveryContext']);
        $event->setDeliveryContext($deliveryContext);

        return $event;
    }

    /**
     * @param array $eventData
     * @return MessageContent
     */
    private static function parseMessageContent($eventData): MessageContent
    {
        $messageType = $eventData['message']['type'];
        if (!isset(self::$messageType2class[$messageType])) {
            return new MessageContent($eventData['message']);
        }

        $messageClass = self::$messageType2class[$messageType];
        $message = new $messageClass($eventData['message']);
        if (\method_exists($message, 'setContentProvider')) {
            $contentProvider = new ContentProvider($eventData['message']['contentProvider']);
            $message->setContentProvider($contentProvider);
        }

        if ($message instanceof TextMessageContent) {
            $messageData = $eventData['message'];
            $emojis = array_map(fn ($item) => new Emoji($item), $messageData['emojis'] ?? []);
            $message->setEmojis($emojis);
            $mentionData = $messageData['mention'] ?? null;
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
        if (!isset($eventData['source'])) {
            return new Source([]);
        }
        $sourceType = $eventData['source']['type'];
        if (!isset(self::$sourceType2class[$sourceType])) {
            return new Source($eventData['source']);
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
            return new ThingsContent($eventData['things']);
        }

        $thingsContentClass = self::$thingsContentType2class[$thingsContentType];
        $content = new $thingsContentClass($eventData['things']);
        if (!($content instanceof ScenarioResultThingsContent)) {
            return $content;
        }

        $resultData = $eventData['things']['result'];
        $result = new ScenarioResult($resultData);
        $actionResults = array_map(fn ($item) => new ActionResult($item), $resultData['actionResults']);
        $result->setActionResults($actionResults);
        $content->setResult($result);
        return $content;
    }

    /**
     * @param array $eventData
     * @return ModuleContent
     */
    private static function parseModuleContent($eventData): ModuleContent
    {
        $moduleContentType = $eventData['module']['type'];

        if (!isset(self::$moduleContentType2class[$moduleContentType])) {
            return new ModuleContent($eventData['module']);
        }
        $moduleContentClass = self::$moduleContentType2class[$moduleContentType];
        return new $moduleContentClass($eventData['module']);
    }
}
