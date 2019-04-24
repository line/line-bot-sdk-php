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

namespace LINE;

use LINE\LINEBot\Event\Parser\EventRequestParser;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Response;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\RichMenuBuilder;
use ReflectionClass;
use DateTime;
use DateTimeZone;

/**
 * A client class of LINE Messaging API.
 *
 * @package LINE
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class LINEBot
{
    const DEFAULT_ENDPOINT_BASE = 'https://api.line.me';

    /** @var string */
    private $channelSecret;
    /** @var string */
    private $endpointBase;
    /** @var HTTPClient */
    private $httpClient;

    /**
     * LINEBot constructor.
     *
     * @param HTTPClient $httpClient HTTP client instance to use API calling.
     * @param array $args Configurations.
     */
    public function __construct(HTTPClient $httpClient, array $args)
    {
        $this->httpClient = $httpClient;
        $this->channelSecret = $args['channelSecret'];

        $this->endpointBase = LINEBot::DEFAULT_ENDPOINT_BASE;
        if (!empty($args['endpointBase'])) {
            $this->endpointBase = $args['endpointBase'];
        }
    }

    /**
     * Gets specified user's profile through API calling.
     *
     * @param string $userId The user ID to retrieve profile.
     * @return Response
     */
    public function getProfile($userId)
    {
        return $this->httpClient->get($this->endpointBase . '/v2/bot/profile/' . urlencode($userId));
    }

    /**
     * Gets message content which is associated with specified message ID.
     *
     * @param string $messageId The message ID to retrieve content.
     * @return Response
     */
    public function getMessageContent($messageId)
    {
        return $this->httpClient->get($this->endpointBase . '/v2/bot/message/' . urlencode($messageId) . '/content');
    }

    /**
     * Replies arbitrary message to destination which is associated with reply token.
     *
     * @param string $replyToken Identifier of destination.
     * @param MessageBuilder $messageBuilder Message builder to send.
     * @return Response
     */
    public function replyMessage($replyToken, MessageBuilder $messageBuilder)
    {
        return $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => $messageBuilder->buildMessage(),
        ]);
    }

    /**
     * Replies text message(s) to destination which is associated with reply token.
     *
     * This method receives variable texts. It can send text(s) message as bulk.
     *
     * Exact signature of this method is <code>replyText(string $replyToken, string $text, string[] $extraTexts)</code>.
     *
     * Means, this method can also receive multiple texts like so;
     *
     * <code>
     * $bot->replyText('reply-text', 'text', 'extra text1', 'extra text2', ...)
     * </code>
     *
     * @param string $replyToken Identifier of destination.
     * @param string $text Text of message.
     * @param string[]|null $extraTexts Extra text of message.
     * @return Response
     * @throws \ReflectionException
     */
    public function replyText($replyToken, $text, $extraTexts = null)
    {
        $extra = [];
        if (!is_null($extraTexts)) {
            $args = func_get_args();
            $extra = array_slice($args, 2);
        }

        /** @var TextMessageBuilder $textMessageBuilder */
        $ref = new ReflectionClass('LINE\LINEBot\MessageBuilder\TextMessageBuilder');
        $textMessageBuilder = $ref->newInstanceArgs(array_merge([$text], $extra));

        return $this->replyMessage($replyToken, $textMessageBuilder);
    }

    /**
     * Sends arbitrary message to destination.
     *
     * @param string $to Identifier of destination.
     * @param MessageBuilder $messageBuilder Message builder to send.
     * @return Response
     */
    public function pushMessage($to, MessageBuilder $messageBuilder)
    {
        return $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
            'to' => $to,
            'messages' => $messageBuilder->buildMessage(),
        ]);
    }

    /**
     * Sends arbitrary message to multi destinations.
     *
     * @param array $tos Identifiers of destination.
     * @param MessageBuilder $messageBuilder Message builder to send.
     * @return Response
     */
    public function multicast(array $tos, MessageBuilder $messageBuilder)
    {
        return $this->httpClient->post($this->endpointBase . '/v2/bot/message/multicast', [
            'to' => $tos,
            'messages' => $messageBuilder->buildMessage(),
        ]);
    }

    /**
     * Leaves from group.
     *
     * @param string $groupId Identifier of group to leave.
     * @return Response
     */
    public function leaveGroup($groupId)
    {
        return $this->httpClient->post($this->endpointBase . '/v2/bot/group/' . urlencode($groupId) . '/leave', []);
    }

    /**
     * Leaves from room.
     *
     * @param string $roomId Identifier of room to leave.
     * @return Response
     */
    public function leaveRoom($roomId)
    {
        return $this->httpClient->post($this->endpointBase . '/v2/bot/room/' . urlencode($roomId) . '/leave', []);
    }

    /**
     * Parse event request to Event objects.
     *
     * @param string $body Request body.
     * @param string $signature Signature of request.
     * @param bool $eventOnly if this flag on, get events only.
     * @return mixed
     * @throws LINEBot\Exception\InvalidEventRequestException
     * @throws LINEBot\Exception\InvalidSignatureException
     */
    public function parseEventRequest($body, $signature, $eventOnly = true)
    {
        return EventRequestParser::parseEventRequest($body, $this->channelSecret, $signature, $eventOnly);
    }

    /**
     * Validate request with signature.
     *
     * @param string $body Request body.
     * @param string $signature Signature of request.
     * @return bool Request is valid or not.
     * @throws LINEBot\Exception\InvalidSignatureException
     */
    public function validateSignature($body, $signature)
    {
        return SignatureValidator::validateSignature($body, $this->channelSecret, $signature);
    }

    /**
     * Gets the user profile of a member of a group that the bot is in.
     * This can be the user ID of a user who has not added the bot as a friend or has blocked the bot.
     *
     * @param string $groupId Identifier of the group
     * @param string $userId Identifier of the user
     * @return Response
     */
    public function getGroupMemberProfile($groupId, $userId)
    {
        $url = sprintf('%s/v2/bot/group/%s/member/%s', $this->endpointBase, urlencode($groupId), urlencode($userId));
        return $this->httpClient->get($url, []);
    }

    /**
     * Gets the user profile of a member of a room that the bot is in.
     * This can be the user ID of a user who has not added the bot as a friend or has blocked the bot.
     *
     * @param string $roomId Identifier of the room
     * @param string $userId Identifier of the user
     * @return Response
     */
    public function getRoomMemberProfile($roomId, $userId)
    {
        $url = sprintf('%s/v2/bot/room/%s/member/%s', $this->endpointBase, urlencode($roomId), urlencode($userId));
        return $this->httpClient->get($url, []);
    }

    /**
     * Gets the user IDs of the members of a group that the bot is in.
     * This includes the user IDs of users who have not added the bot as a friend or has blocked the bot.
     *
     * This feature is only available for LINE@ Approved accounts or official accounts.
     *
     * @param string $groupId Identifier of the group
     * @param string $start continuationToken
     * @return Response
     */
    public function getGroupMemberIds($groupId, $start = null)
    {
        $url = sprintf('%s/v2/bot/group/%s/members/ids', $this->endpointBase, urlencode($groupId));
        $params = is_null($start) ? [] : ['start' => $start];
        return $this->httpClient->get($url, $params);
    }

    /**
     * Gets the user IDs of the members of a room that the bot is in.
     * This includes the user IDs of users who have not added the bot as a friend or has blocked the bot.
     *
     * This feature is only available for LINE@ Approved accounts or official accounts.
     *
     * @param string $roomId Identifier of the room
     * @param string $start continuationToken
     * @return Response
     */
    public function getRoomMemberIds($roomId, $start = null)
    {
        $url = sprintf('%s/v2/bot/room/%s/members/ids', $this->endpointBase, urlencode($roomId));
        $params = is_null($start) ? [] : ['start' => $start];
        return $this->httpClient->get($url, $params);
    }

    /**
     * Gets the user IDs of the members of a group that the bot is in.
     * This includes the user IDs of users who have not added the bot as a friend or has blocked the bot.
     * This method gets all of the members by calling getGroupMemberIds() continually using token
     *
     * This feature is only available for LINE@ Approved accounts or official accounts.
     *
     * @param string $groupId Identifier of the group
     * @return array memberIds
     * @see \LINE\LINEBot::getGroupMemberIds()
     */
    public function getAllGroupMemberIds($groupId)
    {
        $memberIds = [];
        $continuationToken = null;
        do {
            $response = $this->getGroupMemberIds($groupId, $continuationToken);
            $data = $response->getJSONDecodedBody();
            $memberIds = array_merge($memberIds, $data['memberIds']);
            $continuationToken = isset($data['next']) ? $data['next'] : null;
        } while ($continuationToken);

        return $memberIds;
    }

    /**
     * Gets the user IDs of the members of a room that the bot is in.
     * This includes the user IDs of users who have not added the bot as a friend or has blocked the bot.
     * This method gets all of the members by calling getRoomMemberIds() continually using token
     *
     * This feature is only available for LINE@ Approved accounts or official accounts.
     *
     * @param string $roomId Identifier of the room
     * @return array memberIds
     * @see \LINE\LINEBot::getRoomMemberIds()
     */
    public function getAllRoomMemberIds($roomId)
    {
        $memberIds = [];
        $continuationToken = null;
        do {
            $response = $this->getRoomMemberIds($roomId, $continuationToken);
            $data = $response->getJSONDecodedBody();
            $memberIds = array_merge($memberIds, $data['memberIds']);
            $continuationToken = isset($data['next']) ? $data['next'] : null;
        } while ($continuationToken);

        return $memberIds;
    }

    /**
     * Issues a link token used for the account link feature.
     *
     * @param string $userId User ID for the LINE account to be linked.
     * @return Response
     */
    public function createLinkToken($userId)
    {
        $url = sprintf('%s/v2/bot/user/%s/linkToken', $this->endpointBase, urlencode($userId));
        return $this->httpClient->post($url, []);
    }

    /**
     * Gets a rich menu via a rich menu ID.
     *
     * @param string $richMenuId ID of an uploaded rich menu
     * @return Response
     */
    public function getRichMenu($richMenuId)
    {
        $url = sprintf('%s/v2/bot/richmenu/%s', $this->endpointBase, urlencode($richMenuId));
        return $this->httpClient->get($url, []);
    }

    /**
     * Creates a rich menu.
     *
     * You must upload a rich menu image and link the rich menu to a user for the rich menu to be displayed.
     *
     * @param RichMenuBuilder $richMenuBuilder
     * @return Response
     */
    public function createRichMenu($richMenuBuilder)
    {
        return $this->httpClient->post($this->endpointBase . '/v2/bot/richmenu', $richMenuBuilder->build());
    }

     /**
     * Deletes a rich menu.
     *
     * @param string $richMenuId ID of an uploaded rich menu
     * @return Response
     */
    public function deleteRichMenu($richMenuId)
    {
        $url = sprintf('%s/v2/bot/richmenu/%s', $this->endpointBase, urlencode($richMenuId));
        return $this->httpClient->delete($url);
    }

    /**
     * Gets the ID of the rich menu linked to a user.
     *
     * @param string $userId User ID. Found in the source object of webhook event objects.
     * @return Response
     */
    public function getRichMenuId($userId)
    {
        $url = sprintf('%s/v2/bot/user/%s/richmenu', $this->endpointBase, urlencode($userId));
        return $this->httpClient->get($url, []);
    }

    /**
     * Links a rich menu to a user. Only one rich menu can be linked to a user at one time.
     *
     * @param string $userId User ID. Found in the source object of webhook event objects.
     * @param string $richMenuId ID of an uploaded rich menu
     * @return Response
     */
    public function linkRichMenu($userId, $richMenuId)
    {
        $url = sprintf(
            '%s/v2/bot/user/%s/richmenu/%s',
            $this->endpointBase,
            urlencode($userId),
            urlencode($richMenuId)
        );
        return $this->httpClient->post($url, []);
    }

    /**
     * Links a rich menu to multiple users.
     *
     * @param string[] $userIds Found in the source object of webhook event objects. Max: 150 user IDs.
     * @param string $richMenuId ID of an uploaded rich menu
     * @return Response
     */
    public function bulkLinkRichMenu($userIds, $richMenuId)
    {
        $url = $this->endpointBase . '/v2/bot/richmenu/bulk/link';
        return $this->httpClient->post($url, [
            'richMenuId' => $richMenuId,
            'userIds' => $userIds
        ]);
    }

    /**
     * Unlinks a rich menu from multiple user.
     *
     * @param string $userId User ID. Found in the source object of webhook event objects.
     * @return Response
     */
    public function unlinkRichMenu($userId)
    {
        $url = sprintf('%s/v2/bot/user/%s/richmenu', $this->endpointBase, urlencode($userId));
        return $this->httpClient->delete($url);
    }

    /**
     * Unlinks rich menus from multiple users.
     *
     * @param string[] $userIds Found in the source object of webhook event objects. Max: 150 user IDs.
     * @return Response
     */
    public function bulkUnlinkRichMenu($userIds)
    {
        $url = $this->endpointBase . '/v2/bot/richmenu/bulk/unlink';
        return $this->httpClient->post($url, [
            'userIds' => $userIds
        ]);
    }

    /**
     * Downloads an image associated with a rich menu.
     *
     * @param string $richMenuId ID of an uploaded rich menu
     * @return Response
     */
    public function downloadRichMenuImage($richMenuId)
    {
        $url = sprintf('%s/v2/bot/richmenu/%s/content', $this->endpointBase, urlencode($richMenuId));
        return $this->httpClient->get($url);
    }

    /**
     * Uploads and attaches an image to a rich menu.
     *
     * Notes:
     * <ul><li>Images must have one of the following resolutions: 2500x1686 or 2500x843 pixels.</li>
     * <li>You cannot replace an image attached to a rich menu. To update your rich menu image,
     * create a new rich menu object and upload another image.</li></ul>
     *
     * @param string $richMenuId ID of an uploaded rich menu
     * @param string $imagePath Path to the image
     * @param string $contentType image/jpeg or image/png
     * @return Response
     */
    public function uploadRichMenuImage($richMenuId, $imagePath, $contentType)
    {
        $url = sprintf('%s/v2/bot/richmenu/%s/content', $this->endpointBase, urlencode($richMenuId));
        return $this->httpClient->post(
            $url,
            [
                '__file' => $imagePath,
                '__type' => $contentType,
            ],
            [ "Content-Type: $contentType" ]
        );
    }

    /**
     * Gets a list of all uploaded rich menus.
     *
     * @return Response
     */
    public function getRichMenuList()
    {
        return $this->httpClient->get($this->endpointBase . '/v2/bot/richmenu/list');
    }

    /**
     * Get number of sent reply messages
     *
     * @param DateTime $datetime Date the messages were sent.
     * @return Response
     */
    public function getNumberOfSentReplyMessages(DateTime $datetime)
    {
        $url = $this->endpointBase . '/v2/bot/message/delivery/reply';
        $datetime->setTimezone(new DateTimeZone('Asia/Tokyo'));
        return $this->httpClient->get($url, ['date' => $datetime->format('Ymd')]);
    }

    /**
     * Get number of sent push messages
     *
     * @param DateTime $datetime Date the messages were sent.
     * @return Response
     */
    public function getNumberOfSentPushMessages(DateTime $datetime)
    {
        $url = $this->endpointBase . '/v2/bot/message/delivery/push';
        $datetime->setTimezone(new DateTimeZone('Asia/Tokyo'));
        return $this->httpClient->get($url, ['date' => $datetime->format('Ymd')]);
    }

    /**
     * Get number of sent multicast messages
     *
     * @param DateTime $datetime Date the messages were sent.
     * @return Response
     */
    public function getNumberOfSentMulticastMessages(DateTime $datetime)
    {
        $url = $this->endpointBase . '/v2/bot/message/delivery/multicast';
        $datetime->setTimezone(new DateTimeZone('Asia/Tokyo'));
        return $this->httpClient->get($url, ['date' => $datetime->format('Ymd')]);
    }
}
