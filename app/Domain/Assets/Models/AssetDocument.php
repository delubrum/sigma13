<?php

declare(strict_types=1);

namespace App\Domain\Assets\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $expiry
 * @property string|null $code
 * @property int $asset_id
 * @property int $user_id
 * @property string|null $url
 * @property-read string|null $viewUrl
 */
#[Fillable([
    'name',
    'expiry',
    'code',
    'asset_id',
    'user_id',
    'url',
])]
class AssetDocument extends Model implements HasMedia
{
    use InteractsWithMedia;

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'expiry' => 'date',
            'asset_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    /**
     * Property Hook (PHP 8.5) para URL Firmada.
     */
    public ?string $viewUrl {
        get {
            if (! Auth::check()) {
                return null;
            }

            /** @var Media|null $media */
            $media = $this->getFirstMedia('documents');

            if (! $media) {
                return null;
            }

            return $media->getTemporaryUrl(
                now()->addMinutes(15),
                '',
                [
                    'ResponseContentDisposition' => 'inline',
                ]
            );
        }
    }

    /** @return BelongsTo<Asset, $this> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents')
            ->singleFile()
            ->useDisk('r2');
    }
}
