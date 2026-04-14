<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class PrintWo extends Model
{
    protected $table = 'wo';

    protected $guarded = [];
}
