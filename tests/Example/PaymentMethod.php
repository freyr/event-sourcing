<?php

declare(strict_types=1);

namespace Example;

enum PaymentMethod: string
{
    case CARD = 'card';
    case PAYPAL = 'paypal';
}
