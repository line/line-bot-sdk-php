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
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Narrowcast\DemographicFilter\DemographicFilterBuilder;
use LINE\LINEBot\Narrowcast\Recipient\RecipientBuilder;
use LINE\LINEBot\Response;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\RichMenuBuilder;
use ReflectionClass;
use DateTime;
use DateTimeZone;
use CURLFile;

/**
 * A client class of LINE Messaging API.
 *
 * @package LINE
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class LINEBot
{
    const DEFAULT_ENDPOINT_BASE = 'https://api.line.me';
    const DEFAULT_DATA_ENDPOINT_BASE = 'https://api-data.line.me';

    /** @var string */
    private $channelSecret;
    /** @var string */
    private $endpointBase;
    /** @var string */
    private $dataEndpointBase;
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
        $this->dataEndpointBase = LINEBot::DEFAULT_DATA_ENDPOINT_BASE;
        if (array_key_exists('dataEndpointBase', $args) && !empty($args['dataEndpointBase'])) {
            $this->dataEndpointBase = $args['dataEndpointBase'];
        }
    }

    /**
     * Get basic information about bot.
     *
     * @return Response
     */
    public function getBotInfo()
    {
        return $this->httpClient->get($this->endpointBase . '/v2/bot/info');
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
        $url = $this->dataEndpointBase . '/v2/bot/message/' . urlencode($messageId) . '/content';
        return $this->httpClient->get($url);
    }

    /**
     * Gets the target limit for additional messages in the current month.
     *
     * @return Response
     */
    public function getNumberOfLimitForAdditional()
    {
        return $this->httpClient->get($this->endpointBase . '/v2/bot/message/quota');
    }

    /**
     * Gets the number of messages sent in the current month.
     *
     * @return Response
     */
    public function getNumberOfSentThisMonth()
    {
        return $this->httpClient->get($this->endpointBase . '/v2/bot/message/quota/consumption');
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
     * @param boolean $notificationDisabled Don't send push notifications(=true) or send(=false)
     * @param string|null $retryKey UUID(example: 123e4567-e89b-12d3-a456-426614174000) or Not needed retry(=null)
     * @return Response
     */
    public function pushMessage($to, MessageBuilder $messageBuilder, $notificationDisabled = false, $retryKey = null)
    {
        $headers = ['Content-Type: application/json; charset=utf-8'];
        if (isset($retryKey)) {
            $headers[] = HTTPHeader::LINE_RETRY_KEY . ': ' .$retryKey;
        }
        return $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
            'to' => $to,
            'messages' => $messageBuilder->buildMessage(),
            'notificationDisabled' => $notificationDisabled,
        ], $headers);
    }

    /**
     * Sends arbitrary message to multi destinations.
     *
     * @param array $tos Identifiers of destination.
     * @param MessageBuilder $messageBuilder Message builder to send.
     * @param boolean $notificationDisabled Don't send push notifications(=true) or send(=false)
     * @param string|null $retryKey UUID(example: 123e4567-e89b-12d3-a456-426614174000) or Not needed retry(=null)
     * @return Response
     */
    public function multicast(
        array $tos,
        MessageBuilder $messageBuilder,
        $notificationDisabled = false,
        $retryKey = null
    ) {
        $headers = ['Content-Type: application/json; charset=utf-8'];
        if (isset($retryKey)) {
            $headers[] = HTTPHeader::LINE_RETRY_KEY . ': ' .$retryKey;
        }
        return $this->httpClient->post($this->endpointBase . '/v2/bot/message/multicast', [
            'to' => $tos,
            'messages' => $messageBuilder->buildMessage(),
            'notificationDisabled' => $notificationDisabled,
        ], $headers);
    }

    /**
     * Sends push messages to multiple users at any time.
     * LINE@ accounts cannot call this API endpoint. Please migrate it to a LINE official account.
     *
     * @param MessageBuilder $messageBuilder Message builder to send.
     * @param boolean $notificationDisabled Don't send push notifications(=true) or send(=false)
     * @param string|null $retryKey UUID(example: 123e4567-e89b-12d3-a456-426614174000) or Not needed retry(=null)
     * @return Response
     */
    public function broadcast(MessageBuilder $messageBuilder, $notificationDisabled = false, $retryKey = null)
    {
        $headers = ['Content-Type: application/json; charset=utf-8'];
        if (isset($retryKey)) {
            $headers[] = HTTPHeader::LINE_RETRY_KEY . ': ' .$retryKey;
        }
        return $this->httpClient->post($this->endpointBase . '/v2/bot/message/broadcast', [
            'messages' => $messageBuilder->buildMessage(),
            'notificationDisabled' => $notificationDisabled,
        ], $headers);
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
     * Get group summary
     *
     * Gets the group ID, group name, and group icon URL of a group where the LINE Official Account is a member.
     *
     * @param string $groupId Group ID
     * @return Response
     */
    public function getGroupSummary($groupId)
    {
        $url = sprintf('%s/v2/bot/group/%s/summary', $this->endpointBase, urlencode($groupId));
        return $this->httpClient->get($url);
    }

    /**
     * Gets the count of members in a group
     *
     * The number returned excludes the LINE Official Account.
     *
     * @param string $groupId Group ID
     * @return Response
     */
    public function getGroupMembersCount($groupId)
    {
        $url = sprintf('%s/v2/bot/group/%s/members/count', $this->endpointBase, urlencode($groupId));
        return $this->httpClient->get($url);
    }

    /**
     * Gets the count of members in a room
     *
     * The number returned excludes the LINE Official Account.
     *
     * @param string $roomId Room ID
     * @return Response
     */
    public function getRoomMembersCount($roomId)
    {
        $url = sprintf('%s/v2/bot/room/%s/members/count', $this->endpointBase, urlencode($roomId));
        return $this->httpClient->get($url);
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
     * Set the default rich menu.
     *
     * @param string $richMenuId ID of an uploaded rich menu
     * @return Response
     */
    public function setDefaultRichMenuId($richMenuId)
    {
        $url = sprintf('%s/v2/bot/user/all/richmenu/%s', $this->endpointBase, urlencode($richMenuId));
        return $this->httpClient->post($url, []);
    }

    /**
     * Get the default rich menu ID.
     *
     * @return Response
     */
    public function getDefaultRichMenuId()
    {
        $url = $this->endpointBase . '/v2/bot/user/all/richmenu';
        return $this->httpClient->get($url);
    }

    /**
     * Cancel the default rich menu.
     *
     * @return Response
     */
    public function cancelDefaultRichMenuId()
    {
        $url = $this->endpointBase . '/v2/bot/user/all/richmenu';
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
        $url = sprintf('%s/v2/bot/richmenu/%s/content', $this->dataEndpointBase, urlencode($richMenuId));
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
        $url = sprintf('%s/v2/bot/richmenu/%s/content', $this->dataEndpointBase, urlencode($richMenuId));
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

    /**
     * Get number of sent broadcast messages
     *
     * @param DateTime $datetime Date the messages were sent.
     * @return Response
     */
    public function getNumberOfSentBroadcastMessages(DateTime $datetime)
    {
        $url = $this->endpointBase . '/v2/bot/message/delivery/broadcast';
        $datetime->setTimezone(new DateTimeZone('Asia/Tokyo'));
        return $this->httpClient->get($url, ['date' => $datetime->format('Ymd')]);
    }

    /**
     * Get number of message deliveries
     *
     * @param DateTime $datetime Date for which to retrieve number of sent messages.
     * @return Response
     */
    public function getNumberOfMessageDeliveries(DateTime $datetime)
    {
        $url = $this->endpointBase . '/v2/bot/insight/message/delivery';
        $datetime->setTimezone(new DateTimeZone('Asia/Tokyo'));
        return $this->httpClient->get($url, ['date' => $datetime->format('Ymd')]);
    }

    /**
     * Get number of followers
     *
     * @param DateTime $datetime Date for which to retrieve the number of followers.
     * @return Response
     */
    public function getNumberOfFollowers(DateTime $datetime)
    {
        $url = $this->endpointBase . '/v2/bot/insight/followers';
        $datetime->setTimezone(new DateTimeZone('Asia/Tokyo'));
        return $this->httpClient->get($url, ['date' => $datetime->format('Ymd')]);
    }

    /**
     * Get friend demographics
     *
     * It can take up to 3 days for demographic information to be calculated.
     * This means the information the API returns may be 3 days old.
     * Furthermore, your Target reach number must be at least 20 to retrieve demographic information.
     *
     * @return Response
     */
    public function getFriendDemographics()
    {
        $url = $this->endpointBase . '/v2/bot/insight/demographic';
        return $this->httpClient->get($url);
    }

    /**
     * Get user interaction statistics
     *
     * Returns statistics about how users interact with broadcast messages sent from your LINE official account.
     * Interactions are tracked for only 14 days after a message was sent.
     * The statistics are no longer updated after 15 days.
     *
     * @param string $requestId Request ID of broadcast message.
     * @return Response
     */
    public function getUserInteractionStatistics($requestId)
    {
        $url = $this->endpointBase . '/v2/bot/insight/message/event';
        return $this->httpClient->get($url, ['requestId' => $requestId]);
    }

    /**
     * Create channel access token
     *
     * Create a short-lived channel access token.
     * Up to 30 tokens can be issued.
     * If the maximum is exceeded,
     * existing channel access tokens are revoked in the order of when they were first issued.
     *
     * @param string $channelId
     * @return Response
     */
    public function createChannelAccessToken($channelId)
    {
        $url = $this->endpointBase . '/v2/oauth/accessToken';
        return $this->httpClient->post(
            $url,
            [
                'grant_type' => 'client_credentials',
                'client_id' => $channelId,
                'client_secret' => $this->channelSecret,
            ],
            ['Content-Type: application/x-www-form-urlencoded']
        );
    }

    /**
     * Revoke channel access token
     *
     * Revokes a channel access token.
     *
     * @param string $channelAccessToken
     * @return Response
     */
    public function revokeChannelAccessToken($channelAccessToken)
    {
        $url = $this->endpointBase . '/v2/oauth/revoke';
        return $this->httpClient->post(
            $url,
            ['access_token' => $channelAccessToken],
            ['Content-Type: application/x-www-form-urlencoded']
        );
    }

    /**
     * Create channel access token v2.1
     *
     * You can issue up to 30 tokens.
     * If you reach the maximum limit, additional requests of issuing channel access tokens are blocked.
     *
     * @see https://developers.line.biz/en/docs/messaging-api/generate-json-web-token/#generate_jwt
     * @param string $jwt
     * @return Response
     */
    public function createChannelAccessToken21($jwt)
    {
        $url = $this->endpointBase . '/oauth2/v2.1/token';
        return $this->httpClient->post(
            $url,
            [
                'grant_type' => 'client_credentials',
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => $jwt,
            ],
            ['Content-Type: application/x-www-form-urlencoded']
        );
    }

    /**
     * Revoke channel access token v2.1
     *
     * @param string $channelId
     * @param string $channelSecret
     * @param string $channelAccessToken
     * @return Response
     */
    public function revokeChannelAccessToken21($channelId, $channelSecret, $channelAccessToken)
    {
        $url = $this->endpointBase . '/oauth2/v2.1/revoke';
        return $this->httpClient->post(
            $url,
            [
                'client_id' => $channelId,
                'client_secret' => $channelSecret,
                'access_token' => $channelAccessToken,
            ],
            ['Content-Type: application/x-www-form-urlencoded']
        );
    }

    /**
     * Get all valid channel access token key IDs v2.1
     *
     * @param string $jwt
     * @return Response
     */
    public function getChannelAccessToken21Keys($jwt)
    {
        $url = $this->endpointBase . '/oauth2/v2.1/tokens/kid';
        return $this->httpClient->get(
            $url,
            [
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => $jwt,
            ]
        );
    }

    /**
     * Send Narrowcast message.
     *
     * @param MessageBuilder $messageBuilder
     * @param RecipientBuilder|null $recipientBuilder
     * @param DemographicFilterBuilder|null $demographicFilterBuilder
     * @param int|null $limit
     * @param string|null $retryKey UUID(example: 123e4567-e89b-12d3-a456-426614174000) or Not needed retry(=null)
     * @return Response
     */
    public function sendNarrowcast(
        MessageBuilder $messageBuilder,
        RecipientBuilder $recipientBuilder = null,
        DemographicFilterBuilder $demographicFilterBuilder = null,
        $max = null,
        $retryKey = null,
        $upToRemainingQuota = false
    ) {
        $params = [
            'messages' => $messageBuilder->buildMessage()
        ];
        if (isset($recipientBuilder)) {
            $params['recipient'] = $recipientBuilder->build();
        }
        if (isset($demographicFilterBuilder)) {
            $params['filter'] = [
                'demographic' => $demographicFilterBuilder->build(),
            ];
        }

        $params['limit'] = [
            'upToRemainingQuota' => $upToRemainingQuota,
        ];
        if (isset($max)) {
            $params['limit']['max'] = $max;
        }
        $headers = ['Content-Type: application/json; charset=utf-8'];
        if (isset($retryKey)) {
            $headers[] = HTTPHeader::LINE_RETRY_KEY . ': ' .$retryKey;
        }
        return $this->httpClient->post($this->endpointBase . '/v2/bot/message/narrowcast', $params, $headers);
    }

    /**
     * Get Narrowcast message sending progress.
     *
     * @param string $requestId
     * @return Response
     */
    public function getNarrowcastProgress($requestId)
    {
        $url = $this->endpointBase . '/v2/bot/message/progress/narrowcast';
        return $this->httpClient->get($url, ['requestId' => $requestId]);
    }

    /**
     * Create audience for uploading user IDs
     *
     * @param string $description The audience's name. Max character limit: 120
     * @param array $audiences An array of up to 10,000 user IDs or IFAs.
     * @param bool $isIfaAudience If this is false (default), recipients are specified by user IDs.
     * @param string|null $uploadDescription The description to register with the job.
     * @return Response
     */
    public function createAudienceGroupForUploadingUserIds(
        $description,
        $audiences = [],
        $isIfaAudience = false,
        $uploadDescription = null
    ) {
        $params = [
            'description' => $description,
            'isIfaAudience' => $isIfaAudience,
        ];
        if (!empty($audiences)) {
            $params['audiences'] = $audiences;
        }
        if (isset($uploadDescription)) {
            $params['uploadDescription'] = $uploadDescription;
        }
        return $this->httpClient->post($this->endpointBase . '/v2/bot/audienceGroup/upload', $params);
    }

    /**
     * Create audience for uploading user IDs (by file)
     *
     * @param string $description The audience's name. Max character limit: 120
     * @param string $filePath A text file path with one user ID or IFA entered per line. Max number: 1,500,000
     * @param bool $isIfaAudience If this is false (default), recipients are specified by user IDs.
     * @param string|null $uploadDescription The description to register with the job.
     * @return Response
     */
    public function createAudienceGroupForUploadingUserIdsByFile(
        $description,
        $filePath,
        $isIfaAudience = false,
        $uploadDescription = null
    ) {
        $params = [
            'description' => $description,
            'isIfaAudience' => $isIfaAudience,
            'file' => new CURLFile($filePath, 'text/plain', 'file'),
        ];
        if (isset($uploadDescription)) {
            $params['uploadDescription'] = $uploadDescription;
        }
        $url = $this->dataEndpointBase . '/v2/bot/audienceGroup/upload/byFile';
        $headers = ['Content-Type: multipart/form-data'];
        return $this->httpClient->post($url, $params, $headers);
    }

    /**
     * Add user IDs or Identifiers for Advertisers (IFAs) to an audience for uploading user IDs
     *
     * @param int $audienceGroupId The audience ID.
     * @param array $audiences An array of up to 10,000 user IDs or IFAs.
     * @param string|null $uploadDescription The description to register with the job.
     * @return Response
     */
    public function updateAudienceGroupForUploadingUserIds(
        $audienceGroupId,
        $audiences,
        $uploadDescription = null
    ) {
        $params = [
            'audienceGroupId' => $audienceGroupId,
            'audiences' => $audiences,
        ];
        if (isset($uploadDescription)) {
            $params['uploadDescription'] = $uploadDescription;
        }
        return $this->httpClient->put($this->endpointBase . '/v2/bot/audienceGroup/upload', $params);
    }

    /**
     * Add user IDs or Identifiers for Advertisers (IFAs) to an audience for uploading user IDs (by file)
     *
     * @param int $audienceGroupId The audience ID.
     * @param string $filePath A text file path with one user ID or IFA entered per line. Max number: 1,500,000
     * @param string|null $uploadDescription The description to register with the job.
     * @return Response
     */
    public function updateAudienceGroupForUploadingUserIdsByFile(
        $audienceGroupId,
        $filePath,
        $uploadDescription = null
    ) {
        $params = [
            'audienceGroupId' => $audienceGroupId,
            'file' => new CURLFile($filePath, 'text/plain', 'file'),
        ];
        if (isset($uploadDescription)) {
            $params['uploadDescription'] = $uploadDescription;
        }
        $url = $this->dataEndpointBase . '/v2/bot/audienceGroup/upload/byFile';
        $headers = ['Content-Type: multipart/form-data'];
        return $this->httpClient->put($url, $params, $headers);
    }

    /**
     * Create audience for click-based retargeting
     *
     * @param string $description The audience's name. Max character limit: 120
     * @param string $requestId The request ID of a broadcast or narrowcast message sent in the past 60 days.
     * @param string|null $clickUrl The URL clicked by the user. Max character limit: 2,000
     * @return Response
     */
    public function createAudienceGroupForClick($description, $requestId, $clickUrl = null)
    {
        $params = [
            'description' => $description,
            'requestId' => $requestId,
        ];
        if (isset($clickUrl)) {
            $params['clickUrl'] = $clickUrl;
        }
        return $this->httpClient->post($this->endpointBase . '/v2/bot/audienceGroup/click', $params);
    }

    /**
     * Create audience for impression-based retargeting
     *
     * @param string $description The audience's name. Max character limit: 120
     * @param string $requestId The request ID of a broadcast or narrowcast message sent in the past 60 days.
     * @return Response
     */
    public function createAudienceGroupForImpression($description, $requestId)
    {
        return $this->httpClient->post($this->endpointBase . '/v2/bot/audienceGroup/imp', [
            'description' => $description,
            'requestId' => $requestId,
        ]);
    }

    /**
     * Rename an audience
     *
     * @param int $audienceGroupId The audience ID.
     * @param string $description The audience's name. Max character limit: 120
     * @return Response
     */
    public function renameAudience($audienceGroupId, $description)
    {
        $url = sprintf($this->endpointBase . '/v2/bot/audienceGroup/%s/updateDescription', urlencode($audienceGroupId));
        return $this->httpClient->put($url, ['description' => $description]);
    }

    /**
     * Delete audience
     *
     * @param int $audienceGroupId The audience ID.
     * @return Response
     */
    public function deleteAudience($audienceGroupId)
    {
        $url = sprintf($this->endpointBase . '/v2/bot/audienceGroup/%s', urlencode($audienceGroupId));
        return $this->httpClient->delete($url);
    }

    /**
     * Get audience
     *
     * @param int $audienceGroupId The audience ID.
     * @return Response
     */
    public function getAudience($audienceGroupId)
    {
        $url = sprintf($this->endpointBase . '/v2/bot/audienceGroup/%s', urlencode($audienceGroupId));
        return $this->httpClient->get($url);
    }

    /**
     * Get data for multiple audiences
     *
     * @param int $page The page to return when getting (paginated) results. Must be 1 or higher.
     * @param int $size The number of audiences per page. Max: 40
     * @param string|null $description You can search for partial matches.
     * @param string|null $status One of: IN_PROGRESS, READY, FAILED, EXPIRED
     * @param boolean|null $includesExternalPublicGroups
     * @param string|null $createRoute How the audience was created. One of: OA_MANAGER, MESSAGING_API
     * @return Response
     */
    public function getAudiences(
        $page,
        $size = 20,
        $description = null,
        $status = null,
        $includesExternalPublicGroups = null,
        $createRoute = null
    ) {
        $params = [
            'page' => $page,
            'size' => $size,
        ];
        if (isset($description)) {
            $params['description'] = $description;
        }
        if (isset($status)) {
            $params['status'] = $status;
        }
        if (isset($includesExternalPublicGroups)) {
            $params['includesExternalPublicGroups'] = $includesExternalPublicGroups;
        }
        if (isset($createRoute)) {
            $params['createRoute'] = $createRoute;
        }
        return $this->httpClient->get($this->endpointBase . '/v2/bot/audienceGroup/list', $params);
    }

    /**
     * Get the authority level of the audience
     *
     * @return Response
     */
    public function getAuthorityLevel()
    {
        return $this->httpClient->get($this->endpointBase . '/v2/bot/audienceGroup/authorityLevel');
    }

    /**
     * Change the authority level of the audience
     *
     * @param string $authorityLevel One of: PUBLIC, PRIVATE
     * @return Response
     */
    public function updateAuthorityLevel($authorityLevel)
    {
        return $this->httpClient->put($this->endpointBase . '/v2/bot/audienceGroup/authorityLevel', [
            'authorityLevel' => $authorityLevel,
        ]);
    }

    /**
     * Get webhook endpoint information
     *
     * @return Response
     */
    public function getWebhookEndpointInfo()
    {
        return $this->httpClient->get($this->endpointBase . '/v2/bot/channel/webhook/endpoint');
    }

    /**
     * Set webhook endpoint URL
     *
     * @param string $endpoint
     * @return Response
     */
    public function setWebhookEndpoint($endpoint)
    {
        return $this->httpClient->put($this->endpointBase . '/v2/bot/channel/webhook/endpoint', [
            'endpoint' => $endpoint,
        ]);
    }

    /**
     * Checks if the configured webhook endpoint can receive a test webhook event
     *
     * @param string $endpoint
     * @return Response
     */
    public function testWebhookEndpoint($endpoint)
    {
        return $this->httpClient->post($this->endpointBase . '/v2/bot/channel/webhook/test', [
            'endpoint' => $endpoint,
        ]);
    }
}
