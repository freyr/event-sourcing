<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

/**
 * @phpstan-type AnyEvent AggregateChanged<array<string, mixed>, array<string, mixed>>
 */
final class EventRegistry
{
    /**
     * @var array<string, class-string<AnyEvent>>
     */
    private array $events = [];

    /**
     * @param array<string, class-string<AnyEvent>> $evenClasses
     */
    public function __construct(array $evenClasses)
    {
        foreach ($evenClasses as $eventName => $evenClass) {
            $this->events[$eventName] = $evenClass;
        }
    }

    /**
     * @return class-string<AnyEvent>
     */
    public function getBy(string $eventName): string
    {
        return $this->events[$eventName];
    }
}
