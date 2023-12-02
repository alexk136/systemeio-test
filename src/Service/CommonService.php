<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Service;

use App\Config\CouponType;
use App\Entity\CountryTax;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Exception\ApiException;
use App\Repository\CountryTaxRepository;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;

class CommonService
{
    public function __construct(
        private readonly CountryTaxRepository $countryTaxRepository,
        private readonly ProductRepository $productRepository,
        private readonly CouponRepository $couponRepository,
    ) {
    }

    public function recalculateProductPriceByCoupon(Product $product, Coupon $coupon = null): float
    {
        $basePrice = $product->getPrice();

        if (!$coupon) {
            return $basePrice;
        }

        $newPrice = $basePrice;

        switch ($coupon->getCouponType()) {
            case CouponType::Normal:
                $discount = ($basePrice * $coupon->getPercentage()) / 100;
                $newPrice -= $discount;
                break;
            case CouponType::NeNormal:
                $newPrice -= $coupon->getAmount();
                if ($newPrice < 0) {
                    $newPrice = $basePrice;
                }
                break;
        }

        return $newPrice;
    }

    public function getPriceTax(CountryTax $countryTax, $price): float
    {
        return ($price * $countryTax->getTax()) / 100;
    }

    public function getProduct(int $productId): Product
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new ApiException('Product not found.');
        }

        return $product;
    }

    public function getCoupon(string $couponCode): ?Coupon
    {
        $coupon = null;
        if ($couponCode) {
            $coupon = $this->couponRepository->findOneBy(['code' => $couponCode]);

            if (!$coupon) {
                throw new ApiException('Coupon not found.');
            }
        }

        return $coupon;
    }

    public function getCountryTax(string $taxNumber): CountryTax
    {
        $countryCode = mb_substr($taxNumber, 0, 2);
        $countryTax = $this->countryTaxRepository->findOneBy(['countryCode' => $countryCode]);

        if (!$countryTax) {
            throw new ApiException('Tax for country with code '.$countryCode.' not found.');
        }

        return $countryTax;
    }
}
