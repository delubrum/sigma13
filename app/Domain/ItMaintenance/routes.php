<?php

declare(strict_types=1);

use App\Domain\ItMaintenance\Web\Actions\Index;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('it-maintenance')->name('it-maintenance.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
});
