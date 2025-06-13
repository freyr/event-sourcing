<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;

interface AggregateStorage
{
    /**
     * @param array<string, array<AggregateChanged>> $events
     */
    public function store(Id $id, array $events): void;

    /**
     * @param Id $id
     * @return array<mixed>
     */
    public function load(Id $id): array;
}
