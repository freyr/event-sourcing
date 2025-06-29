<?php

declare(strict_types=1);

namespace Freyr\EventSourcing\Tests\Fakes;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\EventName;

/**
 * @phpstan-type ExamplePayloadSerialized array{
 *     fakeField: string,
 *     unit: string,
 *     parameters: array<string, string>
 * }
 *
 * @phpstan-type ExamplePayload array{
 *      fakeField: string,
 *      unit: Unit,
 *      parameters: array<string, string>
 *  }
 *
 * @property-read ExamplePayload $payload
 */
#[EventName(name: 'example.event')]
class ExampleEvent extends AggregateChanged
{
    public string $fakeField {
        get => $this->payload['fakeField'];
    }

    public Unit $unit {
        get => $this->payload['unit'];
    }

    /** @var array<string, string> */
    public array $parameters {
        get => $this->payload['parameters'];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    protected static function deserializePayload(array $payload): array
    {
        /** @var ExamplePayloadSerialized $payload */
        return [
            'fakeField' => $payload['fakeField'],
            'unit' => Unit::tryFrom($payload['unit']),
            'parameters' => $payload['parameters'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializePayload(): array
    {
        /** @var Unit $unit */
        $unit = $this->payload['unit'];

        return [
            'fakeField' => $this->payload['fakeField'],
            'unit' => $unit->value,
            'parameters' => $this->payload['parameters'],
        ];
    }
}
