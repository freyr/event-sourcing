<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;


/**
 * @method popRecordedEvents()
 */
abstract readonly class AggregateRepository
{
    public function __construct(
        private AggregateStorage $storage,
        private EventRegistry $eventRegistry,
    ) {}

    public function persist(AggregateRoot $root): void
    {
        $eventExtractor = fn() => $this->popRecordedEvents();
        /** @var AggregateChanged[] $events */
        $events = $eventExtractor->call($root);
        $this->storage->store($root->id, $events);
    }

    /**
     * @return array<mixed>
     */
    protected function loadEventsFor(AggregateId $id): array
    {
        $serializedEvents = $this->storage->load($id);
        $events = [];
        foreach ($serializedEvents as $serializedEvent) {
            $events[] = ($this->eventRegistry->getBy($serializedEvent['_name']))::fromArray($serializedEvent);
        }

        return $events;
    }
}
