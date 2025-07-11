<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

/**
 * @method popRecordedEvents()
 * @phpstan-type AnyEvent AggregateChanged<array<string, mixed>, array<string, mixed>>
 * @phpstan-import-type EventBaseSerialized from AggregateChanged
 */
abstract readonly class AggregateRepository
{
    public function __construct(
        private AggregateStorage $storage,
        private EventRegistry $eventRegistry,
    ) {}

    public function persist(AggregateRoot $root): void
    {
        $eventExtractor = fn () => $this->popRecordedEvents();
        /** @var list<AnyEvent> $events */
        $events = $eventExtractor->call($root);
        $this->storage->store($root->id, $events);
    }

    /**
     * @return list<AnyEvent>
     */
    protected function loadEventsFor(AggregateId $id): array
    {
        $serializedEvents = $this->storage->load($id);
        $events = [];
        foreach ($serializedEvents as $serializedEvent) {
            /** @var EventBaseSerialized & array<string,mixed> $serializedEvent */
            $eventClass = $this->eventRegistry->getBy($serializedEvent['_name']);
            /** @var AnyEvent $event */
            $event = $eventClass::fromArray($serializedEvent);
            $events[] = $event;
        }

        return $events;
    }
}
