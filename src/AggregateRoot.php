<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

/**
 * Generic-agnostic event alias used inside root; we do not care about payload shapes here.
 *
 * @phpstan-type AnyEvent AggregateChanged<array<string, mixed>, array<string, mixed>>
 */
abstract class AggregateRoot
{
    /**
     * @var list<AnyEvent>
     */
    private array $events = [];

    final protected function __construct(public readonly AggregateId $id) {}

    /**
     * @param list<AnyEvent> $events
     */
    final public static function fromEvents(AggregateId $id, array $events): static
    {
        /** @phpstan-ignore-next-line new.staticInAbstractClassStaticMethod */
        $instance = new static($id);
        $instance->replay($events);

        return $instance;
    }

    /**
     * @param list<AnyEvent> $events
     */
    protected function replay(array $events): void
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
    }

    /**
     * @param AnyEvent $event
     */
    abstract protected function apply(AggregateChanged $event): void;

    /**
     * @return list<AnyEvent>
     */
    protected function popRecordedEvents(): array
    {
        $pendingEvents = $this->events;
        $this->events = [];
        return $pendingEvents;
    }

    /**
     * @param AnyEvent $event
     */
    protected function recordThat(AggregateChanged $event): void
    {
        $this->events[] = $event;
        $this->apply($event);
    }
}
