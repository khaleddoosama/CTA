<?php

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SliderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


// group middleware
Route::middleware(['auth'])->group(function () {
    Route::resource('category', CategoryController::class)->except('show');
    Route::resource('product', ProductController::class)->except('show');
    Route::get('product/status/{product}', [ProductController::class, 'changeStatus'])->name('product.status');
    Route::resource('slider', SliderController::class)->except('show');
    Route::get('slider/status/{slider}', [SliderController::class, 'changeStatus'])->name('slider.status');
});

//PAYMENTS
// verify payment
Route::get('/payments/verify/{payment?}', [PaymentController::class, 'payment_verify'])->name('payment-verify');

