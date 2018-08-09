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
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use PHPUnit\Framework\TestCase;
use LINE\Tests\LINEBot\Util\TestUtil;

class BubbleStylesBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                [BlockStyleBuilder::class, ['#00ffff']],
                [BlockStyleBuilder::class, [null, true, '#000000']],
                [BlockStyleBuilder::class, ['#ffffff']],
                [BlockStyleBuilder::class, ['#00ffff', true, '#000000']]
            ],
            'json' => <<<JSON
{
  "header":{
    "backgroundColor":"#00ffff"
  },
  "hero":{
    "separator": true,
    "separatorColor": "#000000"
  },
  "body":{
    "backgroundColor":"#ffffff"
  },
  "footer":{
    "backgroundColor": "#00ffff",
    "separator": true,
    "separatorColor": "#000000"
  }
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
            $headerBuilder = isset($t['param'][0]) ? TestUtil::createBuilder($t['param'][0]) : null;
            $heroBuilder = isset($t['param'][1]) ? TestUtil::createBuilder($t['param'][1]) : null;
            $bodyBuilder = isset($t['param'][2]) ? TestUtil::createBuilder($t['param'][2]) : null;
            $footerBuilder = isset($t['param'][3]) ? TestUtil::createBuilder($t['param'][3]) : null;

            $builder = new BubbleStylesBuilder(
                $headerBuilder,
                $heroBuilder,
                $bodyBuilder,
                $footerBuilder
            );
            $this->assertEquals(json_decode($t['json'], true), $builder->build());

            $builder = BubbleStylesBuilder::builder()
                ->setHeader($headerBuilder)
                ->setHero($heroBuilder)
                ->setBody($bodyBuilder)
                ->setFooter($footerBuilder);
            $this->assertEquals(json_decode($t['json'], true), $builder->build());
        }
    }
}
