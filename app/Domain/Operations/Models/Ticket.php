<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class Ticket extends Model
{
    protected $table = 'tickets';

    protected $guarded = [];
}
