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

namespace LINE\Tests\Clients;

use LINE\Constants\SdkUserAgent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConfigurationUserAgentTest extends TestCase
{
    /**
     * @param class-string $configurationClass
     */
    #[DataProvider('configurationClassProvider')]
    public function testDefaultUserAgent(string $configurationClass): void
    {
        self::assertSame(
            SdkUserAgent::create(),
            (new $configurationClass())->getUserAgent(),
        );
    }

    /**
     * @return iterable<string, array{class-string}>
     */
    public static function configurationClassProvider(): iterable
    {
        yield 'ChannelAccessToken' => [\LINE\Clients\ChannelAccessToken\Configuration::class];
        yield 'Insight' => [\LINE\Clients\Insight\Configuration::class];
        yield 'Liff' => [\LINE\Clients\Liff\Configuration::class];
        yield 'ManageAudience' => [\LINE\Clients\ManageAudience\Configuration::class];
        yield 'MessagingApi' => [\LINE\Clients\MessagingApi\Configuration::class];
        yield 'Webhook' => [\LINE\Webhook\Configuration::class];
    }

    public function testSetUserAgentOverride(): void
    {
        $config = new \LINE\Clients\MessagingApi\Configuration();
        $config->setUserAgent('custom-user-agent');
        self::assertSame('custom-user-agent', $config->getUserAgent());
    }
}
