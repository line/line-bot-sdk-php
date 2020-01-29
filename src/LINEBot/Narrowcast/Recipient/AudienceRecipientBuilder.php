<?php
/**
 * Copyright 2020 LINE Corporation
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

namespace LINE\LINEBot\Narrowcast\Recipient;

/**
 * A builder class for audience recipient
 *
 * @package LINE\LINEBot\Narrowcast\Recipient
 */
class AudienceRecipientBuilder extends RecipientBuilder
{
    const TYPE = 'audience';

    /** @var int $audienceGroupId */
    private $audienceGroupId;

    /**
     * Set audienceGroupId
     *
     * @param int $audienceGroupId
     * @return $this
     */
    public function setAudienceGroupId($audienceGroupId)
    {
        $this->audienceGroupId = $audienceGroupId;
        return $this;
    }

    /**
     * Builds recipient
     *
     * @return array
     */
    public function build()
    {
        return [
            'type' => self::TYPE,
            'audienceGroupId' => $this->audienceGroupId
        ];
    }
}
