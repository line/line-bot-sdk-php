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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentAlign;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentPosition;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;

class ImageComponentBuilderTest extends TestCase
{

    public function test()
    {
        $json = <<<JSON
{
  "type":"image",
  "url":"http://example.com",
  "flex":2,
  "margin":"xxl",
  "align":"start",
  "gravity":"bottom",
  "size":"full",
  "aspectRatio":"16:9",
  "aspectMode":"cover",
  "backgroundColor":"#000000",
  "action":{"type":"uri", "label":"OK", "uri":"http://linecorp.com/"},
  "position": "relative",
  "offsetTop": "4px",
  "offsetBottom": "4%",
  "offsetStart": "none",
  "offsetEnd": "sm"
}
JSON;

        $componentBuilder = new ImageComponentBuilder(
            'http://example.com',
            2,
            ComponentMargin::XXL,
            ComponentAlign::START,
            ComponentGravity::BOTTOM,
            ComponentImageSize::FULL,
            ComponentImageAspectRatio::R16TO9,
            ComponentImageAspectMode::COVER,
            '#000000',
            new UriTemplateActionBuilder('OK', 'http://linecorp.com/')
        );
        $componentBuilder->setPosition(ComponentPosition::RELATIVE)
            ->setOffsetTop('4px')
            ->setOffsetBottom('4%')
            ->setOffsetStart(ComponentSpacing::NONE)
            ->setOffsetEnd(ComponentSpacing::SM);
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());

        $componentBuilder = ImageComponentBuilder::builder()
            ->setUrl('http://example.com')
            ->setFlex(2)
            ->setMargin(ComponentMargin::XXL)
            ->setAlign(ComponentAlign::START)
            ->setGravity(ComponentGravity::BOTTOM)
            ->setSize(ComponentImageSize::FULL)
            ->setAspectRatio(ComponentImageAspectRatio::R16TO9)
            ->setAspectMode(ComponentImageAspectMode::COVER)
            ->setBackgroundColor('#000000')
            ->setAction(new UriTemplateActionBuilder('OK', 'http://linecorp.com/'))
            ->setPosition(ComponentPosition::RELATIVE)
            ->setOffsetTop('4px')
            ->setOffsetBottom('4%')
            ->setOffsetStart(ComponentSpacing::NONE)
            ->setOffsetEnd(ComponentSpacing::SM);
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());
    }
}
