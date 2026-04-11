<?php

declare(strict_types=1);

use App\Domain\MaintenanceP\Web\Actions\Index;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('maintenance-p')->name('maintenance-p.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
});
