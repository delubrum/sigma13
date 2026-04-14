<?php

declare(strict_types=1);

use App\Domain\HR\Web\Adapters\IndexAdapter as Index;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('employees')->name('employees.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
});
