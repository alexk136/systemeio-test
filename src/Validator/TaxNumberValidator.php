<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Validator;

use App\DTO\ProductPriceRecalculationData;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TaxNumberValidator
{
    public static function validate(ProductPriceRecalculationData $dto, ExecutionContextInterface $context, $payload)
    {
        if ($dto->taxNumber && !self::validateTaxNumber($dto->taxNumber)) {
            $context
                ->buildViolation("Tax number '{$dto->taxNumber}' is not valid or unknown tax number.")
                ->atPath('fileType')
                ->addViolation();
        }
    }

    private static function validateTaxNumber(string $taxNumber): bool
    {
        $countryCode = mb_substr($taxNumber, 0, 2);
        $pattern = '';

        switch ($countryCode) {
            case 'DE':
                // DE за которыми следуют 9 цифр
                $pattern = '/^DE\d{9}$/';
                break;
            case 'IT':
                // IT за которыми следуют 11 цифр
                $pattern = '/^IT\d{11}$/';
                break;
            case 'GR':
                // GR за которыми следуют 9 цифр
                $pattern = '/^GR\d{9}$/';
                break;
            case 'FR':
                // FR за которыми следуют 2 буквы и 9 цифр
                $pattern = '/^FR[a-zA-Z]{2}\d{9}$/';
                break;
            default:
                return false;
        }

        return 1 === preg_match($pattern, $taxNumber);
    }
}
