<?php

declare(strict_types=1);

namespace Example;

use Freyr\EventSourcing\AggregateChanged;

/**
 * @phpstan-type PayloadDeserialized array{
 *     amount: int,
 *     currency: Currency,
 *     paymentMethod: PaymentMethod,
 *     status: PaymentStatus
 * }
 *
 * @phpstan-type PayloadSerialized array{
 *     amount: int,
 *     currency: string,
 *     paymentMethod: string,
 *     status: string
 * }
 *
 * @extends AggregateChanged<PayloadDeserialized, PayloadSerialized>
 */
class PaymentSentForProcessing extends AggregateChanged
{
    public int $amount { get => $this->payload['amount']; }
    public Currency $currency { get => $this->payload['currency']; }
    public PaymentMethod $paymentMethod { get => $this->payload['paymentMethod']; }
    public PaymentStatus $status { get => $this->payload['status']; }

    /**
     * @param PayloadSerialized $payload
     * @return PayloadDeserialized
     */
    protected static function deserializePayload(array $payload): array
    {
        return [
            'amount' => $payload['amount'],
            'currency' => Currency::from($payload['currency']),
            'paymentMethod' => PaymentMethod::from($payload['paymentMethod']),
            'status' => PaymentStatus::from($payload['status']),
        ];
    }

    /**
     * @return PayloadSerialized
     */
    protected function serializePayload(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'paymentMethod' => $this->paymentMethod->value,
            'status' => $this->status->value,
        ];
    }
}
