<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductCatalog\CategoryController;
use App\Http\Controllers\Admin\ProductCatalog\BrandController;
use App\Http\Controllers\Admin\ProductCatalog\UnitController;

Route::prefix('admin')->name('admin.')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Guest Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware('guest')->prefix('auth')->name('auth.')->group(function () {
            Route::get('/login', [AuthController::class, 'login'])->name('login');
            Route::post('/login', [AuthController::class, 'doLogin'])->name('doLogin');
        });

        /*
        |--------------------------------------------------------------------------
        | Authenticated Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware('auth')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

            //=== Product catalog routes ===//
            Route::prefix('product-catalog')->group(function () {
                Route::resource('categories', CategoryController::class);
                Route::post('categories/list', [CategoryController::class, 'list'])->name('categories.list');
                Route::resource('brands', BrandController::class);
                Route::resource('units', UnitController::class);
            });

        });

    });