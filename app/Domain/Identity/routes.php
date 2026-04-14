<?php

declare(strict_types=1);

use App\Domain\Identity\Web\Adapters\Auth\LogoutAdapter;
use App\Domain\Identity\Web\Adapters\LoginAdapter;
use Illuminate\Support\Facades\Route;

Route::get('/login', LoginAdapter::class)->name('login');
Route::post('/logout', LogoutAdapter::class)->name('logout');
