<?php

declare(strict_types=1);

use App\Domain\Users\Web\Adapters\IndexAdapter as Index;
use App\Domain\Users\Web\Adapters\Tabs\DetailTabAdapter as Detail;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('users')->name('users.')->group(function (): void {
    Route::get('/', [Index::class, 'asController'])->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');

    // Tabs
    Route::get('/{id}/info', [Detail::class, 'asController'])->name('info');

    // Updates
    Route::post('/{id}/status', [Detail::class, 'asUpdateStatus'])->name('status.update');
    Route::post('/{id}/permission', [Detail::class, 'asUpdatePermission'])->name('permission.update');
    Route::post('/{id}/update-field', [Detail::class, 'asUpdateField'])->name('field.update');
    Route::post('/{id}/reset', [Index::class,  'asResetPassword'])->name('password.reset');
});
