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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ComponentPosition;

class ButtonComponentBuilderTest extends TestCase
{

    public function test()
    {
        $result = <<<JSON
{
  "type":"button",
  "action":{"type":"uri", "label":"OK", "uri":"http://linecorp.com/"},
  "flex":2,
  "margin":"lg",
  "height":"sm",
  "style":"link",
  "color":"#FF0000",
  "gravity":"center",
  "position": "relative",
  "offsetTop": "4px",
  "offsetBottom": "4%",
  "offsetStart": "none",
  "offsetEnd": "sm"
}
JSON;

        $componentBuilder = new ButtonComponentBuilder(
            new UriTemplateActionBuilder('OK', 'http://linecorp.com/'),
            2,
            ComponentMargin::LG,
            ComponentButtonHeight::SM,
            ComponentButtonStyle::LINK,
            '#FF0000',
            ComponentGravity::CENTER
        );
        $componentBuilder->setPosition(ComponentPosition::RELATIVE)
            ->setOffsetTop('4px')
            ->setOffsetBottom('4%')
            ->setOffsetStart(ComponentSpacing::NONE)
            ->setOffsetEnd(ComponentSpacing::SM);
        $this->assertEquals(json_decode($result, true), $componentBuilder->build());

        $componentBuilder = ButtonComponentBuilder::builder()
            ->setAction(new UriTemplateActionBuilder('OK', 'http://linecorp.com/'))
            ->setFlex(2)
            ->setMargin(ComponentMargin::LG)
            ->setHeight(ComponentButtonHeight::SM)
            ->setStyle(ComponentButtonStyle::LINK)
            ->setColor('#FF0000')
            ->setGravity(ComponentGravity::CENTER)
            ->setPosition(ComponentPosition::RELATIVE)
            ->setOffsetTop('4px')
            ->setOffsetBottom('4%')
            ->setOffsetStart(ComponentSpacing::NONE)
            ->setOffsetEnd(ComponentSpacing::SM);
        $this->assertEquals(json_decode($result, true), $componentBuilder->build());
    }
}
