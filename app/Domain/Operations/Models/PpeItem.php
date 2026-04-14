<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class PpeItem extends Model
{
    protected $table = 'epp_db';

    protected $guarded = [];

    public $timestamps = false;
}
