<?php

declare(strict_types=1);

namespace Example;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case PLN = 'PLN';
}
