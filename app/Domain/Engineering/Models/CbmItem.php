<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Models;

use Illuminate\Database\Eloquent\Model;

final class CbmItem extends Model
{
    protected $table = 'cbm_items';

    protected $guarded = [];

    public $timestamps = false;
}
