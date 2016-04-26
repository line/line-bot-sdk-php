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
use LINE\LINEBot;
use LINE\LINEBot\Message\MultipleMessages;
use LINE\LINEBot\Receive\Receive;
use LINE\LINEBot\Receive\Message;
use LINE\LINEBot\Receive\Message\Contact;
use LINE\LINEBot\Receive\Message\Location;
use LINE\LINEBot\Receive\Message\Sticker;
use LINE\LINEBot\Receive\Message\Text;
use LINE\LINEBot\Receive\Operation;
use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/callback', function (Request $req, Response $res, $arg) {
    $body = $req->getBody();
    $signatureHeader = $req->getHeader('X-LINE-ChannelSignature');
    if (empty($signatureHeader) || !$this->bot->validateSignature($body, $signatureHeader[0])) {
        return $res->withStatus(400, "Bad Request");
    }

    /** @var LINEBot $bot */
    $bot = $this->bot;

    /** @var Receive[] $receives */
    $receives = $bot->createReceivesFromJSON($body);
    foreach ($receives as $receive) {
        if ($receive->isMessage()) {
            /** @var Message $receive */

            $this->logger->info(sprintf(
                'contentId=%s, fromMid=%s, createdTime=%s',
                $receive->getContentId(),
                $receive->getFromMid(),
                $receive->getCreatedTime()
            ));

            if ($receive->isText()) {
                /** @var Text $receive */
                if ($receive->getText() === 'me') {
                    $ret = $bot->getUserProfile($receive->getFromMid());
                    $contact = $ret['contacts'][0];
                    $multipleMsgs = (new MultipleMessages())
                        ->addText(sprintf(
                            'Hello! %s san! Your status message is %s',
                            $contact['displayName'],
                            $contact['statusMessage']
                        ))
                        ->addImage($contact['pictureUrl'], $contact['pictureUrl'])
                        ->addSticker(mt_rand(0, 10), 1, 100);
                    $bot->sendMultipleMessages($receive->getFromMid(), $multipleMsgs);
                } else {
                    $bot->sendText($receive->getFromMid(), $receive->getText());
                }
            } elseif ($receive->isImage() || $receive->isVideo()) {
                $content = $bot->getMessageContent($receive->getContentId());
                $meta = stream_get_meta_data($content->getFileHandle());
                $contentSize = filesize($meta['uri']);
                $type = $receive->isImage() ? 'image' : 'video';

                $previewContent = $bot->getMessageContentPreview($receive->getContentId());
                $previewMeta = stream_get_meta_data($previewContent->getFileHandle());
                $previewContentSize = filesize($previewMeta['uri']);

                $bot->sendText(
                    $receive->getFromMid(),
                    "Thank you for sending a $type.\nOriginal file size: " .
                    "$contentSize\nPreview file size: $previewContentSize"
                );
            } elseif ($receive->isAudio()) {
                $bot->sendText($receive->getFromMid(), "Thank you for sending a audio.");
            } elseif ($receive->isLocation()) {
                /** @var Location $receive */
                $bot->sendLocation(
                    $receive->getFromMid(),
                    sprintf("%s\n%s", $receive->getText(), $receive->getAddress()),
                    $receive->getLatitude(),
                    $receive->getLongitude()
                );
            } elseif ($receive->isSticker()) {
                /** @var Sticker $receive */
                $bot->sendSticker(
                    $receive->getFromMid(),
                    $receive->getStkId(),
                    $receive->getStkPkgId(),
                    $receive->getStkVer()
                );
            } elseif ($receive->isContact()) {
                /** @var Contact $receive */
                $bot->sendText(
                    $receive->getFromMid(),
                    sprintf("Thank you for sending %s information.", $receive->getDisplayName())
                );
            } else {
                throw new \Exception("Received invalid message type");
            }
        } elseif ($receive->isOperation()) {
            /** @var Operation $receive */

            $this->logger->info(sprintf(
                'revision=%s, fromMid=%s',
                $receive->getRevision(),
                $receive->getFromMid()
            ));

            if ($receive->isAddContact()) {
                $bot->sendText($receive->getFromMid(), "Thank you for adding me to your contact list!");
            } elseif ($receive->isBlockContact()) {
                $this->logger->info("Blocked");
            } else {
                throw new \Exception("Received invalid operation type");
            }
        } else {
            throw new \Exception("Received invalid receive type");
        }
    }

    return $res->getBody()->write("OK");
});
