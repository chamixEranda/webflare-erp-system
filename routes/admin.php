<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Guest Routes
        |--------------------------------------------------------------------------
        |
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
        |
        */
        Route::middleware('auth')->group(function () {

            Route::get('/dashboard', function () {
                return view('admin.dashboard');
            })->name('dashboard');

            Route::post('/auth/logout', [AuthController::class, 'logout'])
                ->name('auth.logout');

            // Users Management
            Route::get('/users', [UsersController::class, 'index'])->name('users.index');
            Route::post('/users', [UsersController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

            // Roles Management
            Route::get('/roles', [RolesController::class, 'index'])->name('roles.index');
            Route::get('/roles/create', [RolesController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}/edit', [RolesController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{role}', [RolesController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [RolesController::class, 'destroy'])->name('roles.destroy');

        });

    });
