<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\FrontController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    //update user profile
    Route::put('/user-profile', [AuthController::class, 'updateProfile']);
});
Route::group([
    'middleware' => 'api',

], function ($router) {
    // wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

    // cart routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // order routes
    Route::get('/order', [OrderController::class, 'index']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::post('/order/{id}', [OrderController::class, 'complete']); // complete order
    Route::delete('/order/{id}', [OrderController::class, 'destroy']);

    // wallet
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');

    //paymob payment
    Route::get('/payment/pay', [PaymentController::class, 'payment'])->name('pay.payment');
    Route::Post('/payment/callback', [PaymentController::class, 'CallbackPayment'])->name('callback.payment');
    Route::get('/payment/callback', [PaymentController::class, 'GetCallbackPayment'])->name('get.callback.payment');
});
