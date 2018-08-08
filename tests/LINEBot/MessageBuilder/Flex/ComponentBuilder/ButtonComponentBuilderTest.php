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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\Tests\LINEBot\Util\MockUtil;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentGravity;

class ButtonComponentBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                MessageTemplateActionBuilder::class,
                2,
                ComponentMargin::LG,
                ComponentButtonHeight::SM,
                ComponentButtonStyle::LINK,
                '#FF0000',
                ComponentGravity::CENTER
            ],
            'json' => <<<JSON
{
  "type":"button",
  "action":{"build_result_of":"MessageTemplateActionBuilder:action"},
  "flex":2,
  "margin":"lg",
  "height":"sm",
  "style":"link",
  "color":"#FF0000",
  "gravity":"center"
}
JSON
        ],
        [
            'param' => [
                MessageTemplateActionBuilder::class
            ],
            'json' => <<<JSON
{
  "type":"button",
  "action":{"build_result_of":"MessageTemplateActionBuilder:action"}
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $actionBuilder = MockUtil::builder($this, $t['param'][0], 'action', 'buildTemplateAction');
            $flex = isset($t['param'][1]) ? $t['param'][1] : null;
            $margin = isset($t['param'][2]) ? $t['param'][2] : null;
            $height = isset($t['param'][3]) ? $t['param'][3] : null;
            $style = isset($t['param'][4]) ? $t['param'][4] : null;
            $color = isset($t['param'][5]) ? $t['param'][5] : null;
            $gravity = isset($t['param'][6]) ? $t['param'][6] : null;

            $componentBuilder = new ButtonComponentBuilder(
                $actionBuilder,
                $flex,
                $margin,
                $height,
                $style,
                $color,
                $gravity
            );
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());

            $componentBuilder = ButtonComponentBuilder::builder()
                ->setAction($actionBuilder)
                ->setFlex($flex)
                ->setMargin($margin)
                ->setHeight($height)
                ->setStyle($style)
                ->setColor($color)
                ->setGravity($gravity);
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());
        }
    }
}
