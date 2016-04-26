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

use LINE\LINEBot;
use LINE\LINEBot\Constant\ContentType;
use LINE\LINEBot\Message\RichMessage\Markup;

class RichMessageBuilder
{
    /**
     * Build rich message payload.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_rich_content_message_request
     * @param string $imageURL URL of image which is on your server.
     * @param string $altText Alternative string displayed on low-level devices.
     * @param Markup $markup Rich message object.
     * @return array
     */
    public static function buildRichMessage($imageURL, $altText, Markup $markup)
    {
        return [
            'contentType' => ContentType::RICH_MESSAGE,
            'contentMetadata' => [
                'SPEC_REV' => '1',
                'DOWNLOAD_URL' => $imageURL,
                'ALT_TEXT' => $altText,
                'MARKUP_JSON' => $markup->build(),
            ],
        ];
    }
}
