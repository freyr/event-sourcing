<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

abstract class AggregateRoot
{
    /**
     * @var array<AggregateChanged>
     */
    private array $events = [];

    final protected function __construct(public readonly AggregateId $id) {}

    /**
     * @param AggregateChanged[] $events
     */
    final public static function fromEvents(AggregateId $id, array $events): static
    {
        /** @phpstan-ignore-next-line new.staticInAbstractClassStaticMethod */
        $instance = new static($id);
        $instance->replay($events);

        return $instance;
    }

    /**
     * @param array<AggregateChanged> $events
     */
    protected function replay(array $events): void
    {
        foreach ($events as $event) {
            $this->apply($event);
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
