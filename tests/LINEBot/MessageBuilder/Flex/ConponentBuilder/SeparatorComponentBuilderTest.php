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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SeparatorComponentBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentMargin;

class SeparatorComponentBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [ComponentMargin::MD, '#000000'],
            'json' => <<<JSON
{
  "type":"separator",
  "margin":"md",
  "color":"#000000"
}
JSON
        ],
        [
            'param' => [],
            'json' => <<<JSON
{
  "type":"separator"
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $margin = isset($t['param'][0]) ? $t['param'][0] : null;
            $color = isset($t['param'][1]) ? $t['param'][1] : null;

            $componentBuilder = new SeparatorComponentBuilder(
                $margin,
                $color
            );
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());

            $componentBuilder = SeparatorComponentBuilder::builder()
                ->setMargin($margin)
                ->setColor($color);
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());
        }
    }
}
