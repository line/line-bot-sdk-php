<?php
/**
 * Copyright 2025 LINE Corporation
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

namespace LINE\Tests\Clients\MessagingApi\Model;

use LINE\Clients\MessagingApi\Model\TextMessage;
use PHPUnit\Framework\TestCase;

class TextMessageTest extends TestCase
{
    public function testDiscriminatorIsAutomaticallySet()
    {
        $message = new TextMessage();
        $message->setText('hello!');

        $this->assertEquals('text', $message->getType());
    }

    public function testDiscriminatorIsSetWithConstructorData()
    {
        $message = new TextMessage(['text' => 'hello!']);

        $this->assertEquals('text', $message->getType());
    }
}
