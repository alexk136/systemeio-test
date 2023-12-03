<?php

namespace Service;

use App\Config\CouponType;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Service\CommonService;
use PHPUnit\Framework\TestCase;

class CommonServiceTest extends TestCase {

	public function testRecalculateProductPriceWithoutCoupon()
	{
		$product = $this->createMock(Product::class);
		$product->method('getPrice')->willReturn(100.0);

		$calculator = new CommonService();
		$result = $calculator->recalculateProductPriceByCoupon($product);

		$this->assertEquals(100.0, $result);
	}

	public function testRecalculateProductPriceWithNormalCoupon()
	{
		$product = $this->createMock(Product::class);
		$product->method('getPrice')->willReturn(100.0);

		$coupon = $this->createMock(Coupon::class);
		$coupon->method('getCouponType')->willReturn(CouponType::Normal);
		$coupon->method('getPercentage')->willReturn(10); // 10% discount

		$calculator = new CommonService();
		$result = $calculator->recalculateProductPriceByCoupon($product, $coupon);

		$this->assertEquals(90.0, $result);
	}

	public function testRecalculateProductPriceWithNegativeCoupon()
	{
		$product = $this->createMock(Product::class);
		$product->method('getPrice')->willReturn(100.0);

		$coupon = $this->createMock(Coupon::class);
		$coupon->method('getCouponType')->willReturn(CouponType::NeNormal);
		$coupon->method('getAmount')->willReturn(20.0);

		$calculator = new CommonService();
		$result = $calculator->recalculateProductPriceByCoupon($product, $coupon);

		$this->assertEquals(80.0, $result);
	}
}
