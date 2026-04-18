<?php

declare(strict_types=1);

use App\Domain\Users\Web\Adapters\IndexAdapter as Index;
use App\Domain\Users\Web\Adapters\PermissionsAdapter as Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('users')->name('users.')->group(function (): void {
    Route::get('/', (new Index)->asController(...))->name('index');
    Route::get('/data', (new Index)->asData(...))->name('data');

    // Tab
    Route::get('/{id}/info', (new Permissions)->asController(...))->name('info');

    // Updates
    Route::post('/{id}/status', [Permissions::class, 'asUpdateStatus'])->name('status.update');
    Route::post('/{id}/permission', [Permissions::class, 'asUpdatePermission'])->name('permission.update');
    Route::post('/{id}/reset', [Index::class, 'asResetPassword'])->name('password.reset');
});
