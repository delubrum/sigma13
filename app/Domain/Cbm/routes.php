<?php

declare(strict_types=1);

namespace App\Domain\Cbm;

use App\Domain\Cbm\Web\Adapters\IndexAdapter as Index;
use App\Domain\Cbm\Web\Adapters\Tabs\ItemsTabAdapter;
use App\Domain\Cbm\Web\Adapters\Tabs\PackingTabAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('cbm')->name('cbm.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
    
    // Create/Edit
    Route::get('/create', \App\Domain\Cbm\Web\Adapters\Modals\CreateModalAdapter::class)->name('create');
    Route::post('/save', [\App\Domain\Cbm\Web\Adapters\Modals\CreateModalAdapter::class, 'save'])->name('save');

    // Detail Tabs
    Route::get('/tabs/packing/{id}', PackingTabAdapter::class)->name('packing');
    Route::get('/tabs/items/{id}', ItemsTabAdapter::class)->name('items');
});
