<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\CurrenciesController;
use App\Http\Controllers\WaitingListController;



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

Route::post('waiting-list-registration', [WaitingListController::class, 'store']);

Route::get('signUpCount', [WaitingListController::class, 'getSignUpCount']);

Route::post('register', [PassportAuthController::class, 'register']);

Route::post('login', [PassportAuthController::class, 'login']);

Route::post('verifyOtp', [PassportAuthController::class, 'verifyOtp']);

Route::post('generateOtp', [PassportAuthController::class, 'generateOtp']);

Route::middleware('auth:api')->group(function () {

    Route::resources([

        'user-profiles' => UserProfileController::class,

        'platforms' => PlatformController::class,

        'transactions' => TransactionsController::class,

        'currencies' => CurrenciesController::class,

        'orders' => OrdersController::class
    ]);

    Route::get('orders/search', [OrdersController::class, 'search']);

});




