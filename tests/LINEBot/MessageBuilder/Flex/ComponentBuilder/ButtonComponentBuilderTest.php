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
namespace LINE\Tests\LINEBot\MessageBuilder\Flex\ComponentBuilder;

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\Tests\LINEBot\Util\TestUtil;

class ButtonComponentBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                [UriTemplateActionBuilder::class, ['OK', 'http://linecorp.com/']],
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
  "action":{"type":"uri", "label":"OK", "uri":"http://linecorp.com/"},
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
                [UriTemplateActionBuilder::class, ['OK', 'http://linecorp.com/']],
            ],
            'json' => <<<JSON
{
  "type":"button",
  "action":{"type":"uri", "label":"OK", "uri":"http://linecorp.com/"}
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $actionBuilder = TestUtil::createBuilder($t['param'][0]);
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
