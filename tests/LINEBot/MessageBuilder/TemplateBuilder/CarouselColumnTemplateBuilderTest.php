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
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use PHPUnit\Framework\TestCase;

class CarouselColumnTemplateBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => ['aaa', 'bbb', 'ccc', ['postback', 'message', 'uri']],
            'json' => <<<JSON
{
  "thumbnailImageUrl":"ccc",
  "title":"aaa",
  "text":"bbb",
  "actions":[
    {"type":"postback","label":"AAA","data":"BBB"},
    {"type":"message","label":"CCC","text":"DDD"},
    {"type":"uri","label":"EEE","uri":"FFF"}
  ]
}
JSON

        ],
        [
            'param' => ['aaa', 'bbb', 'ccc', ['message', 'uri'], null],
            'json' => <<<JSON
{
  "thumbnailImageUrl":"ccc",
  "title":"aaa",
  "text":"bbb",
  "actions":[
    {"type":"message","label":"CCC","text":"DDD"},
    {"type":"uri","label":"EEE","uri":"FFF"}
  ]
}
JSON

        ],
        [
            'param' => ['aaa', 'bbb', 'ccc', ['postback'], 'ddd'],
            'json' => <<<JSON
{
  "thumbnailImageUrl":"ccc",
  "title":"aaa",
  "text":"bbb",
  "actions":[
    {"type":"postback","label":"AAA","data":"BBB"}
  ],
  "imageBackgroundColor":"ddd"
}
JSON
        ],
    ];

    public function test()
    {
        $postbackActionBuilder = new PostbackTemplateActionBuilder('AAA', 'BBB');
        $messageTemplateActionBuilder = new MessageTemplateActionBuilder('CCC', 'DDD');
        $uriTemplateActionBuilder = new UriTemplateActionBuilder('EEE', 'FFF');

        foreach (self::$tests as $t) {
            $title = $t['param'][0];
            $text = $t['param'][1];
            $thumbnailImageUrl = $t['param'][2];
            if (is_array($t['param'][3])) {
                $actionBuilders = [];
                if (in_array('postback', $t['param'][3])) {
                    $actionBuilders[] = $postbackActionBuilder;
                }
                if (in_array('message', $t['param'][3])) {
                    $actionBuilders[] = $messageTemplateActionBuilder;
                }
                if (in_array('uri', $t['param'][3])) {
                    $actionBuilders[] = $uriTemplateActionBuilder;
                }
            } else {
                $actionBuilders = null;
            }
            $imageBackgroundColor = isset($t['param'][4]) ? $t['param'][4] : null;

            if (count($t['param']) == 5) {
                $builder = new CarouselColumnTemplateBuilder(
                    $title,
                    $text,
                    $thumbnailImageUrl,
                    $actionBuilders,
                    $imageBackgroundColor
                );
            } else {
                $builder = new CarouselColumnTemplateBuilder(
                    $title,
                    $text,
                    $thumbnailImageUrl,
                    $actionBuilders
                );
            }

            $this->assertEquals($builder->buildTemplate(), json_decode($t['json'], true));
        }
    }
}
