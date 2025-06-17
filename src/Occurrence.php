<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Carbon\CarbonImmutable;
use DateTimeZone;

readonly class Occurrence
{
    public function __construct(public CarbonImmutable $time)
    {}

    final public static function now(): self
    {
        return new self(CarbonImmutable::now(new DateTimeZone('UTC')));
    }

    final public static function fromString(string $occurence): self
    {
        return new self(
            new CarbonImmutable(
                $occurence,
                new DateTimeZone('UTC'),
            ),
        );
    }

    public function __toString(): string
    {
        return $this->time->toIso8601ZuluString();
    }
}
