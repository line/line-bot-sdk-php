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

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SeparatorComponentBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentMargin;

class SeparatorComponentBuilderTest extends TestCase
{

    public function test()
    {
        $json = <<<JSON
{
  "type":"separator",
  "margin":"md",
  "color":"#000000"
}
JSON;

        $componentBuilder = new SeparatorComponentBuilder(
            ComponentMargin::MD,
            '#000000'
        );
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());

        $componentBuilder = SeparatorComponentBuilder::builder()
            ->setMargin(ComponentMargin::MD)
            ->setColor('#000000');
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());
    }
}
