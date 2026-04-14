<?php

declare(strict_types=1);

namespace App\Domain\Users\Models;

use App\Contracts\CanResetPasswordContract;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'name',
    'email',
    'password',
    'permissions',
    'document',
    'is_active',
    'telegram_chat_id',
    'telegram_link_token',
])]
#[Hidden([
    'password',
    'remember_token',
])]
/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string $password
 * @property array|null $permissions
 * @property string|null $document
 * @property bool $is_active
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $remember_token
 * @property-read string $status_label
 */
class User extends Authenticatable implements CanResetPasswordContract
{
    #[\Override]
    public function updatePassword(string $newPassword): void
    {
        $this->forceFill(['password' => $newPassword])->save();
    }

    #[\Override]
    public function getEmail(): string
    {
        return (string) $this->email;
    }

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use LogsActivity;
    use Notifiable;

    #[\Override]
    protected $appends = ['status_label'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /** @return Attribute<string, never> */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn (): string => sprintf(
                '<span class="px-2 py-0.5 rounded border %s font-bold uppercase text-[10px]">%s</span>',
                $this->is_active ? 'border-green-500 text-green-500' : 'border-red-500 text-red-500',
                $this->is_active ? 'Activo' : 'Inactivo'
            )
        );
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
