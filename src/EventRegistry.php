<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

final class EventRegistry
{
    /**
     * @var array<string, class-string<AggregateChanged>>
     */
    private array $events = [];

    /**
     * @param array<string, class-string<AggregateChanged>> $evenClasses
     */
    public function __construct(array $evenClasses)
    {
        foreach ($evenClasses as $eventName => $evenClass) {
            $this->events[$eventName] = $evenClass;
        }
    }

    /**
     * @return class-string<AggregateChanged>
     */
    public function getBy(string $eventName): string
    {
        return $this->events[$eventName];
    }
}
