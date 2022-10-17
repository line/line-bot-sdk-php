<?php

/**
 * Copyright 2021 LINE Corporation
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

namespace LINE\LINEBot\Event\MessageEvent\ImageMessasge;

use LINE\LINEBot\Event\MessageEvent;

class ImageSet
{
    /** @var string */
    private $id;
    /**
     * imageSet.index
     * It won't be included if the sender is using
     * LINE 11.15 or earlier for Android.
     * @var int|null
     */
    private $index;
    /**
     * imageSet.total
     * It won't be included if the sender is using
     * LINE 11.15 or earlier for Android.
     * @var int|null
     */
    private $total;

    public function __construct($imageSet)
    {
        $this->id = $imageSet['id'];
        $this->index = isset($imageSet['index']) ? $imageSet['index'] : null;
        $this->total = isset($imageSet['total']) ? $imageSet['total'] : null;
    }

    /**
     * Returns Image set ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns indicating the image number in a set of images sent simultaneously.
     * An index starting from 1.
     *
     * @return int|null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Returns total number of images sent simultaneously.
     *
     * @return int|null
     */
    public function getTotal()
    {
        return $this->total;
    }
}
