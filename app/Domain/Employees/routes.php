<?php

declare(strict_types=1);

use App\Domain\Employees\Web\Actions\Index;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->prefix('employees')->name('employees.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('/data', [Index::class, 'asData'])->name('data');
});
