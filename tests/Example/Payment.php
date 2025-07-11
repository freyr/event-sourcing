<?php

declare(strict_types=1);

namespace Example;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateRoot;
use LogicException;

class Payment extends AggregateRoot
{

    protected function apply(AggregateChanged $event): void
    {
        match (get_class($event)) {
            default => throw new LogicException('Unknown event: ' . get_class($event))
        };
    }
}