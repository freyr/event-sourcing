<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;

interface AggregateStorage
{
    /**
     * @param AggregateChanged[] $events
     */
    public function store(Id $id, array $events): void;

    /**
     * @return array<mixed>
     */
    public function load(Id $id): array;
}
