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
namespace LINE\Tests\LINEBot\MessageBuilder\Flex;

use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use PHPUnit\Framework\TestCase;

class BlockStyleBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                '#FFFFFF',
                true,
                '#CCCCCC'
            ],
            'json' => <<<JSON
{
  "backgroundColor":"#FFFFFF",
  "separator":true,
  "separatorColor":"#CCCCCC"
}
JSON
        ],
        [
            'param' => [],
            'json' => <<<JSON
{
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $backgroundColor = isset($t['param'][0]) ? $t['param'][0] : null;
            $separator = isset($t['param'][1]) ? $t['param'][1] : null;
            $separatorColor = isset($t['param'][2]) ? $t['param'][2] : null;

            $styleBuilder = new BlockStyleBuilder(
                $backgroundColor,
                $separator,
                $separatorColor
            );
            $this->assertEquals(json_decode($t['json'], true), $styleBuilder->build());

            $styleBuilder = BlockStyleBuilder::builder()
                ->setBackgroundColor($backgroundColor)
                ->setSeparator($separator)
                ->setSeparatorColor($separatorColor);
            $this->assertEquals(json_decode($t['json'], true), $styleBuilder->build());
        }
    }
}
