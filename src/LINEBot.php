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

/**
 * A client class of LINE Messaging API.
 *
 * @package LINE
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
        if (array_key_exists('endpointBase', $args) && !empty($args['endpointBase'])) {
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
     * @param string $replyToken Identifier of destination.
     * @param string $text Text of message.
     * @param string[] $extraTexts Extra text of message.
     * @return Response
     */
    public function replyText($replyToken, $text)
    {
        $numargs = func_num_args();
        for ($i = 2; $i < $numargs; $i++) {
            $extraTexts[] = func_get_arg($i);
        }
        $textMessageBuilder = new TextMessageBuilder($text, $extraTexts);
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
     * @return LINEBot\Event\BaseEvent[]
     */
    public function parseEventRequest($body, $signature)
    {
        return EventRequestParser::parseEventRequest($body, $this->channelSecret, $signature);
    }

    /**
     * Validate request with signature.
     *
     * @param string $body Request body.
     * @param string $signature Signature of request.
     * @return bool Request is valid or not.
     */
    public function validateSignature($body, $signature)
    {
        return SignatureValidator::validateSignature($body, $this->channelSecret, $signature);
    }
}
