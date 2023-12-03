<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Processor\PaymentGate;

use App\Config\PurchaseStatus;
use App\Entity\Purchase;
use App\Exception\ApiException;
use App\Repository\PurchaseRepository;
use App\Service\CommonService;
use App\Service\Payment\PaymentProcessorInterface;

class AbstractPaymentProcessor implements PaymentProcessorInterface
{
    public string $name = '';

    public function __construct(
        readonly private PurchaseRepository $purchaseRepository,
		readonly private CommonService $commonService
    ) {
    }

    public function process(Purchase $purchase): void
    {
        // Для теста различных статусов, мы берем рандомный статус, только для примера.
        $status = $this->commonService->getRandomStatus();
        $error = PurchaseStatus::Error === $status;
        $purchase->setStatus($status);

        // Если у нас ошибка, то нам не нужно ставить PurchasedAt
        if (!$error) {
            $purchase->setPurchasedAt(new \DateTime());
        }

        $this->purchaseRepository->save($purchase, true);

        if ($error) {
            throw new ApiException("Payment error with payment gate {$this->name}");
        }
    }
}
