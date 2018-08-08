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

namespace LINE\LINEBot\Util;

class BuildUtil
{
    /**
     * Execute build method and return result.
     *
     * @param object|null $builder
     * @param string|null $buiildMethod
     *
     * @return mixed|null
     */
    public static function build($builder = null, $buiildMethod = 'build')
    {
        return is_null($builder) ? null : $builder->$buiildMethod();
    }

    /**
     * Remove null elements from array.
     *
     * @param array $ary
     *
     * @return array
     */
    public static function removeNullElements($ary)
    {
        return array_filter($ary, function ($v) {
            return !is_null($v);
        });
    }
}
