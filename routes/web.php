<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Admin / Employee routes
 */
Route::namespace('Admin')->group(function () {
    Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login.view');
    Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login');
    Route::get('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth'], 'as' => 'admin.' ], function () {
    Route::namespace('Admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
});

Route::get('/', function () {
    return view('welcome');
})->name('home');


