<?php

declare(strict_types=1);

/*
 * This file is part of the systeme.io Test Project.
 *
 * Copyright (c) 2023.
 */

namespace App\Config;

enum CouponType: int
{
    // процент от суммы покупки
    case Normal = 1;

    // фиксированная сумма скидки
    case NeNormal = 2;
}
