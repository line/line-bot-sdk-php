<?php
/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
namespace LINE;

use LINE\LINEBot\Constant\BotAPIChannel;
use LINE\LINEBot\Constant\EventType;
use LINE\LINEBot\Constant\RecipientType;
use LINE\LINEBot\DownloadedContents;
use LINE\LINEBot\Exception\LINEBotAPIException;
use LINE\LINEBot\HTTPClient\HTTPClient;
use LINE\LINEBot\Message\Builder\MessageBuilder;
use LINE\LINEBot\Message\Builder\MultipleMessagesBuilder;
use LINE\LINEBot\Message\Builder\RichMessageBuilder;
use LINE\LINEBot\Message\MultipleMessages;
use LINE\LINEBot\Message\RichMessage\Markup;
use LINE\LINEBot\Receive\Receive;
use LINE\LINEBot\Receive\ReceiveFactory;
use LINE\LINEBot\Response\FailedResponse;
use LINE\LINEBot\Response\ResponseFactory;
use LINE\LINEBot\Response\SucceededResponse;
use LINE\LINEBot\SignatureValidator;

class LINEBot
{
    const VERSION = '1.0.0';

    /** @var HTTPClient */
    private $client;
    /** @var string */
    private $channelId;
    /** @var string */
    private $channelSecret;
    /** @var string */
    private $channelMid;
    /** @var string */
    private $eventAPIEndpoint;
    /** @var string */
    private $botAPIEndpoint;

    /**
     * LINEBot constructor.
     *
     * @param array $args Parameter of bot
     * @param HTTPClient $client
     */
    public function __construct(array $args, HTTPClient $client)
    {
        $this->client = $client;
        $this->channelId = $args['channelId'];
        $this->channelSecret = $args['channelSecret'];
        $this->channelMid = $args['channelMid'];
        $this->eventAPIEndpoint = isset($args['eventAPIEndpoint']) ?
            $args['eventAPIEndpoint'] : 'https://trialbot-api.line.me/v1/events';
        $this->botAPIEndpoint = isset($args['botAPIEndpoint']) ?
            $args['botAPIEndpoint'] : 'https://trialbot-api.line.me/v1';
    }

    /**
     * Send a text message to mid(s).
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_text
     * @param string|array $mid Target user's MID string or MID array.
     * @param string $text String you want to send. Message can contain up to 1024 characters
     * @param int $toType Type of user who will receive the message (Default: 1 = user).
     * @return FailedResponse|SucceededResponse
     */
    public function sendText($mid, $text, $toType = RecipientType::USER)
    {
        return $this->sendMessage($mid, MessageBuilder::buildText($text), $toType);
    }

    /**
     * Send a image to mid(s).
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_image
     * @param string|array $mid Target user's MID string or MID array.
     * @param string $imageURL URL of image. Only JPEG format supported. Image size cannot be larger than 1024×1024.
     * @param string $previewURL URL of thumbnail image. For preview. Only JPEG format supported.
     * Image size cannot be larger than 240×240.
     * @param int $toType Type of user who will receive the message (Default: 1 = user).
     * @return FailedResponse|SucceededResponse
     */
    public function sendImage($mid, $imageURL, $previewURL, $toType = RecipientType::USER)
    {
        return $this->sendMessage($mid, MessageBuilder::buildImage($imageURL, $previewURL), $toType);
    }

    /**
     * Send a video to mid(s).
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_video
     * @param string|array $mid Target user's MID string or MID array.
     * @param string $videoURL URL of the movie. The “mp4” format is recommended.
     * @param string $previewImageURL URL of thumbnail image used as a preview.
     * @param int $toType Type of user who will receive the message (Default: 1 = user).
     * @return FailedResponse|SucceededResponse
     */
    public function sendVideo($mid, $videoURL, $previewImageURL, $toType = RecipientType::USER)
    {
        return $this->sendMessage($mid, MessageBuilder::buildVideo($videoURL, $previewImageURL), $toType);
    }

    /**
     * Send a voice message to mid(s).
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_audio
     * @param string|array $mid Target user's MID string or MID array.
     * @param string $audioURL URL of audio file. The “m4a” format is recommended.
     * @param int $durationMillis Length of voice message. The unit is given in milliseconds.
     * @param int $toType Type of user who will receive the message (Default: 1 = user).
     * @return FailedResponse|SucceededResponse
     */
    public function sendAudio($mid, $audioURL, $durationMillis, $toType = RecipientType::USER)
    {
        return $this->sendMessage($mid, MessageBuilder::buildAudio($audioURL, $durationMillis), $toType);
    }

    /**
     * Send location information to mid(s).
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_location
     * @param string|array $mid Target user's MID string or MID array.
     * @param string $text String used to explain the location information (example: name of restaurant, address).
     * @param float $latitude Latitude.
     * @param float $longitude Longitude.
     * @param int $toType Type of user who will receive the message (Default: 1 = user).
     * @return FailedResponse|SucceededResponse
     */
    public function sendLocation($mid, $text, $latitude, $longitude, $toType = RecipientType::USER)
    {
        return $this->sendMessage($mid, MessageBuilder::buildLocation($text, $latitude, $longitude), $toType);
    }

