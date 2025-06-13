<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;

abstract class AggregateRoot
{
    /**
     * @var array<AggregateChanged>
     */
    private array $events = [];

    protected function __construct(protected(set) Id $id)
    {
    }

    /**
     * @param Id $id
     * @param array<AggregateChanged> $streamEvents
     * @return static
     */
    final public static function fromStream(Id $id, array $streamEvents): static
    {
        $instance = new static($id);
        $instance->replay(self::deserializeEventStream($streamEvents));

        return $instance;
    }

    protected function replay(array $historyEvents): void
    {
        /** @var AggregateChanged $pastEvent */
        foreach ($historyEvents as $pastEvent) {
            $this->apply($pastEvent);
        }
    }

    abstract protected function apply(AggregateChanged $event): void;

    /**
     * @return AggregateChanged[]
     */
    private static function deserializeEventStream(array $serializedEvents): array
    {
        $events = [];
        foreach ($serializedEvents as $serializedEvent) {
            $eventName = $serializedEvent['_name'];
            $deserializer = static::eventDeserializer($eventName);
            $events[] = $deserializer($serializedEvent);
        }

        return $events;
    }

    abstract protected static function eventDeserializer(string $eventName): callable;

    protected function popRecordedEvents(): array
    {
        $pendingEvents = $this->events;

        $this->events = [];

        return $pendingEvents;
    }

    protected function recordThat(AggregateChanged $event): void
    {
        $this->events[] = $event;
        $this->apply($event);
    }
}
