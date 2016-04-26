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
namespace LINE\LINEBot\Message\Builder;

use LINE\LINEBot\Constant\ContentType;

class MessageBuilder
{
    /**
     * Build text message payload.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_text
     * @param string $text
     * @return array
     */
    public static function buildText($text)
    {
        return [
            'contentType' => ContentType::TEXT,
            'text' => $text,
        ];
    }

    /**
     * Build image message payload.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_image
     * @param string $imageURL
     * @param string $previewURL
     * @return array
     */
    public static function buildImage($imageURL, $previewURL)
    {
        return [
            'contentType' => ContentType::IMAGE,
            'originalContentUrl' => $imageURL,
            'previewImageUrl' => $previewURL,
        ];
    }

    /**
     * Build video message payload.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_video
     * @param string $videoURL
     * @param string $previewImageURL
     * @return array
     */
    public static function buildVideo($videoURL, $previewImageURL)
    {
        return [
            'contentType' => ContentType::VIDEO,
            'originalContentUrl' => $videoURL,
            'previewImageUrl' => $previewImageURL,
        ];
    }

    /**
     * Build voice message payload.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_audio
     * @param string $audioURL
     * @param int $durationMillis
     * @return array
     */
    public static function buildAudio($audioURL, $durationMillis)
    {
        return [
            'contentType' => ContentType::AUDIO,
            'originalContentUrl' => $audioURL,
            'contentMetadata' => [
                'AUDLEN' => (string)$durationMillis,
            ],
        ];
    }

    /**
     * Build location message payload.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_location
     * @param string $text
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public static function buildLocation($text, $latitude, $longitude)
    {
        return [
            'contentType' => ContentType::LOCATION,
            'text' => $text,
            'location' => [
                'title' => $text,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
        ];
    }

    /**
     * Build sticker message payload.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_sticker
     * @param int $stkid
     * @param int $stkpkgid
     * @param int $stkver
     * @return array
     */
    public static function buildSticker($stkid, $stkpkgid, $stkver = null)
    {
        $meta = [
            'STKID' => (string)$stkid,
            'STKPKGID' => (string)$stkpkgid,
        ];
        if ($stkver !== null) {
            $meta = array_merge($meta, ['STKVER' => (string)$stkver]);
        }

        return [
            'contentType' => ContentType::STICKER,
            'contentMetadata' => $meta,
        ];
    }
}
