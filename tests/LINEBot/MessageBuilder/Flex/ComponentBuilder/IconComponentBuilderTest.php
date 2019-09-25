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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\IconComponentBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentIconSize;
use LINE\LINEBot\Constant\Flex\ComponentIconAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentPosition;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;

class IconComponentBuilderTest extends TestCase
{

    public function test()
    {
        $json = <<<JSON
{
  "type":"icon",
  "url":"http://example.com",
  "margin":"none",
  "size":"4xl",
  "aspectRatio":"2:1",
  "position": "relative",
  "offsetTop": "4px",
  "offsetBottom": "4%",
  "offsetStart": "none",
  "offsetEnd": "sm"
}
JSON;

        $componentBuilder = new IconComponentBuilder(
            'http://example.com',
            ComponentMargin::NONE,
            ComponentIconSize::XXXXL,
            ComponentIconAspectRatio::R2TO1
        );
        $componentBuilder->setPosition(ComponentPosition::RELATIVE)
            ->setOffsetTop('4px')
            ->setOffsetBottom('4%')
            ->setOffsetStart(ComponentSpacing::NONE)
            ->setOffsetEnd(ComponentSpacing::SM);
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());

        $componentBuilder = IconComponentBuilder::builder()
            ->setUrl('http://example.com')
            ->setMargin(ComponentMargin::NONE)
            ->setSize(ComponentIconSize::XXXXL)
            ->setAspectRatio(ComponentIconAspectRatio::R2TO1)
            ->setPosition(ComponentPosition::RELATIVE)
            ->setOffsetTop('4px')
            ->setOffsetBottom('4%')
            ->setOffsetStart(ComponentSpacing::NONE)
            ->setOffsetEnd(ComponentSpacing::SM);
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());
    }
}
