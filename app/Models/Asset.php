<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
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
            'price' => 'decimal:2',
            'confidentiality' => 'integer',
            'integrity' => 'integer',
            'availability' => 'integer',
            'deleted_at' => 'datetime',
        ];
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
            ->nonQueued();
    }

    /** @return HasOne<AssetEvent, $this> */
    public function currentAssignment(): HasOne
    {
        return $this->hasOne(AssetEvent::class, 'asset_id')
            ->where('kind', 'assignment')
            ->latestOfMany();
    }

    /** @return Attribute<?string, never> */
    protected function assigneeName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->currentAssignment?->employee?->name,
        );
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
