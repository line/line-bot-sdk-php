<?php

/**
 * Copyright 2022 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at=>
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace LINE\Tests\LINEBot;

use LINE\LINEBot\Constant\PostbackInputOption;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use PHPUnit\Framework\TestCase;

class PostbackTemplateActionBuilderTest extends TestCase
{
    public function testLabelAndData()
    {
        $poskbackTemplateAction = new PostbackTemplateActionBuilder(
            'postback label',
            'post=back'
        );

        $this->assertEquals(
            [
                "type" => "postback",
                "label" => "postback label",
                "data" => "post=back",
            ],
            $poskbackTemplateAction->buildTemplateAction()
        );
    }

    public function testDisplayText()
    {
        $poskbackTemplateAction = new PostbackTemplateActionBuilder(
            'postback label',
            'post=back',
            'extend text'
        );

        $this->assertEquals(
            [
                "type" => "postback",
                "label" => "postback label",
                "data" => "post=back",
                "displayText" => "extend text",
            ],
            $poskbackTemplateAction->buildTemplateAction()
        );
    }

    public function testInputOption()
    {
        // Case where inputOption parameter is "closeRichMenu"
        $poskbackTemplateAction = new PostbackTemplateActionBuilder(
            'postback label',
            'post=back',
            'extend text',
            PostbackInputOption::CLOSE_RICH_MENU
        );

        $this->assertEquals(
            [
                "type" => "postback",
                "label" => "postback label",
                "data" => "post=back",
                "displayText" => "extend text",
                "inputOption" => "closeRichMenu",
            ],
            $poskbackTemplateAction->buildTemplateAction()
        );

        // Case where inputOption parameter is "openRichMenu"
        $poskbackTemplateAction2 = new PostbackTemplateActionBuilder(
            'postback label2',
            'post=back2',
            'extend text2',
            PostbackInputOption::OPEN_RICH_MENU
        );

        $this->assertEquals(
            [
                "type" => "postback",
                "label" => "postback label2",
                "data" => "post=back2",
                "displayText" => "extend text2",
                "inputOption" => "openRichMenu",
            ],
            $poskbackTemplateAction2->buildTemplateAction()
        );

        // Case where inputOption parameter is "openKeyBoard"
        $poskbackTemplateAction3 = new PostbackTemplateActionBuilder(
            'postback label3',
            'post=back3',
            'extend text3',
            PostbackInputOption::OPEN_KEYBOARD
        );

        $this->assertEquals(
            [
                "type" => "postback",
                "label" => "postback label3",
                "data" => "post=back3",
                "displayText" => "extend text3",
                "inputOption" => "openKeyboard",
            ],
            $poskbackTemplateAction3->buildTemplateAction()
        );

        // Case where inputOption parameter is "openVoice"
        $poskbackTemplateAction4 = new PostbackTemplateActionBuilder(
            'postback label4',
            'post=back4',
            'extend text4',
            PostbackInputOption::OPEN_VOICE
        );

        $this->assertEquals(
            [
                "type" => "postback",
                "label" => "postback label4",
                "data" => "post=back4",
                "displayText" => "extend text4",
                "inputOption" => "openVoice",
            ],
            $poskbackTemplateAction4->buildTemplateAction()
        );
    }


    public function testFillInText()
    {
        $poskbackTemplateAction = new PostbackTemplateActionBuilder(
            'postback label',
            'post=back',
            'extend text',
            PostbackInputOption::OPEN_KEYBOARD,
            'fill in text'
        );

        $this->assertEquals(
            [
                "type" => "postback",
                "label" => "postback label",
                "data" => "post=back",
                "displayText" => "extend text",
                "inputOption" => "openKeyboard",
                "fillInText" => "fill in text",
            ],
            $poskbackTemplateAction->buildTemplateAction()
        );
    }
}
