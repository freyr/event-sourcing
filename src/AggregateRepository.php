<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;

/**
 * @method popRecordedEvents()
 */
abstract readonly class AggregateRepository
{
    public function __construct(private AggregateStorage $storage) {}

    public function persist(AggregateRoot $root): void
    {
        $eventExtractor = fn () => $this->popRecordedEvents();
        /** @var AggregateChanged[] $events */
        $events = $eventExtractor->call($root);
        $this->storage->store($root->id, $events);
    }

    /**
     * @return array<mixed>
     */
    protected function loadEventsFor(Id $id): array
    {
        return $this->storage->load($id);
    }
}
