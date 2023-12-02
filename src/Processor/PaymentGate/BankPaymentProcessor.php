<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Processor\PaymentGate;

use App\Entity\Purchase;

class BankPaymentProcessor extends AbstractPaymentProcessor
{
    public string $name = 'Bank';

    public function process(Purchase $purchase): void
    {
        // Если SDK требует передачи суммы в копецках (Тинек, например), то делаем $total*100,
        // или берем номинал из symfony intl, вроде там было.

        parent::process($purchase);
    }
}
