<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Processor;

use App\Config\PurchaseStatus;
use App\DTO\PurchaseData as Dto;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Exception\ApiException;
use App\Repository\PurchaseRepository;
use App\Service\CommonService;
use App\Service\Data\DataProcessorInterface;
use App\Service\Payment\PaymentProcessorsBus;
use App\Service\RepoService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class PurchaseProcessor implements DataProcessorInterface
{
    public function __construct(
        private PaymentProcessorsBus $paymentProcessorsBus,
        private ValidatorInterface $validator,
        private PurchaseRepository $purchaseRepository,
        private CommonService $commonService,
		private RepoService $repoService
    ) {
    }

    public function process(Request $request): void
    {
        // Процессим покупку
        $dto = $this->getDtoFromRequest($request->request->all());
        $product = $this->repoService->getProduct($dto->product);
        $coupon = $this->repoService->getCoupon($dto->couponCode);
        $purchase = $this->createNewPurchase($dto, $product, $coupon);
        $this->purchaseRepository->save($purchase);
        $this->paymentProcessorsBus->handle($dto->paymentProcessor, $purchase);
    }

    private function createNewPurchase(Dto $dto, Product $product, Coupon $coupon = null): Purchase
    {
        $countryTax = $this->repoService->getCountryTax($dto->taxNumber);
        $price = $this->commonService->recalculateProductPriceByCoupon($product, $coupon);
        $tax = $this->commonService->getPriceTax($countryTax, $price);

        $purchase = new Purchase();
        $purchase->setStatus(PurchaseStatus::Pending);
        $purchase->setPaymentProcessor($dto->paymentProcessor);
        $purchase->setCoupon($coupon);
        $purchase->setProduct($product);
        $purchase->setTotal($price);
        $purchase->setTax($tax);
        $purchase->setTaxNumber($dto->taxNumber);
        $purchase->setPurchasedAt(new \DateTime());

        return $purchase;
    }

    private function getDtoFromRequest(array $data): Dto
    {
        $purchaseData = new Dto();
        $purchaseData->product = $data['product'] ? (int) $data['product'] : null;
        $purchaseData->taxNumber = $data['taxNumber'] ?? null;
        $purchaseData->couponCode = $data['couponCode'] ?? null;
        $purchaseData->paymentProcessor = $data['paymentProcessor'] ?? null;

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
