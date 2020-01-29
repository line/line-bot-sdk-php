<?php
/**
 * Copyright 2020 LINE Corporation
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

namespace LINE\LINEBot\Narrowcast\Recipient;

/**
 * A builder class for operator recipient
 *
 * @package LINE\LINEBot\Narrowcast\Recipient
 */
class OperatorRecipientBuilder extends RecipientBuilder
{
    const TYPE = 'operator';

    /** @var string $operator */
    private $operator;

    /** @var RecipientBuilder[] $children */
    private $children = [];

    /**
     * Set filters with 'and' operation
     *
     * @param RecipientBuilder[] $recipientBuilders
     * @return $this
     */
    public function setAnd($recipientBuilders)
    {
        $this->operator = 'and';
        $this->children[$this->operator] = $recipientBuilders;
        return $this;
    }

    /**
     * Set filters with 'or' operation
     *
     * @param RecipientBuilder[] $recipientBuilders
     * @return $this
     */
    public function setOr($recipientBuilders)
    {
        $this->operator = 'or';
        $this->children[$this->operator] = $recipientBuilders;
        return $this;
    }

    /**
     * Set filters with 'not' operation
     *
     * @param RecipientBuilder $recipientBuilder
     * @return $this
     */
    public function setNot(RecipientBuilder $recipientBuilder)
    {
        $this->operator = 'not';
        $this->children[$this->operator] = $recipientBuilder;
        return $this;
    }

    /**
     * Builds recipient
     *
     * @return array
     */
    public function build()
    {
        $children = $this->children[$this->operator];
        if (is_array($children)) {
            $children = array_map(function ($child) {
                return $child->build();
            }, $children);
        } else {
            $children = $children->build();
        }
        return [
            'type' => self::TYPE,
            $this->operator => $children,
        ];
    }
}
