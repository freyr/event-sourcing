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
     * @param AggregateId $aggregateId
     * @param array<string, mixed> $payload
     * @throws DateMalformedStringException
     */
    final public static function occur(AggregateId $aggregateId, array $payload): static
    {
        if (static::class === self::class) {
            throw new LogicException('Cannot call occur() on abstract AggregateChanged');
        }

        /** @phpstan-ignore-next-line new.staticInAbstractClassStaticMethod */
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
        readonly protected array $payload
    ) {
    }

    /**
     * @param array{
     *     _id: string,
     *     _aggregate_id: string,
     *     _occurred_on: array{date: string, timezone: string},
     *     ...
     * } $payload
     * @throws DateInvalidTimeZoneException
     * @throws DateMalformedStringException
     * @return static
     */
    final public static function fromArray(array $payload): static
    {
        if (static::class === self::class) {
            throw new LogicException('Cannot call fromArray() on abstract AggregateChanged');
        }

        $sanitizePayload = array_diff_key(
            $payload,
            array_flip([
                '_id',
                '_aggregate_id',
                '_occurred_on',
            ])
        );

        $occurredOn = new DateTimeImmutable(
            $payload['_occurred_on']['date'],
            new DateTimeZone($payload['_occurred_on']['timezone'])
        );

        /** @phpstan-ignore-next-line new.staticInAbstractClassStaticMethod */
        return new static(
            Id::fromString($payload['_id']),
            AggregateId::fromString($payload['_aggregate_id']),
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
                '_aggregate_id' => (string)$this->aggregateId,
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
