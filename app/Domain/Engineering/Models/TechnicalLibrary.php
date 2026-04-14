<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Models;

use Illuminate\Database\Eloquent\Model;

final class TechnicalLibrary extends Model
{
    protected $table = 'matrices';

    protected $guarded = [];

    public $timestamps = false;
}
