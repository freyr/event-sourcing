<?php

declare(strict_types=1);

namespace Freyr\EventSourcing\Tests;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\Tests\Fakes\Unit;

class ExampleEvent extends AggregateChanged
{
    public string $fakeField {
        get => $this->payload['fakeField'];
    }

    public Unit $unit {
        get => $this->payload['unit'];
    }

    public array $parameters {
        get => $this->payload['parameters'];
    }

    protected static function deserializePayload(array $payload): array
    {
        return [
            'fakeField' => $payload['fakeField'],
            'unit' => Unit::tryFrom($payload['unit']),
            'parameters' => $payload['parameters'],
        ];
    }

    protected function serializePayload(): array
    {
        return [
            'fakeField' => $this->fakeField,
            'unit' => $this->unit->value,
            'parameters' => $this->parameters,
        ];
    }
}