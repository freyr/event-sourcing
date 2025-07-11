<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;

/**
 * @phpstan-type AnyEvent AggregateChanged<array<string, mixed>, array<string, mixed>>
 */
final class AggregateMemoryStorage implements AggregateStorage
{
    /**
     * @var array<string, array<string>>
     */
    public array $events;

    /**
     * @param List<AnyEvent> $events
     */
    public function store(Id $id, array $events): void
    {
        $serializedEvents = array_map('json_encode', $events);
        array_push($this->events[(string) $id], ...$serializedEvents);
    }

    /**
     * @param Id $id
     * @return array<mixed>
     */
    public function load(Id $id): array
    {
        $serializedEvents = $this->events[(string) $id] ?? [];
        return array_map(static fn ($item) => json_decode($item, true), $serializedEvents);
    }
}
