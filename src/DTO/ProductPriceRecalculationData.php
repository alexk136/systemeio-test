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
class ProductPriceRecalculationData
{
    #[Assert\NotNull]
    #[Assert\GreaterThan(value: 0)]
    public ?int $product = null;

    #[Assert\NotNull]
    public ?string $taxNumber = null;

    #[Assert\NotNull]
    #[Assert\Length(max: 3)]
    public ?string $couponCode = null;
}
