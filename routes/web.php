<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ProductController;
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
    Route::controller(BrandController::class)->group(function () {
        Route::get('/brand', 'index');
        Route::get('/brand/data', 'getBrand');
        Route::post('/brand/store', 'store');
        Route::get('/brand/edit/{id}', 'edit');
        Route::post('/brand/update/{id}', 'update');
        Route::delete('/brand/delete/{id}', 'destroy');
    });
    Route::controller(ProductController::class)->group(function () {
        Route::get('/product', 'index');
        Route::get('/product/data', 'getProduct');
        Route::post('/product/store', 'store');
        Route::get('/product/edit/{id}', 'edit');
        Route::post('/product/update/{id}', 'update');
        Route::delete('/product/delete/{id}', 'destroy');
        Route::post('/update-status','updateStatus');
    });
    Route::controller(MemberController::class)->group(function () {
        Route::get('/member', 'index');
        Route::get('/member/data', 'getMember');
        Route::post('/member/store', 'store');
        Route::get('/member/edit/{id}', 'edit');
        Route::post('/member/update/{id}', 'update');
        Route::delete('/member/delete/{id}', 'destroy');
    });
});

Route::group(['middleware' => 'role:2'], function () {
    // Define your user-specific routes here
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('user.dashboard');
});
Route::fallback([NotFoundController::class, 'index']);
