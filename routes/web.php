<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasswordResetController;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIGMA 13
|--------------------------------------------------------------------------
*/

// LOGIN: Definimos la ruta oficial primero para evitar colisiones de nombres.
Route::get('/login', function () {
    return Auth::check() ? to_route('home') : view('auth.login');
})->name('login')->middleware('guest');

// RAÍZ: Redirección simple. KISS y compatible con cache.
Route::get('/', function () {
    return Auth::check() ? to_route('home') : redirect()->route('login');
});

// Dashboard Principal
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Autenticación con soporte HTMX
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $orchestrator = new class { use HtmxOrchestrator; };

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        if ($request->header('HX-Request')) {
            return $orchestrator->hxRedirect('/home');
        }

        return redirect()->intended('/home');
    }

    if ($request->header('HX-Request')) {
        return $orchestrator->hxNotify('Credenciales no válidas.', 'error')
            ->hxResponse(['message' => 'Error'], 422);
    }

    return back()->withErrors(['email' => 'Las credenciales proporcionadas no son válidas.']);
})->name('login.store')->middleware('guest');

/*
|--------------------------------------------------------------------------
| Password Recovery
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', fn() => view('auth.forgot-password'))
    ->name('password.request')
    ->middleware('guest');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.email')
    ->middleware('guest');

Route::get('/reset-password/{token}', fn(string $token, Request $request) => 
    view('auth.reset-password', ['token' => $token, 'email' => $request->email])
)->name('password.reset')->middleware('guest');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->name('password.update')->middleware('guest');

/*
|--------------------------------------------------------------------------
| Session Closure
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');