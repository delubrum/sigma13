<?php

declare(strict_types=1);

use App\Domain\MaintenanceP\Web\Adapters\IndexAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('maintenancep', IndexAdapter::class)->name('maintenancep');
    Route::get('maintenancep/data', [IndexAdapter::class, 'asData'])->name('maintenancep.data');
});
