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
     * @template TPD of array
     * @template TPS of array
     * @param List<AggregateChanged<TPD, TPS>> $events
     */
    final public static function fromEvents(AggregateId $id, array $events): static
    {
        /** @phpstan-ignore-next-line new.staticInAbstractClassStaticMethod */
        $instance = new static($id);
        $instance->replay($events);

        return $instance;
    }

    /**
     * @template TPD of array
     * @template TPS of array
     * @param List<AggregateChanged<TPD, TPS>> $events
     */
    protected function replay(array $events): void
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
    }

    /**
     * @template TPD of array
     * @template TPS of array
     * @param AggregateChanged<TPD, TPS> $event
     */
    abstract protected function apply(AggregateChanged $event): void;

    /**
     * @return List<AnyEvent>
     */
    protected function popRecordedEvents(): array
    {
        /** @var List<AnyEvent> $pendingEvents */
        $pendingEvents = $this->events;
        $this->events = [];
        return $pendingEvents;
    }

    /**
     * @template TPD of array
     * @template TPS of array
     * @param AggregateChanged<TPD, TPS> $event
     */
    protected function recordThat(AggregateChanged $event): void
    {
        /** @phpstan-ignore-next-line assign.propertyType */
        $this->events[] = $event;
        $this->apply($event);
    }
}
