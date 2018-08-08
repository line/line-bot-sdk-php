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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\Tests\LINEBot\Util\MockUtil;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ComponentMargin;

class BoxComponentBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                ComponentLayout::VERTICAL,
                [
                    TextComponentBuilder::class,
                    ImageComponentBuilder::class
                ],
                3,
                ComponentSpacing::SM,
                ComponentMargin::XS,
                MessageTemplateActionBuilder::class
            ],
            'json' => <<<JSON
{
  "type":"box",
  "layout":"vertical",
  "contents":[
    {"build_result_of":"TextComponentBuilder:0"},
    {"build_result_of":"ImageComponentBuilder:1"}
  ],
  "flex":3,
  "spacing":"sm",
  "margin":"xs",
  "action":{"build_result_of":"MessageTemplateActionBuilder:action"}
}
JSON
        ],
        [
            'param' => [
                ComponentLayout::HORIZONTAL,
                [
                    TextComponentBuilder::class
                ]
            ],
            'json' => <<<JSON
{
  "type":"box",
  "layout":"horizontal",
  "contents":[
    {"build_result_of":"TextComponentBuilder:0"}
  ]
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $layout = $t['param'][0];
            $componentBuilders = [];
            foreach ($t['param'][1] as $index => $class) {
                $componentBuilders[] = MockUtil::builder($this, $class, $index);
            }
            $flex = isset($t['param'][2]) ? $t['param'][2] : null;
            $spacing = isset($t['param'][3]) ? $t['param'][3] : null;
            $margin = isset($t['param'][4]) ? $t['param'][4] : null;
            $actionBuilder = isset($t['param'][5]) ?
                MockUtil::builder($this, $t['param'][5], 'action', 'buildTemplateAction') : null;

            $conponentBuilder = new BoxComponentBuilder(
                $layout,
                $componentBuilders,
                $flex,
                $spacing,
                $margin,
                $actionBuilder
            );
            $this->assertEquals(json_decode($t['json'], true), $conponentBuilder->build());

            $conponentBuilder = BoxComponentBuilder::builder()
                ->setLayout($layout)
                ->setContents($componentBuilders)
                ->setFlex($flex)
                ->setSpacing($spacing)
                ->setMargin($margin)
                ->setAction($actionBuilder);
            $this->assertEquals(json_decode($t['json'], true), $conponentBuilder->build());
        }
    }
}
