<?php

declare(strict_types=1);

namespace App\Domain\IT\Models;

use Illuminate\Database\Eloquent\Model;

final class ItItem extends Model
{
    protected $table = 'it_items';

    protected $guarded = [];

    public $timestamps = false;
}
