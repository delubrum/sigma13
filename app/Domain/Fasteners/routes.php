<?php

declare(strict_types=1);

namespace App\Domain\Fasteners;

use App\Domain\Fasteners\Web\Adapters\IndexAdapter as Index;
use App\Domain\Fasteners\Web\Adapters\Tabs\DetailsTabAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('fasteners')->name('fasteners.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    
    // Detail Tabs
    Route::get('/tabs/details/{id}', DetailsTabAdapter::class)->name('details');
});
