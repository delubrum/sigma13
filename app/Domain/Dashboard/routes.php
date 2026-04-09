<?php

declare(strict_types=1);

use App\Domain\Dashboard\Actions\Index;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/home', Index::class)->name('home');
});
