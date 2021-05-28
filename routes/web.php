<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Accounts\ProfileController;
use App\Http\Controllers\Admin\Accounts\UserController;
use App\Http\Controllers\Admin\Accounts\RoleController;
use App\Http\Controllers\Admin\Address\ProvinceController;
use App\Http\Controllers\Admin\Address\RegencyController;
use App\Http\Controllers\Admin\Address\DistrictController;
use App\Http\Controllers\Admin\Address\VillageController;
use App\Http\Controllers\Admin\Maps\MapController;
use App\Http\Controllers\Admin\Reports\ReportController;
use App\Http\Controllers\Admin\Subscribers\SubscribeController;
use App\Http\Controllers\Front\LandingController;

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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Account Routing
    Route::resource('/account/admin', UserController::class, ['except' => ['update']]);
    Route::put('/account/admin/{id}/{role}/edit', [UserController::class, 'update'])->name('admin.update');
    Route::resource('/account/role', RoleController::class);

    // Profile Routing
    Route::get('/profile/{userId}/{role}', [ProfileController::class, 'editProfile'])->name('edit.profile');
    Route::put('/profile/{userId}/{role}/edit', [ProfileController::class, 'updateProfile'])->name('update.profile');
    Route::put('/profile/address/{userId}/{role}/edit', [ProfileController::class, 'updateProfileAddress'])->name('update.address.profile');
    Route::put('/profile/avatar/{userId}/{role}/edit', [ProfileController::class, 'updateProfileAvatar'])->name('update.profile.avatar');
    Route::put('/settings/{userId}/edit', [ProfileController::class, 'updateSetting'])->name('update.setting');
    Route::put('/settings/reset-password/{userId}/edit', [ProfileController::class, 'resetPassword'])->name('reset.password');

    // Addressing
    Route::resource('/address/provinces', ProvinceController::class, ['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('/address/regencies', RegencyController::class, ['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('/address/districts', DistrictController::class, ['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('/address/villages', VillageController::class, ['only' => ['index', 'store', 'update', 'destroy', 'show']]);

    // Maps
    Route::get('/maps/view', [MapController::class, 'indexView'])->name('map.view');
    Route::delete('/maps/image/{id}/destroy', [MapController::class, 'destroyImage'])->name('maps.image.destroy');
    Route::resource('/maps', MapController::class, ['except' => ['create']]);

    //Subscribers
    Route::resource('/subscribers', SubscribeController::class, ['except' => ['show', 'create']]);
    Route::post('/subscribers/personal', [SubscribeController::class, 'personalBroadcast'])->name('subscribers.personal');
    Route::post('/subscribers/multiple', [SubscribeController::class, 'multipleBroadcast'])->name('subscribers.multiple');
    Route::get('/subscribers/regency', [SubscribeController::class, 'getRegency'])->name('subscribers.getRegency');

    //Reports
    Route::resource('/reports', ReportController::class);
});


/**
 * User routes
 */
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::post('/subscribe/store', [LandingController::class, 'store'])->name('home.store');
Route::get('/maps/{id}/show', [LandingController::class, 'show'])->name('maps.show');
Route::get('/report', [LandingController::class, 'formReport'])->name('form.report');
Route::post('/report', [LandingController::class, 'storeReport'])->name('form.report.store');


