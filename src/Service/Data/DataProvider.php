<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Service\Data;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;

final class DataProvider
{
    private ServiceLocator $locator;

    public function __construct(
        ServiceLocator $locator
    ) {
        $this->locator = $locator;
    }

    public function provide(Request $request, string $processorFqcn): array
    {
        if (!is_subclass_of($processorFqcn, DataProviderInterface::class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" must implement interface "%s".', $processorFqcn, DataProviderInterface::class));
        }

        /** @var DataProviderInterface $provider */
        $provider = $this->locator->get($processorFqcn);

        return $provider->provide($request);
    }
}
