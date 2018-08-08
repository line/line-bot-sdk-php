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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\Tests\LINEBot\Util\MockUtil;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentAlign;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;

class ImageComponentBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                'http://example.com',
                2,
                ComponentMargin::XXL,
                ComponentAlign::START,
                ComponentGravity::BOTTOM,
                ComponentImageSize::FULL,
                ComponentImageAspectRatio::R16TO9,
                ComponentImageAspectMode::COVER,
                '#000000',
                MessageTemplateActionBuilder::class
            ],
            'json' => <<<JSON
{
  "type":"image",
  "url":"http://example.com",
  "flex":2,
  "margin":"xxl",
  "align":"start",
  "gravity":"bottom",
  "size":"full",
  "aspectRatio":"16:9",
  "aspectMode":"cover",
  "backgroundColor":"#000000",
  "action":{"build_result_of":"MessageTemplateActionBuilder:action"}
}
JSON
        ],
        [
            'param' => [
                'http://example.com'
            ],
            'json' => <<<JSON
{
  "type":"image",
  "url":"http://example.com"
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $url = $t['param'][0];
            $flex = isset($t['param'][1]) ? $t['param'][1] : null;
            $margin = isset($t['param'][2]) ? $t['param'][2] : null;
            $align = isset($t['param'][3]) ? $t['param'][3] : null;
            $gravity = isset($t['param'][4]) ? $t['param'][4] : null;
            $size = isset($t['param'][5]) ? $t['param'][5] : null;
            $aspectRatio = isset($t['param'][6]) ? $t['param'][6] : null;
            $aspectMode = isset($t['param'][7]) ? $t['param'][7] : null;
            $backgroundColor = isset($t['param'][8]) ? $t['param'][8] : null;
            $actionBuilder = isset($t['param'][9]) ?
                MockUtil::builder($this, $t['param'][9], 'action', 'buildTemplateAction') : null;

            $componentBuilder = new ImageComponentBuilder(
                $url,
                $flex,
                $margin,
                $align,
                $gravity,
                $size,
                $aspectRatio,
                $aspectMode,
                $backgroundColor,
                $actionBuilder
            );
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());

            $componentBuilder = ImageComponentBuilder::builder()
                ->setUrl($url)
                ->setFlex($flex)
                ->setMargin($margin)
                ->setAlign($align)
                ->setGravity($gravity)
                ->setSize($size)
                ->setAspectRatio($aspectRatio)
                ->setAspectMode($aspectMode)
                ->setBackgroundColor($backgroundColor)
                ->setAction($actionBuilder);
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());
        }
    }
}
