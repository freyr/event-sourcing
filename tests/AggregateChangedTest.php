<?php

declare(strict_types=1);

namespace Freyr\EventSourcing\Tests;

use Freyr\EventSourcing\AggregateId;
use Freyr\EventSourcing\Occurrence;
use Freyr\EventSourcing\Tests\Fakes\ExampleEvent;
use Freyr\EventSourcing\Tests\Fakes\Unit;
use Freyr\Identity\Id;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AggregateChangedTest extends TestCase
{
    #[Test]
    public function it_can_create_event_through_occur_method(): void
    {
        // Arrange
        $aggregateId = AggregateId::new();
        $payload = [
            'fakeField' => 'test-value',
            'unit' => Unit::KG,
            'parameters' => [
                'key' => 'value',
            ],
        ];

        // Act
        $event = ExampleEvent::occur($aggregateId, $payload);

        // Assert
        self::assertSame($aggregateId, $event->aggregateId);
        self::assertSame('test-value', $event->fakeField);
        self::assertSame(Unit::KG, $event->unit);
        self::assertSame([
            'key' => 'value',
        ], $event->parameters);
    }

    #[Test]
    public function it_can_serialize_to_json(): void
    {
        // Arrange
        $aggregateId = AggregateId::new();
        $eventId = Id::new();
        $occurredOn = Occurrence::now();

        $event = ExampleEvent::fromArray([
            '_id' => (string)$eventId,
            '_aggregate_id' => (string)$aggregateId,
            '_occurred_on' => (string)$occurredOn,
            [
                'fakeField' => 'test-value',
                'unit' => Unit::M->value,
                'parameters' => [
                    'foo' => 'bar',
                ],
            ],
        ]);

        // Act
        $jsonData = $event->jsonSerialize();

        // Assert
        self::assertArrayHasKey('_id', $jsonData);
        self::assertArrayHasKey('_aggregate_id', $jsonData);
        self::assertArrayHasKey('_occurred_on', $jsonData);
        self::assertArrayHasKey('fakeField', $jsonData);
        self::assertArrayHasKey('unit', $jsonData);
        self::assertArrayHasKey('parameters', $jsonData);

        self::assertSame((string)$eventId, $jsonData['_id']);
        self::assertSame((string)$aggregateId, $jsonData['_aggregate_id']);
        self::assertSame('test-value', $jsonData['fakeField']);
        self::assertSame(Unit::M->value, $jsonData['unit']);
        self::assertSame([
            'foo' => 'bar',
        ], $jsonData['parameters']);
    }

    #[Test]
    public function it_can_be_created_from_array(): void
    {
        // Arrange
        $eventId = Id::new();
        $aggregateId = AggregateId::new();
        $payload = [
            '_id' => (string)$eventId,
            '_aggregate_id' => (string)$aggregateId,
            '_occurred_on' => '2025-06-13T20:00:00+00:00',
            'fakeField' => 'reconstituted',
            'unit' => 'm',
            'parameters' => [
                'test' => 'success',
            ],
        ];

        // Act
        $event = ExampleEvent::fromArray($payload);

        // Assert
        self::assertTrue($event->eventId->sameAs($eventId));
        self::assertTrue($event->aggregateId->sameAs($aggregateId));
        self::assertSame('reconstituted', $event->fakeField);
        self::assertSame(Unit::M, $event->unit);
        self::assertSame([
            'test' => 'success',
        ], $event->parameters);
    }

    #[Test]
    public function it_correctly_deserializes_payload(): void
    {
        // Arrange
        $rawPayload = [
            'fakeField' => 'raw-value',
            'unit' => Unit::KG,
            'parameters' => [
                'complex' => 'structure',
            ],
        ];

        $aggregateId = AggregateId::new();
        $event = ExampleEvent::occur($aggregateId, $rawPayload);

        // Assert
        self::assertSame('raw-value', $event->fakeField);
        self::assertSame(Unit::KG, $event->unit);
        self::assertSame([
            'complex' => 'structure',
        ], $event->parameters);
    }
}
