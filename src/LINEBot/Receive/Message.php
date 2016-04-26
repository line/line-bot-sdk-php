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

trait Message
{
    use Receive;

    public function isMessage()
    {
        return true;
    }

    public function isSentMe()
    {
        $myMid = $this->getConfig()['channelMid'];
        foreach ($this->getResult()['content']['to'] as $mid) {
            if ($myMid === $mid) {
                return true;
            }
        }
        return false;
    }

    public function getContentId()
    {
        return $this->getResult()['content']['id'];
    }

    public function getCreatedTime()
    {
        return $this->getResult()['content']['createdTime'];
    }

    public function getFromMid()
    {
        return $this->getResult()['content']['from'];
    }

    public function isText()
    {
        return false;
    }

    public function isImage()
    {
        return false;
    }

    public function isVideo()
    {
        return false;
    }

    public function isAudio()
    {
        return false;
    }

    public function isLocation()
    {
        return false;
    }

    public function isSticker()
    {
        return false;
    }

    public function isContact()
    {
        return false;
    }
}
