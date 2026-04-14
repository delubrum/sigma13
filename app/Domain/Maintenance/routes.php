<?php

declare(strict_types=1);

use App\Domain\Maintenance\Web\Adapters\LocativeAdapter;
use App\Domain\Maintenance\Web\Adapters\MaintenanceAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('maintenance', MaintenanceAdapter::class)->name('maintenance');
    Route::get('maintenance/data', [MaintenanceAdapter::class, 'asData'])->name('maintenance.data');

    Route::get('maintenance/locative', LocativeAdapter::class)->name('maintenance.locative');
    Route::get('maintenance/locative/data', [LocativeAdapter::class, 'asData'])->name('maintenance.locative.data');
});
