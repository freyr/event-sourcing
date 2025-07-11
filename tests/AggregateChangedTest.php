<?php

declare(strict_types=1);

namespace Freyr\EventSourcing\Tests;

use Freyr\EventSourcing\AggregateId;
use Freyr\EventSourcing\Tests\Fakes\ExampleEvent;
use Freyr\EventSourcing\Tests\Fakes\Unit;
use PHPUnit\Framework\TestCase;

final class AggregateChangedTest extends TestCase
{
    public function testEventNameAttributeIsUsedInOccur(): void
    {
        $aggregateId = $this->createMock(AggregateId::class);
        $payload = [
            'fakeField' => 'test',
            'unit' => Unit::M,
            'parameters' => [
                'param1' => 'value1',
            ],
        ];

        $event = ExampleEvent::occur($aggregateId, $payload);

        // Verify that the eventName property is set to the value from the EventName attribute
        self::assertSame('example.event', $event->eventName);
    }

    public function testEventNameAttributeIsUsedInFromArray(): void
    {
        $payload = [
            '_id' => '123e4567-e89b-12d3-a456-426614174000',
            '_aggregate_id' => '123e4567-e89b-12d3-a456-426614174001',
            '_occurred_on' => '2023-01-01T00:00:00+00:00',
            '_name' => 'example.event',
            'fakeField' => 'test',
            'unit' => 'm',
            'parameters' => [
                'param1' => 'value1',
            ],
        ];

        $event = ExampleEvent::fromArray($payload);

        // Verify that the eventName property is set to the value from the EventName attribute
        self::assertSame('example.event', $event->eventName);
    }
}
