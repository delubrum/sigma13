<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Models;

use Illuminate\Database\Eloquent\Model;

final class Fastener extends Model
{
    protected $table = 'screws';

    protected $guarded = [];
}
