<?php

declare(strict_types=1);

use App\Domain\Ppe\Web\Adapters\DeliveriesIndexAdapter as DeliveriesIndex;
use App\Domain\Ppe\Web\Adapters\EntriesIndexAdapter as EntriesIndex;
use App\Domain\Ppe\Web\Adapters\ItemsIndexAdapter as ItemsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->prefix('ppe')->name('ppe.')->group(function (): void {
    
    Route::prefix('items')->name('items.')->group(function (): void {
        Route::get('/', ItemsIndex::class)->name('index');
        Route::get('/data', [ItemsIndex::class, 'asData'])->name('data');
    });

    Route::prefix('deliveries')->name('deliveries.')->group(function (): void {
        Route::get('/', DeliveriesIndex::class)->name('index');
        Route::get('/data', [DeliveriesIndex::class, 'asData'])->name('data');
    });

    Route::prefix('entries')->name('entries.')->group(function (): void {
        Route::get('/', EntriesIndex::class)->name('index');
        Route::get('/data', [EntriesIndex::class, 'asData'])->name('data');
    });
});
