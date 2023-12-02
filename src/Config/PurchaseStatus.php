<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Config;

enum PurchaseStatus: int
{
    case Pending = 1;
    case Process = 2;
    case Canceled = 3;
    case Error = 4;
    case Success = 5;
}
