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
  "action":{"type":"message", "label":"ok", "text":"OK"}
}
JSON
            , true);

        $conponentBuilder = new BoxComponentBuilder(
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
        $this->assertEquals($result, $conponentBuilder->build());

        $conponentBuilder = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents([
                new TextComponentBuilder('Hello, World!'),
                new ImageComponentBuilder('https://example.com/image.png')
            ])
            ->setFlex(3)
            ->setSpacing(ComponentSpacing::SM)
            ->setMargin(ComponentMargin::XS)
            ->setAction(new MessageTemplateActionBuilder('ok', 'OK'));
        $this->assertEquals($result, $conponentBuilder->build());
    }
}
