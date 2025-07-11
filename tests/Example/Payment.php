<?php

declare(strict_types=1);

namespace Example;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateId;
use Freyr\EventSourcing\AggregateRoot;
use LogicException;

class Payment extends AggregateRoot
{
    private int $amount;
    private Currency $currency;
    private PaymentMethod $paymentMethod;
    private PaymentStatus $status;

    public function newPayment(): self
    {
        $payment = new self(AggregateId::new());
        $payment->recordThat(PaymentCreated::occur($payment->id, [
            'amount' => 100,
            'currency' => Currency::USD,
            'paymentMethod' => PaymentMethod::CARD,
            'status' => PaymentStatus::PENDING,
        ]));

        return $payment;
    }

    public function process(): void
    {
        $this->recordThat(PaymentSentForProcessing::occur($this->id, [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'paymentMethod' => $this->paymentMethod,
            'status' => $this->status,
        ]));
    }

    /** @phpstan-ignore-next-line missingType.generics */
    protected function apply(AggregateChanged $event): void
    {
        match (true) {
            $event instanceof PaymentCreated => $this->onPaymentCreated($event),
            $event instanceof PaymentSentForProcessing => $this->onPaymentSentForProcessing($event),
            default => throw new LogicException('Unknown event ' . $event::class),
        };
    }

    private function onPaymentCreated(PaymentCreated $event): void
    {
        $this->amount = $event->amount;
        $this->currency = $event->currency;
        $this->paymentMethod = $event->paymentMethod;
        $this->status = $event->status;
    }

    private function onPaymentSentForProcessing(PaymentSentForProcessing $event): void
    {
        $this->amount = $event->amount;
        $this->currency = $event->currency;
        $this->paymentMethod = $event->paymentMethod;
        $this->status = $event->status;
    }
}
