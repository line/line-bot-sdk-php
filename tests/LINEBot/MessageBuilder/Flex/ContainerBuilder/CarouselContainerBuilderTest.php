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

use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;

class CarouselContainerBuilderTest extends TestCase
{

    public function test()
    {
        $json = <<<JSON
{
  "type":"carousel",
  "contents":[
    {
      "type":"bubble",
      "body":{
        "type":"box",
        "layout":"vertical",
        "contents":[
          {"type":"text", "text":"Hellow,"}
        ]
      }
    },
    {
      "type":"bubble",
      "body":{
        "type":"box",
        "layout":"vertical",
        "contents":[
          {"type":"text", "text":"World!"}
        ]
      }
    }
  ]
}
JSON;

        $builder = new CarouselContainerBuilder([
            BubbleContainerBuilder::builder()->setBody(
                new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('Hellow,')])
            ),
            BubbleContainerBuilder::builder()->setBody(
                new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('World!')])
            )
        ]);
        $this->assertEquals(json_decode($json, true), $builder->build());

        $builder = CarouselContainerBuilder::builder()
            ->setContents([
                BubbleContainerBuilder::builder()->setBody(
                    new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('Hellow,')])
                ),
                BubbleContainerBuilder::builder()->setBody(
                    new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('World!')])
                )
            ]);
        $this->assertEquals(json_decode($json, true), $builder->build());
    }
}
