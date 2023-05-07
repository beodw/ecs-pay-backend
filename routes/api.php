<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\CurrenciesController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [PassportAuthController::class, 'register']);

Route::post('login', [PassportAuthController::class, 'login']);

Route::post('verifyOtp', [PassportAuthController::class, 'verifyOtp']);

Route::post('generateOtp', [PassportAuthController::class, 'generateOtp']);

Route::middleware('auth:api')->group(function () {
    Route::resources([
        'orders' => OrdersController::class
    ]);

    Route::resources([
        'currency' => CurrenciesController::class
    ]);

    Route::resources([
        'transactions' => TransactionsController::class
    ]);

    Route::resources([
        'user-profile' => UserProfileController::class
    ]);
});




