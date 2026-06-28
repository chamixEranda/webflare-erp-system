<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Guest Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware('guest')->prefix('auth')->name('auth.')->group(function () {

            Route::get('/login', [AuthController::class, 'login'])
                ->name('login');

            Route::post('/login', [AuthController::class, 'doLogin'])
                ->name('doLogin');

        });

        /*
        |--------------------------------------------------------------------------
        | Authenticated Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware('auth')->group(function () {

            Route::get('/dashboard', function () {
                return view('admin.dashboard');
            })->name('dashboard');

            Route::post('/auth/logout', [AuthController::class, 'logout'])
                ->name('auth.logout');

        });

    });