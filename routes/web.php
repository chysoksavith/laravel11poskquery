<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admmin\PurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\NotFoundController;
use App\Http\Controllers\ProductTestAttrController;
use App\Http\Controllers\SupplierController;
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
        Route::get('/pos', 'showPos')->name('pos.view');
        Route::get('/pos/categories/{slug}', [CategoryController::class, 'showCategory'])->name('pos.categories.show'); // Products by Category
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
        Route::post('/update-status', 'updateStatus');
        Route::get('/generate-product-code', 'generateProductCode');
    });
    Route::controller(MemberController::class)->group(function () {
        Route::get('/member', 'index');
        Route::get('/member/data', 'getMember');
        Route::post('/member/store', 'store');
        Route::get('/member/edit/{id}', 'edit');
        Route::post('/member/update/{id}', 'update');
        Route::delete('/member/delete/{id}', 'destroy');
    });
    Route::controller(SupplierController::class)->group(function () {
        Route::get('/supplier', 'index');
        Route::get('/supplier/data', 'getSupplier');
        Route::post('/supplier/store', 'store');
        Route::get('/supplier/edit/{id}', 'edit');
        Route::post('/supplier/update/{id}', 'update');
        Route::delete('/supplier/delete/{id}', 'destroy');
    });
    Route::controller(ExpenseController::class)->group(function () {
        Route::get('/expense', 'index');
        Route::get('/expense/data', 'geExpense');
        Route::post('/expense/store', 'store');
        Route::get('/expense/edit/{id}', 'edit');
        Route::post('/expense/update/{id}', 'update');
        Route::delete('/expense/delete/{id}', 'destroy');
    });
    Route::controller(PurchaseController::class)->group(function () {
        Route::get('/purchase', 'index');
        Route::get('/purchase/data', 'getPurchase');
        Route::post('/purchase/store', 'store');
        Route::get('/purchase/edit/{id}', 'edit');
        Route::post('/purchase/update/{id}', 'update');
        Route::delete('/purchase/delete/{id}', 'destroy');
    });
});

Route::group(['middleware' => 'role:2'], function () {
    // Define your user-specific routes here
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('user.dashboard');
});
Route::fallback([NotFoundController::class, 'index']);
