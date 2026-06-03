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

namespace LINE\Tests\Clients\MessagingApi;

use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Clients\MessagingApi\ObjectSerializer;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the auto-generated ObjectSerializer.
 *
 * ObjectSerializer is the shared serialization helper that EVERY generated
 * client copy relies on (request bodies, query strings, response parsing). It is
 * fully owned by the OpenAPI generator and is rewritten on every bump, so these
 * tests pin its observable behavior as a safety net for generator updates.
 * The MessagingApi copy is used as the representative; all client copies share
 * the same template.
 */
class ObjectSerializerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset shared static state so tests are independent.
        ObjectSerializer::setDateTimeFormat(\DateTime::ATOM);
    }

    protected function tearDown(): void
    {
        ObjectSerializer::setDateTimeFormat(\DateTime::ATOM);
        parent::tearDown();
    }

    public function testSanitizeForSerializationPassesScalarsAndNull(): void
    {
        $this->assertSame('text', ObjectSerializer::sanitizeForSerialization('text'));
        $this->assertSame(42, ObjectSerializer::sanitizeForSerialization(42));
        $this->assertTrue(ObjectSerializer::sanitizeForSerialization(true));
        $this->assertNull(ObjectSerializer::sanitizeForSerialization(null));
    }

    public function testSanitizeForSerializationFormatsDateTime(): void
    {
        $dateTime = new \DateTime('2026-06-01T10:30:00+00:00');
        $this->assertSame(
            '2026-06-01',
            ObjectSerializer::sanitizeForSerialization($dateTime, '\DateTime', 'date')
        );
        $this->assertSame(
            '2026-06-01T10:30:00+00:00',
            ObjectSerializer::sanitizeForSerialization($dateTime, '\DateTime', 'date-time')
        );
    }

    public function testSanitizeForSerializationRecursesIntoArrays(): void
    {
        $dateTime = new \DateTime('2026-06-01T10:30:00+00:00');
        $result = ObjectSerializer::sanitizeForSerialization(['a' => 1, 'when' => $dateTime]);
        $this->assertSame(1, $result['a']);
        $this->assertSame('2026-06-01T10:30:00+00:00', $result['when']);
    }

    public function testSanitizeForSerializationUsesModelAttributeMapKeys(): void
    {
        $message = new TextMessage(['text' => 'hello']);
        $serialized = ObjectSerializer::sanitizeForSerialization($message);
        // The discriminator and attributeMap keys must be present, unset fields omitted.
        $this->assertSame('text', $serialized->type);
        $this->assertSame('hello', $serialized->text);
        $this->assertObjectNotHasProperty('quoteToken', $serialized);
        $this->assertObjectNotHasProperty('emojis', $serialized);
    }

    public function testSanitizeFilename(): void
    {
        $this->assertSame('sun.gif', ObjectSerializer::sanitizeFilename('../../sun.gif'));
        $this->assertSame('sun.gif', ObjectSerializer::sanitizeFilename('/var/tmp/sun.gif'));
        $this->assertSame('sun.gif', ObjectSerializer::sanitizeFilename('C:\\images\\sun.gif'));
        $this->assertSame('plain.txt', ObjectSerializer::sanitizeFilename('plain.txt'));
    }

    public function testToPathValueUrlEncodes(): void
    {
        $this->assertSame('a%2Fb%20c', ObjectSerializer::toPathValue('a/b c'));
        $this->assertSame('1234567890-AbcdEfgh', ObjectSerializer::toPathValue('1234567890-AbcdEfgh'));
    }

    public function testToStringHandlesTypes(): void
    {
        $this->assertSame('true', ObjectSerializer::toString(true));
        $this->assertSame('false', ObjectSerializer::toString(false));
        $this->assertSame('5', ObjectSerializer::toString(5));
        $this->assertSame(
            '2026-06-01T10:30:00+00:00',
            ObjectSerializer::toString(new \DateTime('2026-06-01T10:30:00+00:00'))
        );
    }

    public function testToFormValuePassesThroughString(): void
    {
        $this->assertSame('value', ObjectSerializer::toFormValue('value'));
    }

    public function testToQueryValueRequiredEmptyKeepsEmptyKey(): void
    {
        $this->assertSame(['name' => ''], ObjectSerializer::toQueryValue(null, 'name', 'string', 'form', true, true));
    }

    public function testToQueryValueOptionalEmptyIsOmitted(): void
    {
        $this->assertSame([], ObjectSerializer::toQueryValue(null, 'name', 'string', 'form', true, false));
        $this->assertSame([], ObjectSerializer::toQueryValue('', 'name', 'string', 'form', true, false));
    }

    public function testToQueryValueZeroIntIsNotEmpty(): void
    {
        // 0 is a meaningful value for an int parameter and must be kept.
        $this->assertSame(['count' => 0], ObjectSerializer::toQueryValue(0, 'count', 'integer', 'form', true, false));
    }

    public function testToQueryValueScalar(): void
    {
        $this->assertSame(['date' => '20260601'], ObjectSerializer::toQueryValue('20260601', 'date'));
    }

    public function testToQueryValueArrayExplode(): void
    {
        $this->assertSame(
            ['ids' => ['a', 'b']],
            ObjectSerializer::toQueryValue(['a', 'b'], 'ids', 'array', 'form', true, true)
        );
    }

    public function testConvertBoolToQueryStringFormatUsesIntByDefault(): void
    {
        $this->assertSame(1, ObjectSerializer::convertBoolToQueryStringFormat(true));
        $this->assertSame(0, ObjectSerializer::convertBoolToQueryStringFormat(false));
    }

    public function testBuildQuery(): void
    {
        $this->assertSame('', ObjectSerializer::buildQuery([]));
        $this->assertSame('a=b&c=d', ObjectSerializer::buildQuery(['a' => 'b', 'c' => 'd']));
        // Repeated keys for list values (no [] index appended).
        $this->assertSame('k=1&k=2', ObjectSerializer::buildQuery(['k' => ['1', '2']]));
        // RFC3986 encoding of reserved characters.
        $this->assertSame('a=b%20c', ObjectSerializer::buildQuery(['a' => 'b c']));
        // Booleans use the integer format by default.
        $this->assertSame('flag=1', ObjectSerializer::buildQuery(['flag' => true]));
    }

    public function testSerializeCollection(): void
    {
        $items = ['a', 'b', 'c'];
        $this->assertSame('a,b,c', ObjectSerializer::serializeCollection($items, 'csv'));
        $this->assertSame('a|b|c', ObjectSerializer::serializeCollection($items, 'pipes'));
        $this->assertSame('a b c', ObjectSerializer::serializeCollection($items, 'ssv'));
        $this->assertSame("a\tb\tc", ObjectSerializer::serializeCollection($items, 'tsv'));
        // Unknown style falls back to CSV.
        $this->assertSame('a,b,c', ObjectSerializer::serializeCollection($items, 'unknown'));
    }

    public function testDeserializeNullReturnsNull(): void
    {
        $this->assertNull(ObjectSerializer::deserialize(null, TextMessage::class));
    }

    public function testDeserializePrimitive(): void
    {
        $this->assertSame(5, ObjectSerializer::deserialize('5', 'int'));
        $this->assertSame('hello', ObjectSerializer::deserialize('hello', 'string'));
    }

    public function testDeserializeDateTime(): void
    {
        $result = ObjectSerializer::deserialize('2026-06-01T10:30:00+00:00', '\DateTime');
        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertSame('2026-06-01T10:30:00+00:00', $result->format(\DateTime::ATOM));
    }

    public function testDeserializeEmptyDateTimeReturnsNull(): void
    {
        $this->assertNull(ObjectSerializer::deserialize('', '\DateTime'));
    }

    public function testDeserializeModelUsesAttributeMap(): void
    {
        $message = ObjectSerializer::deserialize('{"type":"text","text":"hello"}', TextMessage::class);
        $this->assertInstanceOf(TextMessage::class, $message);
        $this->assertSame('hello', $message->getText());
    }

    public function testDeserializeArrayOfModels(): void
    {
        $messages = ObjectSerializer::deserialize(
            '[{"type":"text","text":"a"},{"type":"text","text":"b"}]',
            TextMessage::class . '[]'
        );
        $this->assertIsArray($messages);
        $this->assertCount(2, $messages);
        $this->assertSame('a', $messages[0]->getText());
        $this->assertSame('b', $messages[1]->getText());
    }
}
