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

namespace LINE\LINEBot\Event;

use LINE\LINEBot\Constant\EventSourceType;
use LINE\LINEBot\Exception\InvalidEventSourceException;

/**
 * Base class of each events.
 *
 * Don't instantiate this class individually.
 *
 * @package LINE\LINEBot\Event
 */
class BaseEvent
{
    /** @var array */
    protected $event;

    /**
     * BaseEvent constructor.
     *
     * @param array $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Returns event type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->event['type'];
    }

    /**
     * Returns mode.
     *
     * active: The channel is active.
     * standby: The channel is waiting.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->event['mode'];
    }

    /**
     * Returns timestamp of the event.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->event['timestamp'];
    }

    /**
     * Returns reply token of the event.
     *
     * @return string|null
     */
    public function getReplyToken()
    {
        return isset($this->event['replyToken']) ? $this->event['replyToken'] : null;
    }

    /**
     * Returns the event is user's one or not.
     *
     * @return bool
     */
    public function isUserEvent()
    {
        return $this->event['source']['type'] === EventSourceType::USER;
    }

    /**
     * Returns the event is group's one or not.
     *
     * @return bool
     */
    public function isGroupEvent()
    {
        return $this->event['source']['type'] === EventSourceType::GROUP;
    }

    /**
     * Returns the event is room's one or not.
     *
     * @return bool
     */
    public function isRoomEvent()
    {
        return $this->event['source']['type'] === EventSourceType::ROOM;
    }

    /**
     * Returns the event is unknown or not.
     *
     * @return bool
     */
    public function isUnknownEvent()
    {
        return !($this->isUserEvent() || $this->isGroupEvent() || $this->isRoomEvent());
    }

    /**
     * Returns user ID of the event.
     *
     * @return string|null
     */
    public function getUserId()
    {
        return isset($this->event['source']['userId'])
            ? $this->event['source']['userId']
            : null;
    }

    /**
     * Returns group ID of the event.
     *
     * @return string|null
     * @throws InvalidEventSourceException Raise when called with non group type event.
     */
    public function getGroupId()
    {
        if (!$this->isGroupEvent()) {
            throw new InvalidEventSourceException('This event source is not a group type');
        }
        return isset($this->event['source']['groupId'])
            ? $this->event['source']['groupId']
            : null;
    }

    /**
     * Returns room ID of the event.
     *
     * @return string|null
     * @throws InvalidEventSourceException Raise when called with non room type event.
     */
    public function getRoomId()
    {
        if (!$this->isRoomEvent()) {
            throw new InvalidEventSourceException('This event source is not a room type');
        }
        return isset($this->event['source']['roomId'])
            ? $this->event['source']['roomId']
            : null;
    }

    /**
     * Returns the identifier of the event source that associated with event source type
     * (i.e. userId, groupId or roomId).
     *
     * @return null|string
     * @throws InvalidEventSourceException
     */
    public function getEventSourceId()
    {
        if ($this->isUserEvent()) {
            return $this->getUserId();
        }

        if ($this->isGroupEvent()) {
            return $this->getGroupId();
        }

        if ($this->isRoomEvent()) {
            return $this->getRoomId();
        }

        # Unknown event
        return null;
    }
}
