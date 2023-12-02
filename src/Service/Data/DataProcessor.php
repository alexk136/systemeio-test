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

final class DataProcessor
{
    private ServiceLocator $locator;

    public function __construct(
        ServiceLocator $locator
    ) {
        $this->locator = $locator;
    }

    public function process(Request $request, string $processorFqcn)
    {
        if (!is_subclass_of($processorFqcn, DataProcessorInterface::class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" must implement interface "%s".', $processorFqcn, DataProcessorInterface::class));
        }

        /* @var DataProcessorInterface $processor */
        $processor = $this->locator->get($processorFqcn);

        return $processor->process($request);
    }
}
