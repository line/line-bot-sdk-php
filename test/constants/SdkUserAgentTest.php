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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SdkUserAgentTest extends TestCase
{
    protected function tearDown(): void
    {
        SdkUserAgent::resetForTesting();
    }

    #[DataProvider('versionProvider')]
    public function testCreate(?string $input, string $expected): void
    {
        self::assertSame($expected, SdkUserAgent::create(fn () => $input));
    }

    /**
     * @return iterable<string, array{?string, string}>
     */
    public static function versionProvider(): iterable
    {
        yield 'stable' => ['12.5.0', 'LINE-BotSDK-PHP/12.5.0'];
        yield 'v-prefix' => ['v12.5.0', 'LINE-BotSDK-PHP/12.5.0'];
        yield 'pre-release' => ['12.5.0-beta.1', 'LINE-BotSDK-PHP/12.5.0-beta.1'];
        yield 'build-metadata' => ['12.5.0+build.7', 'LINE-BotSDK-PHP/12.5.0+build.7'];
        yield 'dev-branch' => ['dev-main', 'LINE-BotSDK-PHP/dev-main'];
        yield 'dev-branch-slash' => ['dev-feature/user-agent', 'LINE-BotSDK-PHP/dev-feature-user-agent'];
        yield 'null' => [null, 'LINE-BotSDK-PHP'];
        yield 'empty' => ['', 'LINE-BotSDK-PHP'];
        yield 'whitespace' => ['   ', 'LINE-BotSDK-PHP'];
    }

    public function testOutOfBoundsExceptionFallback(): void
    {
        $result = SdkUserAgent::create(function (): never {
            throw new \OutOfBoundsException('Package not found');
        });
        self::assertSame('LINE-BotSDK-PHP', $result);
    }

    public function testCustomResolverBypassesCache(): void
    {
        SdkUserAgent::create(fn () => '1.0.0');

        self::assertSame(
            'LINE-BotSDK-PHP/99.0.0',
            SdkUserAgent::create(fn () => '99.0.0'),
        );
    }
}
