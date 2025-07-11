<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;
use JsonSerializable;
use LogicException;
use ReflectionClass;

/**
 * @phpstan-consistent-constructor
 */
abstract class AggregateChanged implements JsonSerializable
{
    /**
     * @param AggregateId $aggregateId
     * @phpstan-ignore-next-line missingType.iterableValue
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
            Occurrence::now(),
            self::getEventName(),
            $payload
        );
    }

    /** @phpstan-ignore-next-line missingType.iterableValue */
    final protected function __construct(
        readonly public Id $eventId,
        readonly AggregateId $aggregateId,
        readonly public Occurrence $occurredOn,
        readonly public string $eventName,
        protected array $payload,
    ) { }

    /**
     * @param array{
     *     _id: string,
     *     _aggregate_id: string,
     *     _occurred_on: string,
     *     _name: string
     * } $payload
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
                '_name'
            ])
        );

        /** @phpstan-ignore-next-line new.staticInAbstractClassStaticMethod */
        return new static(
            Id::fromString($payload['_id']),
            AggregateId::fromString($payload['_aggregate_id']),
            Occurrence::fromString($payload['_occurred_on']),
            $payload['_name'],
            static::deserializePayload($sanitizePayload),
        );
    }

    private static function getEventName(): string
    {
        $reflectionClass = new ReflectionClass(static::class);
        $attribute = $reflectionClass->getAttributes(EventName::class)[0] ?? null;

        if ($attribute === null) {
            throw new LogicException(sprintf('Class "%s" does not have an EventName attribute', static::class));
        }

        /** @var EventName $eventNameAttribute */
        $eventNameAttribute = $attribute->newInstance();
        return $eventNameAttribute->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                '_id' => (string)$this->eventId,
                '_aggregate_id' => (string)$this->aggregateId,
                '_occurred_on' => $this->occurredOn,
                '_name' => $this->eventName,
            ],
            $this->serializePayload()
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    abstract protected static function deserializePayload(array $payload): array;

    /**
     * @return array<string, mixed>
     */
    abstract protected function serializePayload(): array;
}
