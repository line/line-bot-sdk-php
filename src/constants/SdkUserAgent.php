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

namespace LINE\Constants;

use Composer\InstalledVersions;

/**
 * @internal
 */
class SdkUserAgent
{
    private const PRODUCT = 'LINE-BotSDK-PHP';
    private const FALLBACK = self::PRODUCT . '/unknown';
    private const PACKAGE = 'linecorp/line-bot-sdk';

    private static ?string $cached = null;

    /**
     * @param null|callable(): string $versionResolver
     */
    public static function create(?callable $versionResolver = null): string
    {
        if ($versionResolver !== null) {
            return self::build($versionResolver);
        }

        if (self::$cached === null) {
            self::$cached = self::build(static function (): string {
                return InstalledVersions::getPrettyVersion(self::PACKAGE);
            });
        }

        return self::$cached;
    }

    /**
     * @internal
     */
    public static function resetForTesting(): void
    {
        self::$cached = null;
    }

    /**
     * @param callable(): string $versionResolver
     */
    private static function build(callable $versionResolver): string
    {
        try {
            $version = $versionResolver();
        } catch (\Throwable) {
            return self::FALLBACK;
        }

        $version = preg_replace('/^v(?=\d)/', '', $version) ?? $version;

        return self::PRODUCT . '/' . $version;
    }
}
