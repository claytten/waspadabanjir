<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Address\ProvinceController;
use App\Http\Controllers\Admin\Address\RegencyController;
use App\Http\Controllers\Admin\Address\DistrictController;
use App\Http\Controllers\Admin\Address\VillageController;
use App\Http\Controllers\Admin\Subscribers\SubscribeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routing Address from guest view
Route::get('/admin/address/provinces', [ProvinceController::class, 'index'])->name('api.provinces.index');
Route::get('/admin/address/regencies', [RegencyController::class, 'index'])->name('api.regencies.index');
Route::get('/admin/address/districts', [DistrictController::class, 'index'])->name('api.districts.index');
Route::get('/admin/address/villages', [VillageController::class, 'index'])->name('api.villages.index');

// Routing Callback Replies Whatsapp
Route::post('/listen-to-replies', [SubscribeController::class, 'listenToReplies'])->name('api.replies');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
