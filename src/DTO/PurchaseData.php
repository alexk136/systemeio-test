<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\DTO;

use App\Validator\TaxNumberValidator;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Callback([TaxNumberValidator::class, 'validate'])]
class PurchaseData extends ProductPriceRecalculationData
{
    #[Assert\NotNull]
    #[Assert\Length(max: 20)]
    public ?string $paymentProcessor = null;
}
