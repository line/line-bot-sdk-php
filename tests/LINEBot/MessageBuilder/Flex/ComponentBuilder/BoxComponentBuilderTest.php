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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentBorderWidth;
use LINE\LINEBot\Constant\Flex\ComponentPosition;
use LINE\LINEBot\Constant\Flex\ComponentBackgroundType;
use LINE\LINEBot\Constant\Flex\ComponentJustifyContent;
use LINE\LINEBot\Constant\Flex\ComponentAlignItems;

class BoxComponentBuilderTest extends TestCase
{

    public function test()
    {
        $result = json_decode(<<<JSON
{
  "type":"box",
  "layout":"vertical",
  "contents":[
    {"type":"text", "text":"Hello, World!"},
    {"type":"image", "url":"https://example.com/image.png"}
  ],
  "flex":3,
  "spacing":"sm",
  "margin":"xs",
  "action":{"type":"message", "label":"ok", "text":"OK"},
  "paddingAll":"none",
  "paddingTop":"5%",
  "paddingBottom":"5px",
  "paddingStart":"lg",
  "paddingEnd":"xl",
  "backgroundColor":"#000000",
  "borderColor":"#000000",
  "borderWidth":"semi-bold",
  "cornerRadius":"xxl",
  "position": "relative",
  "offsetTop": "4px",
  "offsetBottom": "4%",
  "offsetStart": "none",
  "offsetEnd": "sm",
  "justifyContent": "flex-start",
  "alignItems": "center",
  "background": {
    "type": "linearGradient",
    "centerColor": "#000000"
  },
  "width":"5px",
  "height":"5%"
}
JSON
            , true);

        $componentBuilder = new BoxComponentBuilder(
            ComponentLayout::VERTICAL,
            [
                new TextComponentBuilder('Hello, World!'),
                new ImageComponentBuilder('https://example.com/image.png')
            ],
            3,
            ComponentSpacing::SM,
            ComponentMargin::XS,
            new MessageTemplateActionBuilder('ok', 'OK')
        );
        $componentBuilder->setPaddingAll(ComponentSpacing::NONE)
            ->setPaddingTop('5%')
            ->setPaddingBottom('5px')
            ->setPaddingStart(ComponentSpacing::LG)
            ->setPaddingEnd(ComponentSpacing::XL)
            ->setBackgroundColor('#000000')
            ->setBorderColor('#000000')
            ->setBorderWidth(ComponentBorderWidth::SEMI_BOLD)
            ->setCornerRadius(ComponentSpacing::XXL)
            ->setPosition(ComponentPosition::RELATIVE)
            ->setOffsetTop('4px')
            ->setOffsetBottom('4%')
            ->setOffsetStart(ComponentSpacing::NONE)
            ->setOffsetEnd(ComponentSpacing::SM)
            ->setJustifyContent(ComponentJustifyContent::FLEX_START)
            ->setAlignItems(ComponentAlignItems::CENTER)
            ->setBackgroundType(ComponentBackgroundType::LINEAR_GRADIENT)
            ->setBackgroundCenterColor('#000000')
            ->setWidth('5px')
            ->setHeight('5%');
        $this->assertEquals($result, $componentBuilder->build());

        $componentBuilder = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents([
                new TextComponentBuilder('Hello, World!'),
                new ImageComponentBuilder('https://example.com/image.png')
            ])
            ->setFlex(3)
            ->setSpacing(ComponentSpacing::SM)
            ->setMargin(ComponentMargin::XS)
            ->setAction(new MessageTemplateActionBuilder('ok', 'OK'))
            ->setPaddingAll(ComponentSpacing::NONE)
            ->setPaddingTop('5%')
            ->setPaddingBottom('5px')
            ->setPaddingStart(ComponentSpacing::LG)
            ->setPaddingEnd(ComponentSpacing::XL)
            ->setBackgroundColor('#000000')
            ->setBorderColor('#000000')
            ->setBorderWidth(ComponentBorderWidth::SEMI_BOLD)
            ->setCornerRadius(ComponentSpacing::XXL)
            ->setPosition(ComponentPosition::RELATIVE)
            ->setOffsetTop('4px')
            ->setOffsetBottom('4%')
            ->setOffsetStart(ComponentSpacing::NONE)
            ->setOffsetEnd(ComponentSpacing::SM)
            ->setJustifyContent(ComponentJustifyContent::FLEX_START)
            ->setAlignItems(ComponentAlignItems::CENTER)
            ->setBackgroundType(ComponentBackgroundType::LINEAR_GRADIENT)
            ->setBackgroundCenterColor('#000000')
            ->setWidth('5px')
            ->setHeight('5%');
        $this->assertEquals($result, $componentBuilder->build());
    }
}
