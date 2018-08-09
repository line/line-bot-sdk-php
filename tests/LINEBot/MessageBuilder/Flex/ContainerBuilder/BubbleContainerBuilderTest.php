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
namespace LINE\Tests\LINEBot\MessageBuilder\Flex\ContainerBuilder;

use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ContainerDirection;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use PHPUnit\Framework\TestCase;
use LINE\Tests\LINEBot\Util\TestUtil;

class BubbleContainerBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                ContainerDirection::LTR,
                [
                    BoxComponentBuilder::class, [
                        ComponentLayout::VERTICAL, [
                            [TextComponentBuilder::class, ['header']]
                        ]
                    ]
                ],
                [ImageComponentBuilder::class, ['https://example.com/hero.png']],
                [
                    BoxComponentBuilder::class, [
                        ComponentLayout::VERTICAL, [
                            [TextComponentBuilder::class, ['body']]
                        ]
                    ]
                ],
                [
                    BoxComponentBuilder::class, [
                        ComponentLayout::VERTICAL, [
                            [TextComponentBuilder::class, ['footer']]
                        ]
                    ]
                ],
                [
                    BubbleStylesBuilder::class, [
                        null,
                        null,
                        [BlockStyleBuilder::class, [null, true, '#000000']],
                    ]
                ]
            ],
            'json' => <<<JSON
{
  "type":"bubble",
  "direction":"ltr",
  "header":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"header"}
    ]
  },
  "hero":{
    "type":"image",
    "url":"https://example.com/hero.png"
  },
  "body":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"body"}
    ]
  },
  "footer":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"footer"}
    ]
  },
  "styles":{
    "body":{
      "separator": true,
      "separatorColor": "#000000"
    }
  }
}
JSON
        ],
        [
            'param' => [],
            'json' => <<<JSON
{
  "type":"bubble"
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $direction = isset($t['param'][0]) ? $t['param'][0] : null;
            $headerBuilder = isset($t['param'][1]) ? TestUtil::createBuilder($t['param'][1]) : null;
            $heroBuilder = isset($t['param'][2]) ? TestUtil::createBuilder($t['param'][2]) : null;
            $bodyBuilder = isset($t['param'][3]) ? TestUtil::createBuilder($t['param'][3]) : null;
            $footerBuilder = isset($t['param'][4]) ? TestUtil::createBuilder($t['param'][4]) : null;
            $stylesBuilder = isset($t['param'][5]) ? TestUtil::createBuilder($t['param'][5]) : null;

            $builder = new BubbleContainerBuilder(
                $direction,
                $headerBuilder,
                $heroBuilder,
                $bodyBuilder,
                $footerBuilder,
                $stylesBuilder
            );
            $this->assertEquals(json_decode($t['json'], true), $builder->build());

            $builder = BubbleContainerBuilder::builder()
                ->setDirection($direction)
                ->setHeader($headerBuilder)
                ->setHero($heroBuilder)
                ->setBody($bodyBuilder)
                ->setFooter($footerBuilder)
                ->setStyles($stylesBuilder);
            $this->assertEquals(json_decode($t['json'], true), $builder->build());
        }
    }
}
