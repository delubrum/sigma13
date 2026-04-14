<?php

declare(strict_types=1);

use App\Domain\Shared\Web\Actions\Create;
use App\Domain\Shared\Web\Actions\Delete;
use App\Domain\Shared\Web\Actions\Detail;
use App\Domain\Shared\Web\Actions\DownloadMedia;
use App\Domain\Shared\Web\Actions\Excel;
use App\Domain\Shared\Web\Actions\Upload;
use App\Domain\Shared\Web\Actions\Upsert;
use Illuminate\Support\Facades\Route;

// SIGMA Shared Orchestrators
Route::middleware('auth')->group(function (): void {
    Route::get('{route}/export', Excel::class)->name('global.export')->where('route', '[a-zA-Z0-9_-]+');
    Route::get('/{route}/create/{id?}', Create::class);
    Route::post('/{route}/upsert', Upsert::class)->name('global.upsert');
    Route::get('/{route}/{id}', [Detail::class, 'asController'])->name('detail')->where('id', '[0-9]+');
    Route::get('/storage/media/{id}', DownloadMedia::class)->name('shared.media.download');
    Route::post('/{route}/{id}/upload', Upload::class)->name('shared.upload');
    Route::delete('/{route}/{id}', Delete::class)->name('global.delete');
});
