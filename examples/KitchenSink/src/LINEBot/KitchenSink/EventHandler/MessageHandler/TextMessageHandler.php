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

namespace LINE\LINEBot\KitchenSink\EventHandler\MessageHandler;

use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\ButtonsTemplate;
use LINE\Clients\MessagingApi\Model\CameraAction;
use LINE\Clients\MessagingApi\Model\CameraRollAction;
use LINE\Clients\MessagingApi\Model\CarouselColumn;
use LINE\Clients\MessagingApi\Model\CarouselTemplate;
use LINE\Clients\MessagingApi\Model\ConfirmTemplate;
use LINE\Clients\MessagingApi\Model\DatetimePickerAction;
use LINE\Clients\MessagingApi\Model\Emoji;
use LINE\Clients\MessagingApi\Model\ImagemapArea;
use LINE\Clients\MessagingApi\Model\ImagemapBaseSize;
use LINE\Clients\MessagingApi\Model\ImagemapExternalLink;
use LINE\Clients\MessagingApi\Model\ImagemapMessage;
use LINE\Clients\MessagingApi\Model\ImagemapVideo;
use LINE\Clients\MessagingApi\Model\LocationAction;
use LINE\Clients\MessagingApi\Model\Message;
use LINE\Clients\MessagingApi\Model\MessageAction;
use LINE\Clients\MessagingApi\Model\MessageImagemapAction;
use LINE\Clients\MessagingApi\Model\PostbackAction;
use LINE\Clients\MessagingApi\Model\QuickReply;
use LINE\Clients\MessagingApi\Model\QuickReplyItem;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TemplateMessage;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Clients\MessagingApi\Model\URIAction;
use LINE\Clients\MessagingApi\Model\URIImagemapAction;
use LINE\LINEBot\KitchenSink\EventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\Flex\FlexSampleRestaurant;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\Flex\FlexSampleShopping;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\Util\UrlBuilder;
use LINE\Constants\ActionType;
use LINE\Constants\MessageType;
use LINE\Constants\TemplateType;
use LINE\Webhook\Model\GroupSource;
use LINE\Webhook\Model\MessageEvent;
use LINE\Webhook\Model\RoomSource;
use LINE\Webhook\Model\TextMessageContent;

/**
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 * @SuppressWarnings("PHPMD.CyclomaticComplexity")
 * @SuppressWarnings("PHPMD.ExcessiveMethodLength")
 */
class TextMessageHandler implements EventHandler
{
    /** @var MessagingApiApi $bot */
    private $bot;
    /** @var \Psr\Log\LoggerInterface $logger */
    private $logger;
    /** @var \Slim\Http\Request $logger */
    private $req;
    /** @var TextMessageContent $textMessage */
    private $textMessage;
    /** @var MessageEvent $event */
    private $event;

