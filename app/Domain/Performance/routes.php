<?php

declare(strict_types=1);

use App\Domain\Performance\Web\Adapters\PerformanceAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('performance', PerformanceAdapter::class)->name('performance');
    Route::get('performance/data', [PerformanceAdapter::class, 'asData'])->name('performance.data');
});
