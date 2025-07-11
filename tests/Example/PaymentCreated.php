<?php

declare(strict_types=1);

namespace Example;

use Freyr\EventSourcing\AggregateChanged;

class PaymentCreated extends AggregateChanged
{

    public int $amount { get => $this->payload['amount']; }
    public Currency $currency { get => $this->payload['currency']; }
    public PaymentMethod $paymentMethod { get => $this->payload['paymentMethod']; }
    public PaymentStatus $status { get => $this->payload['status']; }

    /**
     * @var array{
     *     amount: int,
     *     currency: Currency,
     *     paymentMethod: PaymentMethod,
     *     status: PaymentStatus
     * }
     */
    protected array $payload;

    /**
     * @param array{
     *     amount: int,
     *     currency: string,
     *     paymentMethod: string,
     *     status: string
     * } $payload
     * @return array{
     *     amount: int,
     *     currency: Currency,
     *     paymentMethod: PaymentMethod,
     *     status: PaymentStatus
     * }
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
     * @return array{
     *     amount: int,
     *     currency: string,
     *     paymentMethod: string,
     *     status: string
     * }
     */
    protected function serializePayload(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'paymentMethod' => $this->paymentMethod->value,
            'status' => $this->status->value
        ];
    }
}