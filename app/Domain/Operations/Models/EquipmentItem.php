<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class EquipmentItem extends Model
{
    protected $table = 'equipment_db';

    protected $guarded = [];

    public $timestamps = false;
}
