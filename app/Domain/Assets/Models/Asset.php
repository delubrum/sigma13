<?php

declare(strict_types=1);

namespace App\Domain\Assets\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
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
    use LogsActivity;

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
            'price'            => 'float',
            'confidentiality'  => 'integer',
            'integrity'        => 'integer',
            'availability'     => 'integer',
            'deleted_at'       => 'datetime',
        ];
    }

    // --- PHP 8.5 Property Hooks ---

    /** Criticality score: sum of CIA dimensions */
    public int $criticalityScore {
        get => ($this->confidentiality ?? 0) + ($this->integrity ?? 0) + ($this->availability ?? 0);
    }

    /** Public photo URL (R2) */
    public string $profilePhotoUrl {
        get {
            $media = $this->getFirstMedia('profile');
            return $media ? $media->getUrl() : '';
        }
    }

    /** Public QR URL */
    public string $qrUrl {
        get => route('assets.public', ['serial' => $this->serial ?: (string) $this->id]);
    }

    // --- Relations (same-module only) ---

    /** @return HasOne<AssetEvent, $this> */
    public function currentAssignment(): HasOne
    {
        return $this->hasOne(AssetEvent::class, 'asset_id')
            ->where('kind', 'assignment')
            ->latestOfMany()
            ->with('employee');
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

    // --- Spatie Config ---

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile')
            ->singleFile()
            ->useDisk('r2_public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10);
    }
}
