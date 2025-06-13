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

    final protected function __construct(protected(set) Id $id)
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
        $instance->replay($streamEvents);

        return $instance;
    }

    /**
     * @param array<AggregateChanged> $historyEvents
     */
    protected function replay(array $historyEvents): void
    {
        foreach ($historyEvents as $pastEvent) {
            $this->apply($pastEvent);
        }
    }

    abstract protected function apply(AggregateChanged $event): void;

    /**
     * @return array<AggregateChanged>
     */
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
