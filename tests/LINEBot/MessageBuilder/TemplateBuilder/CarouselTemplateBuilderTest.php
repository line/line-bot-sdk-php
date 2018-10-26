<?php

/**
 * Copyright 2018 LINE Corporation
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

namespace LINE\Tests\LINEBot\MessageBuilder\TemplateBuilder;

use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use PHPUnit\Framework\TestCase;

class CarouselTemplateBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [['postback', 'message', 'uri']],
            'json' => <<<JSON
{
  "type":"carousel",
  "columns":[
    {"thumbnailImageUrl":"ccc","title":"aaa","text":"bbb","actions":[{"type":"postback","label":"AAA","data":"BBB"}]},
    {"thumbnailImageUrl":"ccc","title":"aaa","text":"bbb","actions":[{"type":"message","label":"CCC","text":"DDD"}]},
    {"thumbnailImageUrl":"ccc","title":"aaa","text":"bbb","actions":[{"type":"uri","label":"EEE","uri":"FFF"}]}
  ]
}
JSON
        ],
        [
            'param' => [['message', 'uri'], null, null],
            'json' => <<<JSON
{
  "type":"carousel",
  "columns":[
    {"thumbnailImageUrl":"ccc","title":"aaa","text":"bbb","actions":[{"type":"message","label":"CCC","text":"DDD"}]},
    {"thumbnailImageUrl":"ccc","title":"aaa","text":"bbb","actions":[{"type":"uri","label":"EEE","uri":"FFF"}]}
  ]
}
JSON
        ],
        [
            'param' => [['postback'], 'ddd'],
            'json' => <<<JSON
{
  "type":"carousel",
  "columns":[
    {"thumbnailImageUrl":"ccc","title":"aaa","text":"bbb","actions":[{"type":"postback","label":"AAA","data":"BBB"}]}
  ],
  "imageAspectRatio":"ddd"
}
JSON
        ],
        [
            'param' => [['message'], 'ddd', 'eee'],
            'json' => <<<JSON
{
  "type":"carousel",
  "columns":[
    {"thumbnailImageUrl":"ccc","title":"aaa","text":"bbb","actions":[{"type":"message","label":"CCC","text":"DDD"}]}
  ],
  "imageAspectRatio":"ddd",
  "imageSize":"eee"
}
JSON
        ],
    ];

    private function carouselTemplateBuilder($actionBuilder)
    {
        return new CarouselColumnTemplateBuilder('aaa', 'bbb', 'ccc', [$actionBuilder]);
    }

    public function test()
    {
        $postbackActionBuilder = $this->carouselTemplateBuilder(new PostbackTemplateActionBuilder('AAA', 'BBB'));
        $messageTemplateActionBuilder = $this->carouselTemplateBuilder(new MessageTemplateActionBuilder('CCC', 'DDD'));
        $uriTemplateActionBuilder = $this->carouselTemplateBuilder(new UriTemplateActionBuilder('EEE', 'FFF'));

        foreach (self::$tests as $t) {
            if (is_array($t['param'][0])) {
                $columnTemplateBuilders= [];
                if (in_array('postback', $t['param'][0])) {
                    $columnTemplateBuilders[] = $postbackActionBuilder;
                }
                if (in_array('message', $t['param'][0])) {
                    $columnTemplateBuilders[] = $messageTemplateActionBuilder;
                }
                if (in_array('uri', $t['param'][0])) {
                    $columnTemplateBuilders[] = $uriTemplateActionBuilder;
                }
            } else {
                $columnTemplateBuilders = null;
            }
            $imageAspectRatio = isset($t['param'][1]) ? $t['param'][1] : null;
            $imageSize = isset($t['param'][2]) ? $t['param'][2] : null;

            if (count($t['param']) == 3) {
                $templateBuilder = new CarouselTemplateBuilder($columnTemplateBuilders, $imageAspectRatio, $imageSize);
            } elseif (count($t['param']) == 2) {
                $templateBuilder = new CarouselTemplateBuilder($columnTemplateBuilders, $imageAspectRatio);
            } else {
                $templateBuilder = new CarouselTemplateBuilder($columnTemplateBuilders);
            }

            $this->assertEquals($templateBuilder->buildTemplate(), json_decode($t['json'], true));
        }
    }
}