    /**
     * Send a sticker to mid(s).
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_sticker
     * @param string|array $mid Target user's MID string or MID array.
     * @param int $stkid ID of the sticker.
     * @param int $stkpkgid Package ID of the sticker.
     * @param int $stkver Version number of the sticker. If omitted, the latest version number is applied.
     * @param int $toType Type of user who will receive the message (Default: 1 = user).
     * @return FailedResponse|SucceededResponse
     */
    public function sendSticker($mid, $stkid, $stkpkgid, $stkver = null, $toType = RecipientType::USER)
    {
        return $this->sendMessage($mid, MessageBuilder::buildSticker($stkid, $stkpkgid, $stkver), $toType);
    }

    /**
     * Send a rich message to mid(s).
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_rich_content_message_request
     * @param string|array $mid Target user's MID string or MID array.
     * @param string $imageURL URL of image which is on your server.
     * @param string $altText Alternative string displayed on low-level devices.
     * @param Markup $markup Markup json of rich message object.
     * @param int $toType Type of user who will receive the message (Default: 1 = user).
     * @return FailedResponse|SucceededResponse
     */
    public function sendRichMessage($mid, $imageURL, $altText, Markup $markup, $toType = RecipientType::USER)
    {
        return $this->sendMessage($mid, RichMessageBuilder::buildRichMessage($imageURL, $altText, $markup), $toType);
    }

    /**
     * Send multiple messages to mid(s).
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_multiple_messages_request
     * @param string|array $mid Target user's MID string or MID array.
     * @param MultipleMessages $multipleMessages Multiple messages to send.
     * @param int $messageNotified Zero-based index of the message to be notified. Default value is 0.
     * @return FailedResponse|SucceededResponse
     */
    public function sendMultipleMessages($mid, MultipleMessages $multipleMessages, $messageNotified = 0)
    {
        $multipleMessages = MultipleMessagesBuilder::buildMultipleMessages($multipleMessages, $messageNotified);
        return $this->sendMessage($mid, $multipleMessages, null, EventType::SENDING_MULTIPLE_MESSAGES);
    }

    /**
     * Retrieve the content of a user's message which is an image or video file.
     *
     * @link https://developers.line.me/bot-api/api-reference#getting_message_content_request
     * @param string $messageId ID of the message.
     * @param resource $fileHandler File handler to store contents temporally.
     * @return DownloadedContents
     */
    public function getMessageContent($messageId, $fileHandler = null)
    {
        return $this->client->downloadContents($this->botAPIEndpoint . "/bot/message/$messageId/content", $fileHandler);
    }

    /**
     * Retrieve thumbnail preview of the message.
     *
     * @link https://developers.line.me/bot-api/api-reference#getting_message_content_preview_request
     * @param string $messageId ID of the message.
     * @param resource $fileHandler File handler to store contents temporally.
     * @return DownloadedContents
     */
    public function getMessageContentPreview($messageId, $fileHandler = null)
    {
        return $this->client
            ->downloadContents($this->botAPIEndpoint . "/bot/message/$messageId/content/preview", $fileHandler);
    }

    /**
     * Retrieve user profiles.
     *
     * @link https://developers.line.me/bot-api/api-reference#getting_user_profile_information_request
     * @param string|array $mid Array of MIDs to retrieve user profile.
     * @return array User profiles.
     * @throws LINEBotAPIException When request is failed or received invalid response.
     */
    public function getUserProfile($mid)
    {
        $query = http_build_query(
            ['mids' => (is_array($mid) ? implode(',', $mid) : $mid)]
        );
        return $this->client->get($this->botAPIEndpoint . '/profiles?' . $query);
    }

    /**
     * Validate signature.
     *
     * @param string $json JSON body.
     * @param string $signature The signature to validate.
     * @return bool
     */
    public function validateSignature($json, $signature)
    {
        return SignatureValidator::validateSignature($json, $this->channelSecret, $signature);
    }

    /**
     * Create receives from JSON request string.
     *
     * @param string $json JSON body.
     * @return Receive[]
     */
    public function createReceivesFromJSON($json)
    {
        return ReceiveFactory::createFromJSON([
            'channelId' => $this->channelId,
            'channelSecret' => $this->channelSecret,
            'channelMid' => $this->channelMid,
        ], $json);
    }

    /**
     * Send a message.
     *
     * @param string|array $mid
     * @param array $data
     * @param int $toType
     * @param string $eventType
     * @return FailedResponse|SucceededResponse
     */
    private function sendMessage(
        $mid,
        array $data,
        $toType = RecipientType::USER,
        $eventType = EventType::SENDING_MESSAGE
    ) {
        return $this->postMessage([
            'to' => is_array($mid) ? $mid : [$mid],
            'content' => array_merge(['toType' => $toType], $data),
        ], $eventType);
    }

    /**
     * POST a message.
     *
     * @param array $data
     * @param string $eventType
     * @return FailedResponse|SucceededResponse
     */
    private function postMessage(array $data, $eventType = EventType::SENDING_MESSAGE)
    {
        $data['toChannel'] = BotAPIChannel::SENDING_CHANNEL_ID;
        $data['eventType'] = $eventType;

        $res = $this->client->post($this->eventAPIEndpoint, $data);
        return ResponseFactory::createResponse($res);
    }
}
