<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class EquipmentRegister extends Model
{
    protected $table = 'equipment_register';

    protected $guarded = [];

    public $timestamps = false;
}
