<?php

declare(strict_types=1);

use App\Domain\Identity\Web\Adapters\Auth\LoginAdapter as LoginStoreAdapter;
use App\Domain\Identity\Web\Adapters\Auth\LogoutAdapter;
use App\Domain\Identity\Web\Adapters\LoginAdapter;
use App\Domain\Identity\Web\Adapters\Password\ResetAdapter;
use App\Domain\Identity\Web\Adapters\Password\SendResetLinkAdapter;
use App\Domain\Identity\Web\Adapters\Password\ShowAdapter;
use Illuminate\Support\Facades\Route;

Route::get('/login', LoginAdapter::class)->name('login');
Route::post('/login', LoginStoreAdapter::class)->name('login.store');
Route::post('/logout', LogoutAdapter::class)->name('logout');

// Password Reset
Route::get('/forgot-password', LoginAdapter::class)->name('password.request');
Route::post('/forgot-password', SendResetLinkAdapter::class)->name('password.email');
Route::get('/reset-password/{token}', ShowAdapter::class)->name('password.reset');
Route::post('/reset-password', ResetAdapter::class)->name('password.update');


