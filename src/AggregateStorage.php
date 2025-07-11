<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;

/**
 * @phpstan-type AnyEvent AggregateChanged<array<string, mixed>, array<string, mixed>>
 */
interface AggregateStorage
{
    /**
     * @param list<AnyEvent> $events
     */
    public function store(Id $id, array $events): void;

    /**
     * @return array<mixed>
     */
    public function load(Id $id): array;
}
