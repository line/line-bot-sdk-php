<?php
/**
 * Copyright 2023 LINE Corporation
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
/**
 * BotInfoResponse
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  LINE\Clients\MessagingApi
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * LINE Messaging API
 *
 * This document describes LINE Messaging API.
 *
 * The version of the OpenAPI document: 0.0.1
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 6.5.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace LINE\Clients\MessagingApi\Model;

use \ArrayAccess;
use \LINE\Clients\MessagingApi\ObjectSerializer;

/**
 * BotInfoResponse Class Doc Comment
 *
 * @category Class
 * @package  LINE\Clients\MessagingApi
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class BotInfoResponse implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'BotInfoResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'userId' => 'string',
        'basicId' => 'string',
        'premiumId' => 'string',
        'displayName' => 'string',
        'pictureUrl' => 'string',
        'chatMode' => 'string',
        'markAsReadMode' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'userId' => null,
        'basicId' => null,
        'premiumId' => null,
        'displayName' => null,
        'pictureUrl' => 'uri',
        'chatMode' => null,
        'markAsReadMode' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'userId' => false,
		'basicId' => false,
		'premiumId' => false,
		'displayName' => false,
		'pictureUrl' => false,
		'chatMode' => false,
		'markAsReadMode' => false
    ];

    /**
      * If a nullable field gets set to null, insert it here
      *
      * @var boolean[]
      */
    protected array $openAPINullablesSetToNull = [];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return boolean[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param boolean[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Checks if a property is nullable
     *
     * @param string $property
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'userId' => 'userId',
        'basicId' => 'basicId',
        'premiumId' => 'premiumId',
        'displayName' => 'displayName',
        'pictureUrl' => 'pictureUrl',
        'chatMode' => 'chatMode',
        'markAsReadMode' => 'markAsReadMode'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'userId' => 'setUserId',
        'basicId' => 'setBasicId',
        'premiumId' => 'setPremiumId',
        'displayName' => 'setDisplayName',
        'pictureUrl' => 'setPictureUrl',
        'chatMode' => 'setChatMode',
        'markAsReadMode' => 'setMarkAsReadMode'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'userId' => 'getUserId',
        'basicId' => 'getBasicId',
        'premiumId' => 'getPremiumId',
        'displayName' => 'getDisplayName',
        'pictureUrl' => 'getPictureUrl',
        'chatMode' => 'getChatMode',
        'markAsReadMode' => 'getMarkAsReadMode'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }

    public const CHAT_MODE_CHAT = 'chat';
    public const CHAT_MODE_BOT = 'bot';
    public const MARK_AS_READ_MODE_AUTO = 'auto';
    public const MARK_AS_READ_MODE_MANUAL = 'manual';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getChatModeAllowableValues()
    {
        return [
            self::CHAT_MODE_CHAT,
            self::CHAT_MODE_BOT,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getMarkAsReadModeAllowableValues()
    {
        return [
            self::MARK_AS_READ_MODE_AUTO,
            self::MARK_AS_READ_MODE_MANUAL,
        ];
    }

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->setIfExists('userId', $data ?? [], null);
        $this->setIfExists('basicId', $data ?? [], null);
        $this->setIfExists('premiumId', $data ?? [], null);
        $this->setIfExists('displayName', $data ?? [], null);
        $this->setIfExists('pictureUrl', $data ?? [], null);
        $this->setIfExists('chatMode', $data ?? [], null);
        $this->setIfExists('markAsReadMode', $data ?? [], null);
    }

    /**
    * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
    * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
    * $this->openAPINullablesSetToNull array
    *
    * @param string $variableName
    * @param array  $fields
    * @param mixed  $defaultValue
    */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['userId'] === null) {
            $invalidProperties[] = "'userId' can't be null";
        }
        if ($this->container['basicId'] === null) {
            $invalidProperties[] = "'basicId' can't be null";
        }
        if ($this->container['displayName'] === null) {
            $invalidProperties[] = "'displayName' can't be null";
        }
        if ($this->container['chatMode'] === null) {
            $invalidProperties[] = "'chatMode' can't be null";
        }
        $allowedValues = $this->getChatModeAllowableValues();
        if (!is_null($this->container['chatMode']) && !in_array($this->container['chatMode'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'chatMode', must be one of '%s'",
                $this->container['chatMode'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['markAsReadMode'] === null) {
            $invalidProperties[] = "'markAsReadMode' can't be null";
        }
        $allowedValues = $this->getMarkAsReadModeAllowableValues();
        if (!is_null($this->container['markAsReadMode']) && !in_array($this->container['markAsReadMode'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'markAsReadMode', must be one of '%s'",
                $this->container['markAsReadMode'],
                implode("', '", $allowedValues)
            );
        }

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets userId
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->container['userId'];
    }

    /**
     * Sets userId
     *
     * @param string $userId Bot's user ID
     *
     * @return self
     */
    public function setUserId($userId)
    {
        if (is_null($userId)) {
            throw new \InvalidArgumentException('non-nullable userId cannot be null');
        }
        $this->container['userId'] = $userId;

        return $this;
    }

    /**
     * Gets basicId
     *
     * @return string
     */
    public function getBasicId()
    {
        return $this->container['basicId'];
    }

    /**
     * Sets basicId
     *
     * @param string $basicId Bot's basic ID
     *
     * @return self
     */
    public function setBasicId($basicId)
    {
        if (is_null($basicId)) {
            throw new \InvalidArgumentException('non-nullable basicId cannot be null');
        }
        $this->container['basicId'] = $basicId;

        return $this;
    }

    /**
     * Gets premiumId
     *
     * @return string|null
     */
    public function getPremiumId()
    {
        return $this->container['premiumId'];
    }

    /**
     * Sets premiumId
     *
     * @param string|null $premiumId Bot's premium ID. Not included in the response if the premium ID isn't set.
     *
     * @return self
     */
    public function setPremiumId($premiumId)
    {
        if (is_null($premiumId)) {
            throw new \InvalidArgumentException('non-nullable premiumId cannot be null');
        }
        $this->container['premiumId'] = $premiumId;

        return $this;
    }

    /**
     * Gets displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->container['displayName'];
    }

    /**
     * Sets displayName
     *
     * @param string $displayName Bot's display name
     *
     * @return self
     */
    public function setDisplayName($displayName)
    {
        if (is_null($displayName)) {
            throw new \InvalidArgumentException('non-nullable displayName cannot be null');
        }
        $this->container['displayName'] = $displayName;

        return $this;
    }

    /**
     * Gets pictureUrl
     *
     * @return string|null
     */
    public function getPictureUrl()
    {
        return $this->container['pictureUrl'];
    }

    /**
     * Sets pictureUrl
     *
     * @param string|null $pictureUrl Profile image URL. `https` image URL. Not included in the response if the bot doesn't have a profile image.
     *
     * @return self
     */
    public function setPictureUrl($pictureUrl)
    {
        if (is_null($pictureUrl)) {
            throw new \InvalidArgumentException('non-nullable pictureUrl cannot be null');
        }
        $this->container['pictureUrl'] = $pictureUrl;

        return $this;
    }

    /**
     * Gets chatMode
     *
     * @return string
     */
    public function getChatMode()
    {
        return $this->container['chatMode'];
    }

    /**
     * Sets chatMode
     *
     * @param string $chatMode Chat settings set in the LINE Official Account Manager (opens new window). One of:  `chat`: Chat is set to \"On\". `bot`: Chat is set to \"Off\".
     *
     * @return self
     */
    public function setChatMode($chatMode)
    {
        if (is_null($chatMode)) {
            throw new \InvalidArgumentException('non-nullable chatMode cannot be null');
        }
        $allowedValues = $this->getChatModeAllowableValues();
        if (!in_array($chatMode, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'chatMode', must be one of '%s'",
                    $chatMode,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['chatMode'] = $chatMode;

        return $this;
    }

    /**
     * Gets markAsReadMode
     *
     * @return string
     */
    public function getMarkAsReadMode()
    {
        return $this->container['markAsReadMode'];
    }

    /**
     * Sets markAsReadMode
     *
     * @param string $markAsReadMode Automatic read setting for messages. If the chat is set to \"Off\", auto is returned. If the chat is set to \"On\", manual is returned.  `auto`: Auto read setting is enabled. `manual`: Auto read setting is disabled.
     *
     * @return self
     */
    public function setMarkAsReadMode($markAsReadMode)
    {
        if (is_null($markAsReadMode)) {
            throw new \InvalidArgumentException('non-nullable markAsReadMode cannot be null');
        }
        $allowedValues = $this->getMarkAsReadModeAllowableValues();
        if (!in_array($markAsReadMode, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'markAsReadMode', must be one of '%s'",
                    $markAsReadMode,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['markAsReadMode'] = $markAsReadMode;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}

