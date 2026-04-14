<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

final class MntItem extends Model
{
    protected $table = 'mnt_items';

    protected $guarded = [];

    public $timestamps = false;
}
