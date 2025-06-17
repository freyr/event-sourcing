<?php

declare(strict_types=1);

namespace Freyr\EventSourcing\Tests\Fakes;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateRoot;
use LogicException;

class ExampleAggregate extends AggregateRoot
{
    private Unit $unit = Unit::Test;

    public function addUnit(string $someCommand): void
    {
        if ($someCommand === 'succeeded' && $this->unit === Unit::Test) {
            $this->recordThat(ExampleEvent::occur($this->id, [
                'unit' => Unit::M,
            ]));
        }
    }

    public function isNotTest(): bool
    {
        return $this->unit !== Unit::Test;
    }


    protected function apply(AggregateChanged $event): void
    {
        match (get_class($event)) {
            ExampleEvent::class => $this->unit = $event->unit,
            default => throw new LogicException('Unknown event: ' . get_class($event))
        };
    }
}
