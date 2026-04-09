<?php

declare(strict_types=1);

use App\Domain\Identity\Actions\Auth\Login;
use App\Domain\Identity\Actions\Auth\Logout;
use App\Domain\Identity\Actions\Password\Reset;
use App\Domain\Identity\Actions\Password\SendResetLink;
use App\Domain\Identity\Actions\Password\Show;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', fn (): Factory|\Illuminate\Contracts\View\View => view('auth.login'))->name('login');
    Route::post('/login', Login::class)->name('login.store')->middleware(ProtectAgainstSpam::class);
    Route::post('/forgot-password', SendResetLink::class)->name('password.email');
    Route::get('/reset-password/{token}', Show::class)->name('password.reset');
    Route::post('/reset-password', Reset::class)->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', Logout::class)->name('logout');
});
