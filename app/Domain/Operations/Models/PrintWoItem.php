<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class PrintWoItem extends Model
{
    protected $table = 'wo_items';

    protected $guarded = [];
}
