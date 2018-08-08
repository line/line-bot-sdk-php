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

use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use LINE\Tests\LINEBot\Util\MockUtil;
use PHPUnit\Framework\TestCase;

class BubbleStylesBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                BlockStyleBuilder::class,
                BlockStyleBuilder::class,
                BlockStyleBuilder::class,
                BlockStyleBuilder::class
            ],
            'json' => <<<JSON
{
  "header":{"build_result_of":"BlockStyleBuilder:header"},
  "hero":{"build_result_of":"BlockStyleBuilder:hero"},
  "body":{"build_result_of":"BlockStyleBuilder:body"},
  "footer":{"build_result_of":"BlockStyleBuilder:footer"}
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
            $headerBuilder = isset($t['param'][0]) ? MockUtil::builder($this, $t['param'][0], 'header') : null;
            $heroBuilder = isset($t['param'][1]) ? MockUtil::builder($this, $t['param'][1], 'hero') : null;
            $bodyBuilder = isset($t['param'][2]) ? MockUtil::builder($this, $t['param'][2], 'body') : null;
            $footerBuilder = isset($t['param'][3]) ? MockUtil::builder($this, $t['param'][3], 'footer') : null;

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
