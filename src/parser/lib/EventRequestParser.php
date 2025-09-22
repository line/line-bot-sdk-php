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

namespace LINE\Parser;

use LINE\Parser\Exception\InvalidEventRequestException;
use LINE\Parser\Exception\InvalidSignatureException;
use LINE\Webhook\Model\Event;

/**
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
class EventRequestParser
{
    /**
     * Validate signature and parse Webhook event request.
     * When discriminator is not unknown, Webhook event will be parsed to the corresponding superclass.
     * For example, `"type":"unknown"` will be parsed to LINE\Webhook\Model\Event and
     * "type":"message", "message.type":"unknown" will be parsed to LINE\Webhook\Model\MessageContent.
     *
     * @param string $body
     * @param string $channelSecret
     * @param string $signature
     * @return ParsedEvents
     * @throws InvalidEventRequestException
     * @throws InvalidSignatureException
     */
    public static function parseEventRequest(string $body, string $channelSecret, string $signature): ParsedEvents
    {
        if (trim($signature) === '') {
            throw new InvalidSignatureException('Request does not contain signature');
        }

        if (!SignatureValidator::validateSignature($body, $channelSecret, $signature)) {
            throw new InvalidSignatureException('Invalid signature has given');
        }

        $parsedReq = json_decode($body, true);
        if (!isset($parsedReq['events'])) {
            throw new InvalidEventRequestException();
        }

        /* @var Event[] $events */
        $events = [];
        foreach ($parsedReq['events'] as $eventData) {
            $events[] = Event::fromAssocArray($eventData);
        }

        return new ParsedEvents($parsedReq['destination'] ?? null, $events);
    }
}
