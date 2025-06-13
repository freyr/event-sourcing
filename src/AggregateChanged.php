<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use DateInvalidTimeZoneException;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;
use Freyr\Identity\Id;
use JsonSerializable;
use LogicException;

abstract class AggregateChanged implements JsonSerializable
{
    /**
     * @param array<string, mixed> $payload
     * @return static
     * @throws DateMalformedStringException
     */
    final public static function occur(AggregateId $aggregateId, array $payload): static
    {
        if (static::class === self::class) {
            throw new LogicException('Cannot call occur() on abstract AggregateChanged');
        }

        return new static(
            Id::new(),
            $aggregateId,
            new DateTimeImmutable('now', new DateTimeZone('UTC')),
            $payload
        );
    }

    final protected function __construct(
        readonly public Id $eventId,
        readonly AggregateId $aggregateId,
        readonly public DateTimeImmutable $occurredOn,
        /** @var array<string, mixed> */
        readonly public array $payload
    ) {
    }

    /**
     * @param array<string, array<string, mixed>|mixed> $payload
     * @return static
     * @throws DateInvalidTimeZoneException
     * @throws DateMalformedStringException
     */
    final public static function fromArray(array $payload): static
    {
        if (static::class === self::class) {
            throw new LogicException('Cannot call fromArray() on abstract AggregateChanged');
        }

        $sanitizePayload = array_diff_assoc(
            $payload,
            ['_id', '_aggregate_id', '_occurred_on']
        );

        $occurredOn = new DateTimeImmutable(
            $payload['_occurred_on']['date'],
            new DateTimeZone($payload['_occurred_on']['timezone'])
        );

        return new static(
            Id::fromString($payload['_id']),
            AggregateId::fromString($payload['_id']),
            $occurredOn,
            static::deserializePayload($sanitizePayload),
        );
    }

    /**
     * @return array<string, array<string, mixed>|mixed> $payload
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                '_id' => (string)$this->eventId,
                '_occurred_on' => $this->occurredOn,
            ],
            $this->serializePayload()
        );
    }

    /**
     * @param array<string, array<string, mixed>|mixed> $payload
     * @return array<string, mixed> $payload
     */
    abstract protected static function deserializePayload(array $payload): array;

    /**
     * @return array<string, array<string, string>|string> $payload
     */
    abstract protected function serializePayload(): array;
}
