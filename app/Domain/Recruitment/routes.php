<?php

declare(strict_types=1);

use App\Domain\Recruitment\Web\Adapters\IndexAdapter as Index;
use App\Domain\Recruitment\Web\Adapters\JobProfilesAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('recruitment')->name('recruitment.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');

    Route::prefix('profiles')->name('profiles.')->group(function (): void {
        Route::get('/', JobProfilesAdapter::class)->name('index');
        Route::get('/data', [JobProfilesAdapter::class, 'asData'])->name('data');
    });
});
