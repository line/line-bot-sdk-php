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
namespace LINE\Tests\LINEBot\MessageBuilder\Flex\ContainerBuilder;

use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ContainerDirection;
use LINE\LINEBot\Constant\Flex\BubleContainerSize;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use PHPUnit\Framework\TestCase;

class BubbleContainerBuilderTest extends TestCase
{

    public function test()
    {
        $json = <<<JSON
{
  "type":"bubble",
  "size":"giga",
  "direction":"ltr",
  "header":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"header"}
    ]
  },
  "hero":{
    "type":"image",
    "url":"https://example.com/hero.png"
  },
  "body":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"body"}
    ]
  },
  "footer":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"footer"}
    ]
  },
  "styles":{
    "body":{
      "separator": true,
      "separatorColor": "#000000"
    }
  }
}
JSON;

        $builder = new BubbleContainerBuilder(
            ContainerDirection::LTR,
            new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('header')]),
            new ImageComponentBuilder('https://example.com/hero.png'),
            new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('body')]),
            new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('footer')]),
            BubbleStylesBuilder::builder()->setBody(new BlockStyleBuilder(null, true, '#000000')),
            BubleContainerSize::GIGA
        );
        $this->assertEquals(json_decode($json, true), $builder->build());

        $builder = BubbleContainerBuilder::builder()
            ->setDirection(ContainerDirection::LTR)
            ->setSize(BubleContainerSize::GIGA)
            ->setHeader(new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('header')]))
            ->setHero(new ImageComponentBuilder('https://example.com/hero.png'))
            ->setBody(new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('body')]))
            ->setFooter(new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('footer')]))
            ->setStyles(BubbleStylesBuilder::builder()->setBody(new BlockStyleBuilder(null, true, '#000000')));
        $this->assertEquals(json_decode($json, true), $builder->build());
    }

    public function test2()
    {
        $json = <<<JSON
{
  "type":"bubble",
  "size":"giga",
  "direction":"ltr",
  "header":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"header"}
    ]
  },
  "hero":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"header"}
    ]
  },
  "body":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"body"}
    ]
  },
  "footer":{
    "type":"box",
    "layout":"vertical",
    "contents":[
      {"type":"text", "text":"footer"}
    ]
  },
  "styles":{
    "body":{
      "separator": true,
      "separatorColor": "#000000"
    }
  },
  "action":{
    "type":"uri",
    "label":"OK",
    "uri":"http://linecorp.com/"
  }
}
JSON;

        $builder = new BubbleContainerBuilder(
            ContainerDirection::LTR,
            new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('header')]),
            new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('header')]),
            new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('body')]),
            new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('footer')]),
            BubbleStylesBuilder::builder()->setBody(new BlockStyleBuilder(null, true, '#000000')),
            BubleContainerSize::GIGA
        );
        $builder->setAction(new UriTemplateActionBuilder('OK', 'http://linecorp.com/'));
        $this->assertEquals(json_decode($json, true), $builder->build());

        $builder = BubbleContainerBuilder::builder()
            ->setDirection(ContainerDirection::LTR)
            ->setSize(BubleContainerSize::GIGA)
            ->setHeader(new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('header')]))
            ->setHero(new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('header')]))
            ->setBody(new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('body')]))
            ->setFooter(new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('footer')]))
            ->setStyles(BubbleStylesBuilder::builder()->setBody(new BlockStyleBuilder(null, true, '#000000')));
        $builder->setAction(new UriTemplateActionBuilder('OK', 'http://linecorp.com/'));
        $this->assertEquals(json_decode($json, true), $builder->build());
    }
}
