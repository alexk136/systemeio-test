<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Service\Payment;

use App\Entity\Purchase;

interface PaymentProcessorInterface
{
    /**
     * Process Purchase.
     */
    public function process(Purchase $purchase);
}
