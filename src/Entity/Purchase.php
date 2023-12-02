<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Entity;

use App\Config\PurchaseStatus;
use App\Repository\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'product', referencedColumnName: 'id')]
    private Product $product;

    #[ORM\Column(type: Types::STRING)]
    private string $taxNumber;

    #[ORM\ManyToOne(targetEntity: Coupon::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'coupon', referencedColumnName: 'id', nullable: true)]
    private ?Coupon $coupon = null;

    #[ORM\Column(type: Types::STRING)]
    private string $paymentProcessor = '';

    #[ORM\Column(type: Types::INTEGER, enumType: PurchaseStatus::class)]
    private PurchaseStatus $status;

    #[ORM\Column(type: Types::DECIMAL)]
    private float $total;

    #[ORM\Column(type: Types::DECIMAL)]
    private float $tax;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $purchasedAt = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getTaxNumber(): string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    public function getCoupon(): Coupon
    {
        return $this->coupon;
    }

    public function setCoupon(?Coupon $coupon): void
    {
        $this->coupon = $coupon;
    }

    public function getPaymentProcessor(): string
    {
        return $this->paymentProcessor;
    }

    public function setPaymentProcessor(string $paymentProcessor): void
    {
        $this->paymentProcessor = $paymentProcessor;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getStatus(): PurchaseStatus
    {
        return $this->status;
    }

    public function setStatus(PurchaseStatus $status): void
    {
        $this->status = $status;
    }

    public function getPurchasedAt(): ?\DateTime
    {
        return $this->purchasedAt;
    }

    public function setPurchasedAt(?\DateTime $purchasedAt): void
    {
        $this->purchasedAt = $purchasedAt;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax): void
    {
        $this->tax = $tax;
    }

    public function getFinalPrice(): float
    {
        return $this->getTotal() + $this->getTax();
    }
}
