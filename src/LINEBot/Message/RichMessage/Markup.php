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
namespace LINE\LINEBot\Message\RichMessage;

use LINE\LINEBot\Exception\IllegalRichMessageHeightException;
use LINE\LINEBot\Exception\JSONEncodingException;

class Markup
{
    /** @var array */
    private $canvas;
    /** @var array */
    private $images;
    /** @var array */
    private $actions;
    /** @var array */
    private $scenes;

    /**
     * Markup constructor.
     *
     * @link https://developers.line.me/bot-api/api-reference#sending_rich_content_message_object
     * @throws IllegalRichMessageHeightException When specified canvas height that is over 2080px.
     * @param int $height A height of the canvas area. Max value is 2080px.
     */
    public function __construct($height)
    {
        if ($height > 2080) {
            throw new IllegalRichMessageHeightException('Rich Message canvas\'s height ' .
                'should be less than or equals 2080px');
        }

        $this->canvas = [
            'height' => $height,       // Integer value. Max value is 2080px.
            'width' => 1040,           // Integer fixed value: 1040.
            'initialScene' => 'scene1' // Fixed string: 'scene1'
        ];

        $this->images = [
            'image1' => [
                'x' => 0,       // Fixed 0.
                'y' => 0,       // Fixed 0.
                'w' => 1040,    // Integer fixed value: 1040.
                'h' => $height, // Integer value. Max value is 2080px.
            ],
        ];

        $this->actions = [];

        $this->scenes = [
            'scene1' => [
                'draws' => [
                    [
                        'image' => 'image1', // Use the image ID "image1".
                        'x' => 0,            // Fixed 0.
                        'y' => 0,            // Fixed 0.
                        'w' => 1040,         // Integer fixed value: 1040.
                        'h' => $height,      // Integer value. Max value is 2080px.
                    ]
                ],
                'listeners' => [],
            ],
        ];
    }

    /**
     * Set an action.
     *
     * @param string $actionName Name of an action.
     * @param string $text Alternative string displayed on low-level devices.
     * @param string $linkURI URL to opened in the web browser.
     * @param string $type Action type.
     * @return Markup $this
     */
    public function setAction($actionName, $text, $linkURI, $type = 'web')
    {
        $obj = [
            'type' => $type,
        ];

        if ($type === 'web') {
            $obj['text'] = $text;
            $obj['params'] = [
                'linkUri' => $linkURI,
            ];
        } elseif ($type === 'sendMessage') {
            $obj['params'] = [
                'text' => $text,
            ];
        }

        $this->actions[$actionName] = $obj;

        return $this;
    }

    /**
     * Add a listener which is associated with an action.
     *
     * @param string $actionName Action name to associate with listener.
     * @param int $x x-coordinate value.
     * @param int $y y-coordinate value.
     * @param int $width Width of the image.
     * @param int $height Height of the image.
     * @return Markup $this
     */
    public function addListener($actionName, $x, $y, $width, $height)
    {
        $this->scenes['scene1']['listeners'][] = [
            'type' => 'touch', # Fixed string: 'touch'
            'params' => [$x, $y, $width, $height],
            'action' => $actionName,
        ];

        return $this;
    }

    /**
     * Generate markup JSON from this instance.
     *
     * @return string Markup JSON.
     * @throws JSONEncodingException
     */
    public function build()
    {
        $json = json_encode([
            'canvas' => $this->canvas,
            'images' => $this->images,
            'actions' => $this->actions,
            'scenes' => $this->scenes,
        ]);

        if ($json === false) {
            throw new JSONEncodingException("Failed to encode markup JSON");
        }
        return $json;
    }
}
