<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use DateTimeImmutable;
use DateTimeZone;
use Freyr\Identity\Id;
use JsonSerializable;
use LogicException;

/** @phpstan-consistent-constructor */
abstract class AggregateChanged implements JsonSerializable
{
    /**
     * @param array{_id:string,_occurred_on:array{date:string,timezone:string}} $payload
     * @return static
     */
    final public static function occur(array $payload): static
    {
        if (static::class === self::class) {
            throw new LogicException('Cannot call occur() on abstract AggregateChanged');
        }

        // @phpstan-ignore-next-line new.staticInAbstractClassStaticMethod
        return new static(
            Id::new(),
            static::eventName(),
            new DateTimeImmutable('now', new DateTimeZone('UTC')),
            $payload
        );
    }

    /**
     * @param Id $eventId
     * @param string $name
     * @param DateTimeImmutable $occurredOn
     * @param array{_id:string,_occurred_on:array{date:string,timezone:string}} $payload
     */
    final public function __construct(
        readonly public Id $eventId,
        readonly public string $name,
        readonly public DateTimeImmutable $occurredOn,
        /** @var array{_id:string,_occurred_on:array{date:string,timezone:string}} */
        readonly public array $payload
    ) {
    }

    /**
     * @param array{_id:string,_aggregate_id:string,_occurred_on:array{date:string,timezone:string}} $payload
     * @return static
     */
    final public static function fromArray(array $payload): static
    {
        if (static::class === self::class) {
            throw new LogicException('Cannot call fromArray() on abstract AggregateChanged');
        }

        /**
         * @phpstan-ignore argument.type
         */
        $sanitizePayload = array_diff_assoc(
            $payload,
            ['_id', '_aggregate_id', '_occurred_on']
        );

        $occurredOn = new DateTimeImmutable(
            $payload['_occurred_on']['date'],
            new DateTimeZone($payload['_occurred_on']['timezone'])
        );
        // @phpstan-ignore-next-line new.staticInAbstractClassStaticMethod
        return new static(
            Id::fromString($payload['_id']),
            static::eventName(),
            $occurredOn,
            static::deserializePayload($sanitizePayload), /** @phpstan-ignore argument.type */

        );
    }

    /**
     * @return array{_id:string,_occurred_on:array{date:string,timezone:string}} $payload
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                '_id' => (string)$this->eventId,
                '_occurred_on' => $this->occurredOn,
                '_name' => $this->name,
            ],
            $this->serializePayload()
        );
    }

    /**
     * @param array<string, array<string, string>|string> $payload
     * @return array<string, string> $payload
     */
    abstract protected static function deserializePayload(array $payload): array;

    /**
     * @return array{_id:string,_occurred_on:array{date:string,timezone:string}} $payload
     */
    abstract protected function serializePayload(): array;
    abstract public static function eventName(): string;
}
