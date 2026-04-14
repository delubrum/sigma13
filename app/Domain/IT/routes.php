<?php

declare(strict_types=1);

use App\Domain\IT\Web\Adapters\IndexAdapter as Index;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('it')->name('it.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
});
