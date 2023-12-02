<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Service\Payment;

use App\Entity\Purchase;
use App\Exception\ApiException;
use App\Processor\PaymentGate\BankPaymentProcessor;
use App\Processor\PaymentGate\PaypalPaymentProcessor;
use App\Processor\PaymentGate\StripePaymentProcessor;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final readonly class PaymentProcessorsBus implements ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $locator,
    ) {
    }

    public static function getSubscribedServices(): array
    {
        // Можно назначить через service.yaml, как больше нравится.
        // Такой подход тут для примера.
        return [
            'paypal' => PaypalPaymentProcessor::class,
            'bank' => BankPaymentProcessor::class,
            'stripe' => StripePaymentProcessor::class,
        ];
    }

    public function handle(string $paymentProcessor, Purchase $purchase): mixed
    {
        if ($this->locator->has($paymentProcessor)) {
            $handler = $this->locator->get($paymentProcessor);

            if (!is_subclass_of($handler, PaymentProcessorInterface::class)) {
                throw new \InvalidArgumentException(sprintf('Class "%s" must implement interface "%s".', $handler, PaymentProcessorInterface::class));
            }

            /* @var PaymentProcessorInterface $handler */
            return $handler->process($purchase);
        }
        throw new ApiException('Payment Processor Not Found.');
    }
}
