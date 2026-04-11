<?php

declare(strict_types=1);

namespace App\Domain\Employees\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id', 'name', 'city', 'start_date', 'status', 'profile'
])]
final class Employee extends Model
{
    /** @var string */
    protected $table = 'employees';

    #[\Override]
    public $incrementing = false;
    
    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    public $timestamps = false; // Legacy table uses standard naming but check first

    #[\Override]
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'status'     => 'boolean',
        ];
    }

    /** @return HasMany<PersonalDataUpdate, $this> */
    public function updates(): HasMany
    {
        return $this->hasMany(PersonalDataUpdate::class, 'employee_id', 'id');
    }

    /** @return HasMany<EmployeeDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id', 'id');
    }
}
