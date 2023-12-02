<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Processor\PaymentGate;

use App\Entity\Purchase;
use App\Exception\ApiException;

class PaypalPaymentProcessor extends AbstractPaymentProcessor
{
    public string $name = 'PayPal';

    public function process(Purchase $purchase): void
    {
        if ($purchase->getTotal() > 100000) {
            $exception = new ApiException('[#14271] Transaction "c82711ca-7e67-41c8-9f35-5b965e645d12" failed: Too high price');
            $exception->setErrors(['paymentProcessor' => $purchase->getPaymentProcessor()]);
        }

        // Если SDK требует передачи суммы в копецках (Тинек, например), то делаем $total*100,
        // или берем номинал из symfony intl, вроде там было.

        parent::process($purchase);
    }
}
