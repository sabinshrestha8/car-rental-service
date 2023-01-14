<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ReviewController;
use GuzzleHttp\Middleware;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('signup', 'signup')->name('auth.signup');
    Route::post('login', 'login')->name('auth.login');
    Route::post('logout', 'logout')->middleware('auth:sanctum')->name('auth.logout');
    Route::get('user', 'getAuthenticatedUser')->middleware('auth:sanctum')->name('auth.user');
    Route::put('user/update', 'updateProfile')->middleware('auth:sanctum');
    Route::post('/password/email', 'sendPasswordResetLinkEmail')->middleware('throttle:5,1')->name('password.email');
    Route::post('/password/reset', 'resetPassword')->name('password.reset');
});

Route::apiResource('/cars', CarController::class)->middleware('auth:sanctum');

Route::get('/search', [CarController::class, 'searchCar']);

Route::controller(BookingController::class)->prefix('bookings')->middleware('auth:sanctum')->group(function () {
    Route::post('/', 'bookCar');

    Route::post('/{id}', 'cancelBooking');

    Route::put('/{id}', 'updateBooking');

    Route::get('/', 'showBookings');
});

Route::group(['prefix'=>'cars', 'middleware' => ['auth:sanctum']], function () {
    Route::apiResource('/{car}/reviews', ReviewController::class);
});
