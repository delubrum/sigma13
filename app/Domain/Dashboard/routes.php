<?php

declare(strict_types=1);

use App\Domain\Dashboard\Web\Adapters\DashboardAdapter;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/', DashboardAdapter::class)->name('dashboard');
});
