<?php

namespace App\Service;

use App\Entity\CountryTax;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Exception\ApiException;
use App\Repository\CountryTaxRepository;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;

readonly class RepoService {
	public function __construct(
		private CountryTaxRepository $countryTaxRepository,
		private ProductRepository    $productRepository,
		private CouponRepository     $couponRepository,
	) {
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