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
namespace LINE\Tests\LINEBot\MessageBuilder\Flex;

use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use PHPUnit\Framework\TestCase;

class BubbleStylesBuilderTest extends TestCase
{

    public function test()
    {
        $json = <<<JSON
{
  "header":{
    "backgroundColor":"#00ffff"
  },
  "hero":{
    "separator": true,
    "separatorColor": "#000000"
  },
  "body":{
    "backgroundColor":"#ffffff"
  },
  "footer":{
    "backgroundColor": "#00ffff",
    "separator": true,
    "separatorColor": "#000000"
  }
}
JSON;

        $builder = new BubbleStylesBuilder(
            new BlockStyleBuilder('#00ffff'),
            new BlockStyleBuilder(null, true, '#000000'),
            new BlockStyleBuilder('#ffffff'),
            new BlockStyleBuilder('#00ffff', true, '#000000')
        );
        $this->assertEquals(json_decode($json, true), $builder->build());

        $builder = BubbleStylesBuilder::builder()
            ->setHeader(new BlockStyleBuilder('#00ffff'))
            ->setHero(new BlockStyleBuilder(null, true, '#000000'))
            ->setBody(new BlockStyleBuilder('#ffffff'))
            ->setFooter(new BlockStyleBuilder('#00ffff', true, '#000000'));
        $this->assertEquals(json_decode($json, true), $builder->build());
    }
}
