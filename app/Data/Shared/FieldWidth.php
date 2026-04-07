<?php

declare(strict_types=1);

namespace App\Data\Shared;

enum FieldWidth: int
{
    case Full = 4;
    case ThreeQuarters = 3;
    case Half = 2;
    case Quarter = 1;
}
