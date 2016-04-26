<?php
/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
namespace LINE\LINEBot\Receive\Operation;

use LINE\LINEBot\Constant\OpType;
use LINE\LINEBot\Exception\UnsupportedContentTypeException;

class OperationReceiveFactory
{
    /**
     * Create a operation receive that is according to operation type.
     *
     * @param array $config Channel (bot) information.
     * @param array $result
     * @return AddContact|BlockContact
     * @throws UnsupportedContentTypeException
     */
    public static function create(array $config, array $result)
    {
        $opType = $result['content']['opType'];
        switch ($opType) {
            case OpType::ADDED_AS_FRIEND:
                return new AddContact($config, $result);
            case OpType::BLOCKED:
                return new BlockContact($config, $result);
            default:
                throw new UnsupportedContentTypeException("Unsupported opType is given: $opType");
        }
    }
}
