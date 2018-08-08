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

use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use PHPUnit\Framework\TestCase;
use LINE\Tests\LINEBot\Util\MockUtil;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;

class CarouselContainerBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                [
                    BubbleContainerBuilder::class,
                    BubbleContainerBuilder::class
                ]
            ],
            'json' => <<<JSON
{
  "type":"carousel",
  "contents":[
    {"build_result_of":"BubbleContainerBuilder:0"},
    {"build_result_of":"BubbleContainerBuilder:1"}
  ]
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $containerBuilders = [];
            foreach ($t['param'][0] as $index => $class) {
                $containerBuilders[] = MockUtil::builder($this, $class, $index);
            }

            $builder = new CarouselContainerBuilder(
                $containerBuilders
            );
            $this->assertEquals(json_decode($t['json'], true), $builder->build());

            $builder = CarouselContainerBuilder::builder()
                ->setContents($containerBuilders);
            $this->assertEquals(json_decode($t['json'], true), $builder->build());
        }
    }
}
