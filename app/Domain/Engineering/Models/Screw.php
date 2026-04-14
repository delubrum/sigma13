<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Models;

use Illuminate\Database\Eloquent\Model;

final class Screw extends Model
{
    protected $table = 'screws';

    protected $guarded = [];

    public $timestamps = false;
}
