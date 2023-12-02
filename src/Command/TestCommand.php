<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Command;

use App\Exception\ApiException;
use App\Processor\PurchaseProcessor;
use App\Provider\PriceCalculationProvider;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use App\Service\Payment\PaymentProcessorsBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;

#[AsCommand(
    name: 'app:test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{
    public function __construct(
        private readonly PurchaseProcessor $purchaseProcessor,
        private readonly PriceCalculationProvider $priceCalculationProvider,
        private readonly ProductRepository $productRepository,
        private readonly CouponRepository $couponRepository,
        private readonly PaymentProcessorsBus $paymentProcessorsBus,
        private readonly PurchaseRepository $purchaseRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Тестируем покупку
        $this->makePurchase($input, $output);

        $this->recalculatePrice($input, $output);

        return Command::SUCCESS;
    }

    private function makePurchase(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $taxNumbers = ['IT12345678900', 'DE123456789', 'GR123456789', 'FRFR123456789', '12DE000001'];
        $productIds = $this->getProductIds();
        $couponCodes = $this->getCouponsCodes();
        $paymentProcessors = array_keys($this->paymentProcessorsBus->getSubscribedServices());

        $randomPurchase = [
            'product' => $productIds[array_rand($productIds)],
            'taxNumber' => $taxNumbers[array_rand($taxNumbers)],
            'couponCode' => $couponCodes[array_rand($couponCodes)],
            'paymentProcessor' => $paymentProcessors[array_rand($paymentProcessors)],
        ];

        $request = new Request([], $randomPurchase);

        try {
            $this->purchaseProcessor->process($request);
        } catch (ApiException $e) {
            $io->error($e->getMessage());
            $this->printErrors($e);

            return;
        }

        $purchase = $this->purchaseRepository->findOneBy([], ['id' => 'DESC']);
        echo "\nPURCHASE ({$purchase->getId()}):\n";
        echo "Product Id: {$randomPurchase['product']}\n";
        echo "TaxNumber: {$randomPurchase['taxNumber']}\n";
        echo "Coupon Code: {$randomPurchase['couponCode']}\n";
        echo "Payment Processor: {$randomPurchase['paymentProcessor']}\n";
        echo "Purchased at: {$purchase->getPurchasedAt()?->format('Y-m-d h:m')}\n";
        echo "Purchase price (no tax): {$purchase->getTotal()} euro\n";
        echo "Purchase tax: {$purchase->getTax()} euro\n";
        echo "Purchase total: {$purchase->getFinalPrice()} euro\n";
        echo "Purchase status: {$purchase->getStatus()->name}\n";
        echo str_repeat('-', 30)."\n";

        $io->success('Purchase complete.');
    }

    private function recalculatePrice(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $taxNumbers = ['IT12345678900', 'DE123456789', 'GR123456789', 'FRFR123456789', '12DE000001'];
        $productIds = $this->getProductIds();
        $couponCodes = $this->getCouponsCodes();

        $randomProduct = [
            'product' => $productIds[array_rand($productIds)],
            'taxNumber' => $taxNumbers[array_rand($taxNumbers)],
            'couponCode' => $couponCodes[array_rand($couponCodes)],
        ];

        $request = new Request([], $randomProduct);

        try {
            $result = $this->priceCalculationProvider->provide($request);
        } catch (ApiException $e) {
            $io->error($e->getMessage());
            $this->printErrors($e);

            return;
        }
        $total = array_sum($result);

        echo "\nRecalculated price:\n";
        echo "Product Id: {$randomProduct['product']}\n";
        echo "TaxNumber: {$randomProduct['taxNumber']}\n";
        echo "Coupon Code: {$randomProduct['couponCode']}\n";
        echo "Product price (no tax): {$result['price']} euro\n";
        echo "Product tax: {$result['tax']} euro\n";
        echo "Product total: {$total} euro\n";
        echo str_repeat('-', 30)."\n";

        $io->success('Price recalculation complete.');
    }

    private function getProductIds(): array
    {
        $products = $this->productRepository->findAll();
        if (!$products) {
            throw new \Exception('Add products to DB');
        }

        $result = ['a'];
        foreach ($products as $product) {
            $result[] = $product->getId();
        }

        return $result;
    }

    private function getCouponsCodes(): array
    {
        $coupons = $this->couponRepository->findAll();

        $result = [''];
        foreach ($coupons as $coupon) {
            $result[] = $coupon->getCode();
        }

        return $result;
    }

    private function printErrors(ApiException $e): void
    {
        $errors = $e->getErrors();
        $uniqErrors = [];
        if ($errors) {
            echo "Errors:\n";
            foreach ($errors as $violations) {
                if (\is_array($violations)) {
                    /* @var ConstraintViolation $error */
                    foreach ($violations as $error) {
                        $errorHash = $error->getMessage();
                        if (!\in_array($errorHash, $uniqErrors, true)) {
                            echo $error->getPropertyPath().' - '.$error->getMessage()."\n";
                            $uniqErrors[] = md5($error->getMessage());
                        }
                    }
                } else {
                    echo $violations;
                }
            }
        }
        $e->setErrors([]);
    }
}
