<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Attribute;

/**
 * Attribute to annotate event classes with a specific name.
 * This can be used at compile time to create a registry of AggregateChange events.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class EventName
{
    /**
     * @param string $name The name of the event
     */
    public function __construct(
        public readonly string $name
    ) {
        if (empty($name)) {
            throw new \InvalidArgumentException('Event name cannot be empty');
        }
    }
}
