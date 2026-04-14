<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

final class Recruitment extends Model
{
    protected $table = 'recruitment';

    protected $guarded = [];
}
