<?php

declare(strict_types=1);

namespace Freyr\EventSourcing\Tests;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateId;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\EventSourcing\Tests\Fakes\ExampleAggregate;
use Freyr\EventSourcing\Tests\Fakes\ExampleEvent;
use Freyr\EventSourcing\Tests\Fakes\Unit;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    #[Test]
    public function it_can_be_reconstituted_from_events(): void
    {
        // Arrange
        $aggregateId = AggregateId::new();
        $events = [
            ExampleEvent::occur(
                $aggregateId,
                [
                    'fakeField' => 'test-value',
                    'unit' => Unit::M,
                    'parameters' => [
                        'key' => 'value',
                    ],
                ]
            ),
        ];
        
        // Act
        $aggregate = ExampleAggregate::fromEvents($aggregateId, $events);
        
        // Assert
        self::assertTrue($aggregate->isNotTest());
    }
    
    #[Test]
    public function it_applies_events_when_command_is_executed(): void
    {
        // Arrange
        $aggregateId = AggregateId::new();
        $aggregate = ExampleAggregate::fromEvents($aggregateId, []);
        
        // Act
        $aggregate->addUnit('succeeded');

        $events = $this->getRecordedEvents($aggregate);
        self::assertCount(1, $events);
        self::assertInstanceOf(ExampleEvent::class, $events[0]);
        self::assertSame($aggregateId, $events[0]->aggregateId);
        self::assertSame(Unit::M, $events[0]->unit);
    }
    
    #[Test]
    public function it_does_not_record_events_when_command_conditions_are_not_met(): void
    {
        // Arrange
        $aggregateId = AggregateId::new();
        $aggregate = ExampleAggregate::fromEvents($aggregateId, []);
        
        // Act
        $aggregate->addUnit('failed'); // This won't match the condition in addUnit
        
        // Assert
        $events = $this->getRecordedEvents($aggregate);
        self::assertCount(0, $events);
    }
    
    #[Test]
    public function it_can_pop_recorded_events(): void
    {
        $aggregateId = AggregateId::new();
        $aggregate = ExampleAggregate::fromEvents($aggregateId, []);
        
        // Generate some events
        $aggregate->addUnit('succeeded');
        
        // Act
        $events = $this->getRecordedEvents($aggregate);
        $eventsAfterPop = $this->getRecordedEvents($aggregate);
        
        // Assert
        self::assertCount(1, $events);
        self::assertCount(0, $eventsAfterPop); // Events cleared after pop
        self::assertInstanceOf(ExampleEvent::class, $events[0]);
    }
    
    /**
     * Helper method to get recorded events using reflection
     * @return array<AggregateChanged>
     */
    private function getRecordedEvents(AggregateRoot $aggregate): array
    {
        /** @phpstan-ignore-next-line method.notFound */
        $eventExtractor = fn () => $this->popRecordedEvents();
        /** @var AggregateChanged[] $events */
        $events = $eventExtractor->call($aggregate);

        return $events;
    }
}
