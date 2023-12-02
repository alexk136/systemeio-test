<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Provider;

use App\DTO\ProductPriceRecalculationData as Dto;
use App\Exception\ApiException;
use App\Service\CommonService;
use App\Service\Data\DataProviderInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class PriceCalculationProvider implements DataProviderInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private CommonService $commonService
    ) {
    }

    #[ArrayShape(['price' => 'float', 'tax' => 'float'])]
    public function provide(Request $request): array
    {
        $dto = $this->getDtoFromRequest($request->request->all());
        $product = $this->commonService->getProduct($dto->product);
        $coupon = $this->commonService->getCoupon($dto->couponCode);
        $countryTax = $this->commonService->getCountryTax($dto->taxNumber);

        $price = $this->commonService->recalculateProductPriceByCoupon($product, $coupon);
        $tax = $this->commonService->getPriceTax($countryTax, $price);

        return ['price' => $price, 'tax' => $tax];
    }

    private function getDtoFromRequest(array $data): Dto
    {
        $purchaseData = new Dto();
        $purchaseData->product = $data['product'] ? (int) $data['product'] : null;
        $purchaseData->taxNumber = $data['taxNumber'] ?? null;
        $purchaseData->couponCode = $data['couponCode'] ?? null;

        $this->validate($purchaseData);

        return $purchaseData;
    }

    private function validate(Dto $purchaseData): void
    {
        $errors = $this->validator->validate($purchaseData);

        if (\count($errors) > 0) {
            $exception = new ApiException('Validation errors.');
            $exception->setErrors((array) $errors);
            throw $exception;
        }
    }
}
