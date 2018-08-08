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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\IconComponentBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentIconSize;
use LINE\LINEBot\Constant\Flex\ComponentIconAspectRatio;

class IconComponentBuilderTest extends TestCase
{

    private static $tests = [
        [
            'param' => [
                'http://example.com',
                ComponentMargin::NONE,
                ComponentIconSize::XXXXL,
                ComponentIconAspectRatio::R2TO1
            ],
            'json' => <<<JSON
{
  "type":"icon",
  "url":"http://example.com",
  "margin":"none",
  "size":"4xl",
  "aspectRatio":"2:1"
}
JSON
        ],
        [
            'param' => [
                'http://example.com'
            ],
            'json' => <<<JSON
{
  "type":"icon",
  "url":"http://example.com"
}
JSON
        ],
    ];

    public function test()
    {
        foreach (self::$tests as $t) {
            $url = $t['param'][0];
            $margin = isset($t['param'][1]) ? $t['param'][1] : null;
            $size = isset($t['param'][2]) ? $t['param'][2] : null;
            $aspectRatio = isset($t['param'][3]) ? $t['param'][3] : null;

            $componentBuilder = new IconComponentBuilder(
                $url,
                $margin,
                $size,
                $aspectRatio
            );
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());

            $componentBuilder = IconComponentBuilder::builder()
                ->setUrl($url)
                ->setMargin($margin)
                ->setSize($size)
                ->setAspectRatio($aspectRatio);
            $this->assertEquals(json_decode($t['json'], true), $componentBuilder->build());
        }
    }
}
