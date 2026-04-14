<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class Equipment extends Model
{
    protected $table = 'equipment';

    protected $guarded = [];
}
