<?php

declare(strict_types=1);

namespace App\Domain\Assets\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'area', 'hostname', 'serial', 'brand', 'model', 'kind', 'cpu', 'ram',
    'ssd', 'hdd', 'so', 'sap', 'price', 'acquisition_date', 'invoice', 'supplier',
    'warranty', 'status', 'classification', 'confidentiality',
    'integrity', 'availability', 'location', 'phone', 'work_mode',
    'url', 'operator',
])]
class Asset extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
            'price' => 'float',
            'confidentiality' => 'integer',
            'integrity' => 'integer',
            'availability' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<Asset>  $query
     */
    protected function scopeOrderByCriticality(Builder $query, string $direction = 'desc'): void
    {
        if (strtolower($direction) === 'asc') {
            $query->orderByRaw('(confidentiality + integrity + availability) asc');
        } else {
            $query->orderByRaw('(confidentiality + integrity + availability) desc');
        }
    }

    /**
     * @param  Builder<Asset>  $query
     */
    protected function scopeOrderByAssignee(Builder $query, string $direction = 'desc'): void
    {
        $dir = strtolower($direction) === 'asc' ? 'asc' : 'desc';
        $query->leftJoin('asset_events', function ($join): void {
            $join->on('assets.id', '=', 'asset_events.asset_id')
                ->where('asset_events.kind', '=', 'assignment')
                ->whereRaw('asset_events.id = (select max(id) from asset_events where asset_id = assets.id and kind = ?)', ['assignment']);
        })
            ->leftJoin('employees', 'asset_events.employee_id', '=', 'employees.id')
            ->orderBy('employees.name', $dir)
            ->select('assets.*');
    }

    /** @return HasOne<AssetEvent, $this> */
    public function currentAssignment(): HasOne
    {
        return $this->hasOne(AssetEvent::class, 'asset_id')
            ->where('kind', 'assignment')
            ->latestOfMany()
            ->with('employee');
    }

    /** @return Attribute<string, never> */
    protected function assigneeName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->currentAssignment?->employee->name ?? '—',
        );
    }

    /** @return Attribute<string, never> */
    protected function criticality(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $score = ($this->confidentiality ?? 0) + ($this->integrity ?? 0) + ($this->availability ?? 0);
                $color = match (true) {
                    $score >= 8 => 'border-red-500 text-red-500',
                    $score >= 5 => 'border-orange-500 text-orange-500',
                    default => 'border-sigma-b text-sigma-tx2',
                };

                return sprintf('<span class="px-2 py-0.5 rounded border %s font-bold text-[10px]">%d</span>', $color, $score);
            }
        );
    }

    /** @return Attribute<string, never> */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn (): string => sprintf(
                '<span class="px-2 py-0.5 rounded border %s font-bold uppercase text-[10px]">%s</span>',
                match ($this->status) {
                    'available' => 'border-green-500 text-green-500',
                    'assigned' => 'border-blue-500 text-blue-500',
                    'maintenance' => 'border-yellow-500 text-yellow-500',
                    'retired' => 'border-red-500 text-red-500',
                    default => 'border-sigma-b text-sigma-tx2',
                },
                $this->status ?? 'available'
            )
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile')
            ->singleFile()
            ->useDisk('s3');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10);
    }

    /** @return HasMany<AssetDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class, 'asset_id');
    }

    /** @return HasMany<AssetEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(AssetEvent::class, 'asset_id')->orderByDesc('id');
    }
}
