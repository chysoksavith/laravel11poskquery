<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotFoundController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'login');
    Route::post('login_post', 'login_post');
    Route::get('logout', 'logout');
});
Route::group(['middleware' => 'role:1', 'prefix' => 'admin'], function () {
    // Define your admin-specific routes here
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/category', 'index');
        Route::get('/category/data', 'getCategory');
        Route::post('/category/store', 'store');
        Route::get('/category/edit/{id}', 'edit');
        Route::post('/category/update/{id}', 'update');
        Route::delete('/category/delete/{id}', 'destroy');
    });
});

Route::group(['middleware' => 'role:2'], function () {
    // Define your user-specific routes here
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('user.dashboard');
});
Route::fallback([NotFoundController::class, 'index']);
