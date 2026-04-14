<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class Inspection extends Model
{
    protected $table = 'inspections';

    protected $guarded = [];
}
