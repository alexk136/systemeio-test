<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Service;

use App\Config\CouponType;
use App\Config\PurchaseStatus;
use App\Entity\CountryTax;
use App\Entity\Coupon;
use App\Entity\Product;
use JetBrains\PhpStorm\Pure;

class CommonService
{
    #[Pure]
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

	#[Pure]
	public function getPriceTax(CountryTax $countryTax, $price): float
    {
        return ($price * $countryTax->getTax()) / 100;
    }

	public function getRandomStatus(): PurchaseStatus {
		$statusInt = mt_rand(2, 5);
		return PurchaseStatus::from($statusInt);
	}
}
