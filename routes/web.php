<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'login');
    Route::post('login_post', 'login_post');
    Route::get('logout', 'logout');
});
Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
    // Define your admin routes here
    Route::get('dashboard', [DashboardController::class, 'dashboard']);
});
// user
Route::group(['middleware' => 'user'], function () {
    // Define your admin routes here
    Route::get('dashboard', [DashboardController::class, 'dashboard']);
});
