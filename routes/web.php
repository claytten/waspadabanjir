<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Accounts\ProfileController;

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

        Route::namespace('Accounts')->group(function () {
            Route::get('/profile/{userId}/{role}', [ProfileController::class, 'editProfile'])->name('edit.profile');
            Route::put('/profile/{userId}/{role}/edit', [ProfileController::class, 'updateProfile'])->name('update.profile');
            Route::put('/profile/avatar/{userId}/{role}/edit', [ProfileController::class, 'updateProfileAvatar'])->name('update.profile.avatar');
            Route::put('/settings/{userId}/edit', [ProfileController::class, 'updateSetting'])->name('update.setting');
            Route::put('/settings/reset-password/{userId}/edit', [ProfileController::class, 'resetPassword'])->name('reset.password');
        });
    });
});

Route::get('/', function () {
    return view('welcome');
})->name('home');


