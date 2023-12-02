<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    public function __construct(
        array $data = [],
        array $error = null,
        int $status = 200,
        array $headers = [],
        bool $json = false)
    {
        parent::__construct(['result' => $data, 'error' => $error], $status, $headers, $json);
    }
}
