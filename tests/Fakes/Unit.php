<?php

declare(strict_types=1);

namespace Freyr\EventSourcing\Tests\Fakes;

enum Unit: string
{
    case Test = 'test';
    case KG = 'kg';
    case M = 'm';
}