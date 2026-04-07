<?php

declare(strict_types=1);

use App\Actions\Users\Index;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('users')->name('users.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
});
