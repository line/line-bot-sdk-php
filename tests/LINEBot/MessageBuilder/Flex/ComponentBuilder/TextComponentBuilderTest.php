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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentAlign;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;

class TextComponentBuilderTest extends TestCase
{

    public function test()
    {
        $json = <<<JSON
{
  "type":"text",
  "text":"Hello, World!",
  "flex":2,
  "margin":"lg",
  "size":"5xl",
  "align":"end",
  "gravity":"top",
  "wrap":true,
  "maxLines":0,
  "weight":"bold",
  "color":"#111111",
  "action":{"type":"uri", "label":"OK", "uri":"http://linecorp.com/"}
}
JSON;

        $componentBuilder = new TextComponentBuilder(
            'Hello, World!',
            2,
            ComponentMargin::LG,
            ComponentFontSize::XXXXXL,
            ComponentAlign::END,
            ComponentGravity::TOP,
            true,
            0,
            ComponentFontWeight::BOLD,
            '#111111',
            new UriTemplateActionBuilder('OK', 'http://linecorp.com/')
        );
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());

        $componentBuilder = TextComponentBuilder::builder()
            ->setText('Hello, World!')
            ->setFlex(2)
            ->setMargin(ComponentMargin::LG)
            ->setSize(ComponentFontSize::XXXXXL)
            ->setAlign(ComponentAlign::END)
            ->setGravity(ComponentGravity::TOP)
            ->setWrap(true)
            ->setMaxLines(0)
            ->setWeight(ComponentFontWeight::BOLD)
            ->setColor('#111111')
            ->setAction(new UriTemplateActionBuilder('OK', 'http://linecorp.com/'));
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());
    }
}
