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

namespace LINE\Tests\LINEBot\Util;

class TestUtil
{
    public static function createBuilder($params)
    {
        if (is_array($params)) {
            if (count($params) == 2 && class_exists($params[0])) {
                list($class, $args) = $params;
                foreach ($args as $k => $v) {
                    if (is_array($v)) {
                        $args[$k] = self::createBuilder($v);
                    }
                }
                $reflector = new \ReflectionClass($class);
                return $reflector->newInstanceArgs($args);
            } else {
                foreach ($params as $k => $v) {
                    if (is_array($v)) {
                        $params[$k] = self::createBuilder($v);
                    }
                }
            }
        }
        return $params;
    }
}
