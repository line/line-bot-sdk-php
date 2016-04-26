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
namespace LINE\LINEBot\Receive;

use LINE\LINEBot\Constant\EventType;
use LINE\LINEBot\Exception\JSONDecodingException;
use LINE\LINEBot\Exception\UnsupportedContentTypeException;
use LINE\LINEBot\Exception\UnsupportedEventTypeException;
use LINE\LINEBot\Receive;
use LINE\LINEBot\Receive\Message\Audio;
use LINE\LINEBot\Receive\Message\Contact;
use LINE\LINEBot\Receive\Message\Image;
use LINE\LINEBot\Receive\Message\Location;
use LINE\LINEBot\Receive\Message\MessageReceiveFactory;
use LINE\LINEBot\Receive\Message\Sticker;
use LINE\LINEBot\Receive\Message\Text;
use LINE\LINEBot\Receive\Message\Video;
use LINE\LINEBot\Receive\Operation\AddContact;
use LINE\LINEBot\Receive\Operation\BlockContact;
use LINE\LINEBot\Receive\Operation\OperationReceiveFactory;

class ReceiveFactory
{
    /**
     * Create a receive payload that is according to event type.
     *
     * @param array $config Channel (bot) information.
     * @param array $result
     * @return Audio|Contact|Image|Location|Sticker|Text|Video|AddContact|BlockContact
     * @throws UnsupportedEventTypeException
     * @throws UnsupportedContentTypeException
     */
    public static function create(array $config, array $result)
    {
        $eventType = $result['eventType'];
        switch ($eventType) {
            case EventType::RECEIVING_MESSAGE:
                return MessageReceiveFactory::create($config, $result);
            case EventType::RECEIVING_OPERATION:
                return OperationReceiveFactory::create($config, $result);
            default:
                throw new UnsupportedEventTypeException("Undefined eventType: $eventType");
        }
    }

    /**
     * Create receives payload from JSON string.
     *
     * @param array $config Channel (bot) information.
     * @param string $json
     * @return Receive[]
     * @throws JSONDecodingException
     * @throws UnsupportedEventTypeException
     */
    public static function createFromJSON(array $config, $json)
    {
        $data = json_decode($json, true);
        if ($data === null) {
            throw new JSONDecodingException("Invalid JSON has given");
        }

        $results = [];
        foreach ($data['result'] as $result) {
            $results[] = ReceiveFactory::create($config, $result);
        }

        return $results;
    }
}
