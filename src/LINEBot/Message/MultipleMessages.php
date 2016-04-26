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
namespace LINE\LINEBot\Message;

use LINE\LINEBot\Message\Builder\MessageBuilder;

class MultipleMessages
{
    /** @var array */
    private $messages = [];

    /**
     * Add text message.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_text
     * @param string $text
     * @return MultipleMessages $this
     */
    public function addText($text)
    {
        $this->messages[] = MessageBuilder::buildText($text);
        return $this;
    }

    /**
     * Add image message.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_image
     * @param string $imageURL
     * @param string $previewURL
     * @return MultipleMessages $this
     */
    public function addImage($imageURL, $previewURL)
    {
        $this->messages[] = MessageBuilder::buildImage($imageURL, $previewURL);
        return $this;
    }

    /**
     * Add video message.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_video
     * @param string $videoURL
     * @param string $previewImageURL
     * @return MultipleMessages $this
     */
    public function addVideo($videoURL, $previewImageURL)
    {
        $this->messages[] = MessageBuilder::buildVideo($videoURL, $previewImageURL);
        return $this;
    }

    /**
     * Add voice message.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_audio
     * @param string $audioURL
     * @param int $duration
     * @return MultipleMessages $this
     */
    public function addAudio($audioURL, $duration)
    {
        $this->messages[] = MessageBuilder::buildAudio($audioURL, $duration);
        return $this;
    }

    /**
     * Add location message.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_location
     * @param string $text
     * @param float $latitude
     * @param float $longitude
     * @return MultipleMessages $this
     */
    public function addLocation($text, $latitude, $longitude)
    {
        $this->messages[] = MessageBuilder::buildLocation($text, $latitude, $longitude);
        return $this;
    }

    /**
     * Add sticker message.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_message_sticker
     * @param int $stkid
     * @param int $stkpkgid
     * @param int $stkver
     * @return MultipleMessages $this
     */
    public function addSticker($stkid, $stkpkgid, $stkver = null)
    {
        $this->messages[] = MessageBuilder::buildSticker($stkid, $stkpkgid, $stkver);
        return $this;
    }

    /**
     * Get registered messages.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
