<?php

declare(strict_types=1);

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    #[\Override]
    protected $table = 'permissions';

    #[\Override]
    protected $fillable = [
        'kind',
        'name',
        'category',
        'title',
        'subtitle',
        'link',
        'props',
        'sort_order',
        'url',
        'icon',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'props' => 'array',
        ];
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    protected function scopeMenu(Builder $query): Builder
    {
        return $query->where('kind', 'menu');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    protected function scopeForUser(Builder $query, User $user): Builder
    {
        $permissions = $user->permissions;

        if (! is_array($permissions) || $permissions === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('id', array_map(intval(...), $permissions));
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    protected function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
