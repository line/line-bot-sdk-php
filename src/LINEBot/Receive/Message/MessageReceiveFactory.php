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
namespace LINE\LINEBot\Receive\Message;

use LINE\LINEBot\Constant\ContentType;
use LINE\LINEBot\Exception\UnsupportedContentTypeException;
use LINE\LINEBot\Receive\Message;

class MessageReceiveFactory
{
    /**
     * Create a message receive that is according to content type.
     *
     * @param array $config Channel (bot) information.
     * @param array $result
     * @return Audio|Contact|Image|Location|Sticker|Text|Video
     * @throws UnsupportedContentTypeException
     */
    public static function create(array $config, array $result)
    {
        $contentType = $result['content']['contentType'];
        switch ($contentType) {
            case ContentType::TEXT:
                return new Text($config, $result);
            case ContentType::IMAGE:
                return new Image($config, $result);
            case ContentType::VIDEO:
                return new Video($config, $result);
            case ContentType::AUDIO:
                return new Audio($config, $result);
            case ContentType::LOCATION:
                return new Location($config, $result);
            case ContentType::STICKER:
                return new Sticker($config, $result);
            case ContentType::CONTACT:
                return new Contact($config, $result);
            default:
                throw new UnsupportedContentTypeException("Unsupported contentType is given: $contentType");
        }
    }
}
