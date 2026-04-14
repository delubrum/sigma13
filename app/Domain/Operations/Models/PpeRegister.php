<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class PpeRegister extends Model
{
    protected $table = 'epp_register';

    protected $guarded = [];

    public $timestamps = false;
}
