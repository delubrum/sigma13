<?php

declare(strict_types=1);

use App\Actions\Auth\Login;
use App\Actions\Auth\Logout;
use App\Actions\Dashboard\Index;
use App\Actions\Password\Reset;
use App\Actions\Password\SendResetLink;
use App\Actions\Password\Show;
use App\Actions\Shared\Create;
use App\Actions\Shared\Detail;
use App\Actions\Shared\Excel;
use App\Actions\Shared\Upload;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::redirect('/', '/home');

require __DIR__.'/modules/assets.php';
require __DIR__.'/modules/users.php';
require __DIR__.'/modules/it.php';
require __DIR__.'/modules/maintenance.php';
require __DIR__.'/modules/maintenancep.php';
require __DIR__.'/modules/recruitment.php';



Route::middleware('guest')->group(function (): void {
    Route::get('/login', fn (): Factory|\Illuminate\Contracts\View\View => view('auth.login'))->name('login');
    Route::post('/login', Login::class)->name('login.store')->middleware(ProtectAgainstSpam::class);
    Route::post('/forgot-password', SendResetLink::class)->name('password.email');
    Route::get('/reset-password/{token}', Show::class)->name('password.reset');
    Route::post('/reset-password', Reset::class)->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', Logout::class)->name('logout');
    Route::get('/home', Index::class)->name('home');
});

// SIGMA Shared Orchestrators
Route::get('{route}/export', Excel::class)->name('global.export')->where('route', '[a-zA-Z0-9_-]+');
Route::get('/{route}/create', Create::class);
Route::get('/{route}/{id}', [Detail::class, 'asController'])->name('detail');
Route::post('/{route}/{id}/upload', Upload::class)->name('shared.upload');
