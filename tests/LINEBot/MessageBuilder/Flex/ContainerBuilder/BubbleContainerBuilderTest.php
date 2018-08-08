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

use LINE\LINEBot\Constant\Flex\ContainerDirection;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use PHPUnit\Framework\TestCase;
use LINE\Tests\LINEBot\Util\MockUtil;

class BubbleContainerBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                ContainerDirection::LTR,
                BoxComponentBuilder::class,
                ImageComponentBuilder::class,
                BoxComponentBuilder::class,
                BoxComponentBuilder::class,
                BubbleStylesBuilder::class
            ],
            'json' => <<<JSON
{
  "type":"bubble",
  "direction":"ltr",
  "header":{"build_result_of":"BoxComponentBuilder:header"},
  "hero":{"build_result_of":"ImageComponentBuilder:hero"},
  "body":{"build_result_of":"BoxComponentBuilder:body"},
  "footer":{"build_result_of":"BoxComponentBuilder:footer"},
  "styles":{"build_result_of":"BubbleStylesBuilder:styles"}
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
            $headerBuilder = isset($t['param'][1]) ? MockUtil::builder($this, $t['param'][1], 'header') : null;
            $heroBuilder = isset($t['param'][2]) ? MockUtil::builder($this, $t['param'][2], 'hero') : null;
            $bodyBuilder = isset($t['param'][3]) ? MockUtil::builder($this, $t['param'][3], 'body') : null;
            $footerBuilder = isset($t['param'][4]) ? MockUtil::builder($this, $t['param'][4], 'footer') : null;
            $stylesBuilder = isset($t['param'][5]) ? MockUtil::builder($this, $t['param'][5], 'styles') : null;

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
