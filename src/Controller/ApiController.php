<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Controller;

use App\Processor\PurchaseProcessor;
use App\Provider\PriceCalculationProvider;
use App\Response\ApiResponse;
use App\Service\Data\DataProcessor;
use App\Service\Data\DataProvider;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    public function __construct(
        private readonly DataProcessor $dataProcessor,
        private readonly DataProvider $dataProvider
    ) {
    }

    #[Route('/api/price/calculation', name: 'app_price_calculation', methods: ['POST'])]
	#[OA\Post(
		path: "/api/price/calculation",
		summary: "Recalculates Product Price",
		requestBody: new OA\RequestBody(
			description: "",
			required: true,
			content: new OA\MediaType(
				mediaType: "application/json",
				schema: new OA\Schema(
					properties: [
						new OA\Property(property: "product", type: "integer", example: 1),
						new OA\Property(property: "taxNumber", type: "string", example: "DE123456789"),
						new OA\Property(property: "couponCode", type: "string", example: "D15")
					],
					type: "object"
				)
			)
		),
		tags: ["Product Pricing"],
		responses: [
			new OA\Response(
				response: 200,
				description: "Recalculates Product price.",
				content: new OA\MediaType(
					mediaType: "application/json",
					schema: new OA\Schema(
						properties: [
							new OA\Property(property: "price", type: "float", example: 19.99),
							new OA\Property(property: "tax", type: "float", example: 1.99)
						],
						type: "object"
					)
				)
			)
		]
	)]
    public function calculate(Request $request): ApiResponse
    {
        // Пересчитываем цену
        $result = $this->dataProvider->provide($request, PriceCalculationProvider::class);
        return new ApiResponse($result);
    }

    #[Route('/api/purchase', name: 'app_purchase', methods: ['POST'])]
	#[OA\Post(
		path: "/api/purchase",
		summary: "Purchase product.",
		requestBody: new OA\RequestBody(
			description: "",
			required: true,
			content: new OA\MediaType(
				mediaType: "application/json",
				schema: new OA\Schema(
					properties: [
						new OA\Property(property: "product", type: "integer", example: 1),
						new OA\Property(property: "taxNumber", type: "string", example: "DE123456789"),
						new OA\Property(property: "couponCode", type: "string", example: "D15"),
						new OA\Property(property: "paymentProcessor", type: "string", example: "paypal")
					],
					type: "object"
				)
			)
		),
		tags: ["Purchase product"],
		responses: [
			new OA\Response(
				response: 200,
				description: "Purchase product"
			)
		]
	)]
    public function purchase(Request $request): ApiResponse
    {
        // Проводим покупку
        $this->dataProcessor->process($request, PurchaseProcessor::class);
        return new ApiResponse();
    }
}
