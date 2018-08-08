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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\Tests\LINEBot\Util\MockUtil;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentAlign;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;

class TextComponentBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                'Hello, World!',
                2,
                ComponentMargin::LG,
                ComponentFontSize::XXXXXL,
                ComponentAlign::END,
                ComponentGravity::TOP,
                true,
                0,
                ComponentFontWeight::BOLD,
                '#111111',
                MessageTemplateActionBuilder::class
            ],
            'json' => <<<JSON
{
  "type":"text",
  "text":"Hello, World!",
  "flex":2,
  "margin":"lg",
  "size":"5xl",
  "align":"end",
  "gravity":"top",
  "wrap":true,
  "maxLines":0,
  "weight":"bold",
  "color":"#111111",
  "action":{"build_result_of":"MessageTemplateActionBuilder:action"}
}
JSON
        ],
        [
            'param' => ['Hello, World!'],
            'json' => <<<JSON
{
  "type":"text",
  "text":"Hello, World!"
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $text = $t['param'][0];
            $flex = isset($t['param'][1]) ? $t['param'][1] : null;
            $margin = isset($t['param'][2]) ? $t['param'][2] : null;
            $size = isset($t['param'][3]) ? $t['param'][3] : null;
            $align = isset($t['param'][4]) ? $t['param'][4] : null;
            $gravity = isset($t['param'][5]) ? $t['param'][5] : null;
            $wrap = isset($t['param'][6]) ? $t['param'][6] : null;
            $maxLines = isset($t['param'][7]) ? $t['param'][7] : null;
            $weight = isset($t['param'][8]) ? $t['param'][8] : null;
            $color = isset($t['param'][9]) ? $t['param'][9] : null;
            $actionBuilder = isset($t['param'][10]) ?
                MockUtil::builder($this, $t['param'][10], 'action', 'buildTemplateAction') : null;

            $componentBuilder = new TextComponentBuilder(
                $text,
                $flex,
                $margin,
                $size,
                $align,
                $gravity,
                $wrap,
                $maxLines,
                $weight,
                $color,
                $actionBuilder
            );
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());

            $componentBuilder = TextComponentBuilder::builder()
                ->setText($text)
                ->setFlex($flex)
                ->setMargin($margin)
                ->setSize($size)
                ->setAlign($align)
                ->setGravity($gravity)
                ->setWrap($wrap)
                ->setMaxLines($maxLines)
                ->setWeight($weight)
                ->setColor($color)
                ->setAction($actionBuilder);
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());
        }
    }
}