    /**
     * TextMessageHandler constructor.
     * @param $bot
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Psr\Http\Message\RequestInterface $req
     * @param TextMessageContent $textMessage
     */
    public function __construct(MessagingApiApi $bot, \Psr\Log\LoggerInterface $logger, \Psr\Http\Message\RequestInterface $req, MessageEvent $event)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->req = $req;
        $this->event = $event;
        $this->textMessage = $event->getMessage();
    }

    /**
     * @throws \LINE\Parser\Exception\InvalidEventSourceException
     * @throws \ReflectionException
     */
    public function handle()
    {
        $text = $this->textMessage->getText();
        $replyToken = $this->event->getReplyToken();
        $source = $this->event->getSource();
        $this->logger->info("Got text message from $replyToken: $text");

        switch ($text) {
            case 'profile':
                $userId = $source->getUserId();
                $this->sendProfile($replyToken, $userId);
                break;
            case 'bye':
                if ($source instanceof RoomSource) {
                    $this->replyText($replyToken, 'Leaving room');
                    $this->bot->leaveRoom($source->getRoomId());
                    break;
                }
                if ($source instanceof GroupSource) {
                    $this->replyText($replyToken, 'Leaving group');
                    $this->bot->leaveGroup($source->getGroupId());
                    break;
                }
                $this->replyText($replyToken, 'Bot cannot leave from 1:1 chat');
                break;
            case 'confirm':
                $templateMessage = new TemplateMessage([
                    'type' => MessageType::TEMPLATE,
                    'altText' => 'Confirm alt text',
                    'template' => new ConfirmTemplate([
                        'type' => TemplateType::CONFIRM,
                        'text' => 'Do it?',
                        'actions' => [
                            new MessageAction([
                                'type' => ActionType::MESSAGE,
                                'label' => 'Yes',
                                'text' => 'Yes!',
                            ]),
                            new MessageAction([
                                'type' => ActionType::MESSAGE,
                                'label' => 'No',
                                'text' => 'No!',
                            ]),
                        ],
                    ]),
                ]);
                $this->replyMessage($replyToken, $templateMessage);
                break;
            case 'buttons':
                $imageUrl = UrlBuilder::buildUrl($this->req, ['static', 'buttons', '1040.jpg']);
                $this->logger->info('imageUrl: ' . $imageUrl);
                $templateMessage = new TemplateMessage([
                    'type' => MessageType::TEMPLATE,
                    'altText' => 'Button alt text',
                    'template' => new ButtonsTemplate([
                        'type' => TemplateType::BUTTONS,
                        'title' => 'My button sample',
                        'text' => 'Hello my button',
                        'thumbnailImageUrl' => $imageUrl,
                        'actions' => [
                            new URIAction([
                                'type' => ActionType::URI,
                                'label' => 'Go to line.me',
                                'uri' => 'https://line.me',
                            ]),
                            new PostbackAction([
                                'type' => ActionType::POSTBACK,
                                'label' => 'Buy',
                                'data' => 'action=buy&itemid=123',
                            ]),
                            new PostbackAction([
                                'type' => ActionType::POSTBACK,
                                'label' => 'Add to cart',
                                'data' => 'action=add&itemid=123',
                            ]),
                            new MessageAction([
                                'type' => ActionType::MESSAGE,
                                'label' => 'Say message',
                                'text' => 'hello hello',
                            ]),
                        ],
                    ]),
                ]);
                $this->replyMessage($replyToken, $templateMessage);
                break;
            case 'carousel':
                $imageUrl = UrlBuilder::buildUrl($this->req, ['static', 'buttons', '1040.jpg']);
                $templateMessage = new TemplateMessage([
                    'type' => MessageType::TEMPLATE,
                    'altText' => 'Button alt text',
                    'template' => new CarouselTemplate([
                        'type' => TemplateType::CAROUSEL,
                        'columns' => [
                            new CarouselColumn([
                                'title' => 'foo',
                                'text' => 'bar',
                                'thumbnailImageUrl' => $imageUrl,
                                'actions' => [
                                    new URIAction([
                                        'type' => ActionType::URI,
                                        'label' => 'Go to line.me',
                                        'uri' => 'https://line.me',
                                    ]),
                                    new PostbackAction([
                                        'type' => ActionType::POSTBACK,
                                        'label' => 'Buy',
                                        'data' => 'action=buy&itemid=123',
                                    ]),
                                ],
                            ]),
                            new CarouselColumn([
                                'title' => 'buz',
                                'text' => 'qux',
                                'thumbnailImageUrl' => $imageUrl,
                                'actions' => [
                                    new PostbackAction([
                                        'type' => ActionType::POSTBACK,
                                        'label' => 'Add to cart',
                                        'data' => 'action=add&itemid=123',
                                    ]),
                                    new MessageAction([
                                        'type' => ActionType::MESSAGE,
                                        'label' => 'Say message',
                                        'text' => 'hello hello',
                                    ]),
                                ],
                            ]),
                        ],
                    ]),
                ]);
                $this->replyMessage($replyToken, $templateMessage);
                break;
            case 'imagemap':
                $richMessageUrl = UrlBuilder::buildUrl($this->req, ['static', 'rich']);
                $imagemapMessage = new ImagemapMessage([
                    'type' => MessageType::IMAGEMAP,
                    'baseUrl' => $richMessageUrl,
                    'altText' => 'This is alt text',
                    'baseSize' => new ImagemapBaseSize([
                        'width' => 1040,
                        'height' => 1040,
                    ]),
                    'actions' => [
                        new URIImagemapAction([
                            'type' => ActionType::URI,
                            'linkUri' => 'https://store.line.me/family/manga/en',
                            'area' => new ImagemapArea([
                                'x' => 0,
                                'y' => 0,
                                'width' => 520,
                                'height' => 520,
                            ]),
                        ]),
                        new URIImagemapAction([
                            'type' => ActionType::URI,
                            'linkUri' => 'https://store.line.me/family/music/en',
                            'area' => new ImagemapArea([
                                'x' => 520,
                                'y' => 0,
                                'width' => 520,
                                'height' => 520,
                            ]),
                        ]),
                        new URIImagemapAction([
                            'type' => ActionType::URI,
                            'linkUri' => 'https://store.line.me/family/play/en',
                            'area' => new ImagemapArea([
                                'x' => 0,
                                'y' => 520,
                                'width' => 520,
                                'height' => 520,
                            ]),
                        ]),
                        new MessageImagemapAction([
                            'type' => ActionType::MESSAGE,
                            'text' => 'URANAI!',
                            'area' => new ImagemapArea([
                                'x' => 520,
                                'y' => 520,
                                'width' => 520,
                                'height' => 520,
                            ]),
                        ]),
                    ],
                ]);
                $this->replyMessage($replyToken, $imagemapMessage);
                break;
            case 'imagemapVideo':
                $this->logger->info('static: ' . UrlBuilder::buildUrl($this->req, ['static', 'video.mp4']));
                $this->logger->info('static: ' . UrlBuilder::buildUrl($this->req, ['static', 'preview.jpg']));
                $richMessageUrl = UrlBuilder::buildUrl($this->req, ['static', 'rich']);
                $imagemapMessage = new ImagemapMessage([
                    'type' => MessageType::IMAGEMAP,
                    'baseUrl' => $richMessageUrl,
                    'altText' => 'This is alt text',
                    'baseSize' => new ImagemapBaseSize([
                        'width' => 1040,
                        'height' => 1040,
                    ]),
                    'actions' => [
                        new URIImagemapAction([
                            'type' => ActionType::URI,
                            'linkUri' => 'https://store.line.me/family/manga/en',
                            'area' => new ImagemapArea([
                                'x' => 0,
                                'y' => 0,
                                'width' => 520,
                                'height' => 520,
                            ]),
                        ]),
                        new URIImagemapAction([
                            'type' => ActionType::URI,
                            'linkUri' => 'https://store.line.me/family/music/en',
                            'area' => new ImagemapArea([
                                'x' => 520,
                                'y' => 0,
                                'width' => 520,
                                'height' => 520,
                            ]),
                        ]),
                        new URIImagemapAction([
                            'type' => ActionType::URI,
                            'linkUri' => 'https://store.line.me/family/play/en',
                            'area' => new ImagemapArea([
                                'x' => 0,
                                'y' => 520,
                                'width' => 520,
                                'height' => 520,
                            ]),
                        ]),
                        new MessageImagemapAction([
                            'type' => ActionType::MESSAGE,
                            'text' => 'URANAI!',
                            'area' => new ImagemapArea([
                                'x' => 520,
                                'y' => 520,
                                'width' => 520,
                                'height' => 520,
                            ]),
                        ]),
                    ],
                    'video' => new ImagemapVideo([
                        'originalContentUrl' => UrlBuilder::buildUrl($this->req, ['static', 'video.mp4']),
                        'previewImageUrl' => UrlBuilder::buildUrl($this->req, ['static', 'preview.jpg']),
                        'area' => new ImagemapArea([
                            'x' => 260,
                            'y' => 260,
                            'width' => 520,
                            'height' => 520,
                        ]),
                        'externalLink' => new ImagemapExternalLink([
                            'linkUri' => 'https://line.me',
                            'label' => 'LINE',
                        ]),
                    ]),
                ]);
                $this->replyMessage($replyToken, $imagemapMessage);
                break;
            case 'restaurant':
                $this->replyMessage($replyToken, FlexSampleRestaurant::get());
                break;
            case 'shopping':
                $this->replyMessage($replyToken, FlexSampleShopping::get());
                break;
            case 'quickReply':
                $quickReply = new QuickReply([
                    'items' => [
                        new QuickReplyItem([
                            'type' => 'action',
                            'action' => new LocationAction([
                                'type' => ActionType::LOCATION,
                                'label' => 'Location',
                            ]),
                        ]),
                        new QuickReplyItem([
                            'type' => 'action',
                            'action' => new CameraAction([
                                'type' => ActionType::CAMERA,
                                'label' => 'Camera',
                            ]),
                        ]),
                        new QuickReplyItem([
                            'type' => 'action',
                            'action' => new CameraRollAction([
                                'type' => ActionType::CAMERA_ROLL,
                                'label' => 'Camera roll',
                            ]),
                        ]),
                        new QuickReplyItem([
                            'type' => 'action',
                            'action' => new PostbackAction([
                                'type' => ActionType::POSTBACK,
                                'label' => 'Buy',
                                'text' => 'Buy',
                                'data' => 'action=buy&itemid=123',
                            ]),
                        ]),
                        new QuickReplyItem([
                            'type' => 'action',
                            'action' => new DatetimePickerAction([
                                'type' => ActionType::DATETIME_PICKER,
                                'label' => 'Select date',
                                'data' => 'storeId=12345',
                                'mode' => 'datetime',
                                'initial' => '2017-12-25t00:00',
                                'max' => '2018-01-24t23:59',
                                'min' => '2017-12-25t00:00',
                            ]),
                        ]),
                    ]
                ]);

                $message = new TextMessage([
                    'text' => '$ click button! $',
                    'type' => MessageType::TEXT,
                    'emojis' => [
                        new Emoji([
                            'index' => 0,
                            'productId' => '5ac1bfd5040ab15980c9b435',
                            'emojiId' => '001',
                        ]),
                        new Emoji([
                            'index' => 16,
                            'productId' => '5ac1bfd5040ab15980c9b435',
                            'emojiId' => '001',
                        ]),
                    ],
                    'quickReply' => $quickReply,
                ]);
                $request = new ReplyMessageRequest([
                    'replyToken' => $replyToken,
                    'messages' => [$message],
                ]);
                $this->bot->replyMessage($request);
                break;
            case 'error':
                $this->errorCase($replyToken, $userId);
                break;
            case 'http info':
                $this->replyMessageWithHttpInfo($replyToken);
                break;
            default:
                $this->echoBack($replyToken, $text);
                break;
        }
    }

    /**
     * @param string $replyToken
     * @param string $text
     * @throws \ReflectionException
     */
    private function echoBack($replyToken, $text)
    {
        $this->logger->info("Returns echo message $replyToken: $text");
        $this->replyText($replyToken, $text);
    }

    /**
     * @param $replyToken
     * @param $userId
     * @throws \ReflectionException
     */
    private function sendProfile($replyToken, $userId)
    {
        if (!isset($userId)) {
            $this->replyText($replyToken, "Bot can't use profile API without user ID");
            return;
        }

        try {
            $profile = $this->bot->getProfile($userId);
        } catch (\LINE\Clients\MessagingApi\ApiException $e) {
            $this->replyText($replyToken, json_encode($e->getResponseBody()));
            return;
        }

        $this->replyText(
            $replyToken,
            'Display name: ' . $profile->getDisplayName(),
            'Status message: ' . $profile->getStatusMessage(),
        );
    }

    private function replyText(string $replyToken, string $text)
    {
        $textMessage = (new TextMessage(['text' => $text, 'type' => MessageType::TEXT]));
        return $this->replyMessage($replyToken, $textMessage);
    }

    private function replyMessage(string $replyToken, Message $message)
    {
        $request = new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => [$message],
        ]);
        try {
            $this->bot->replyMessage($request);
        } catch (\LINE\Clients\MessagingApi\ApiException $e) {
            $this->logger->info('BODY:' . $e->getResponseBody());
            throw $e;
        }
    }

    private function errorCase(string $replyToken)
    {
        try {
            $profile = $this->bot->getProfile("invalid-userId");
        } catch (\LINE\Clients\MessagingApi\ApiException $e) {
            $headers = $e->getResponseHeaders();
            $lineRequestId = isset($headers['x-line-request-id']) ? $headers['x-line-request-id'][0] : 'Not Available';
            $httpStatusCode = $e->getCode();
            $errorMessage = $e->getResponseBody();

            $this->logger->info("x-line-request-id: $lineRequestId");
            $this->logger->info("http status code: $httpStatusCode");
            $this->logger->info("error response: $errorMessage");
            $this->replyText($replyToken, $errorMessage);
            return;
        }
    }

    private function replyMessageWithHttpInfo(string $replyToken)
    {
        $request = new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => [$textMessage = (new TextMessage(['text' => 'reply with http info', 'type' => MessageType::TEXT]))],
        ]);
        $response = $this->bot->replyMessageWithHttpInfo($request);
        $this->logger->info('body:' . $response[0]);
        $this->logger->info('http status code:' . $response[1]);
        $this->logger->info('headers(x-line-request-id):' . $response[2]['x-line-request-id'][0]);
    }
}
