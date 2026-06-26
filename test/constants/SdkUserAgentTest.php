<?php

/**
 * Copyright 2026 LINE Corporation
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

namespace LINE\Tests\Constants;

use LINE\Constants\SdkUserAgent;
use PHPUnit\Framework\TestCase;

class SdkUserAgentTest extends TestCase
{
    protected function tearDown(): void
    {
        SdkUserAgent::resetForTesting();
    }

    public function testStableVersion(): void
    {
        self::assertSame('LINE-BotSDK-PHP/12.5.0', SdkUserAgent::create(fn () => '12.5.0'));
    }

    public function testStripsLeadingV(): void
    {
        self::assertSame('LINE-BotSDK-PHP/12.5.0', SdkUserAgent::create(fn () => 'v12.5.0'));
    }

    public function testExceptionFallback(): void
    {
        $result = SdkUserAgent::create(function (): never {
            throw new \RuntimeException('unexpected error');
        });
        self::assertSame('LINE-BotSDK-PHP/unknown', $result);
    }
}
