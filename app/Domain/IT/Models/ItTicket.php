<?php

declare(strict_types=1);

namespace App\Domain\IT\Models;

use Illuminate\Database\Eloquent\Model;

final class ItTicket extends Model
{
    protected $table = 'it';

    protected $guarded = [];
}
