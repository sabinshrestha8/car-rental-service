<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CarController;

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

    Route::post('/password/email', 'sendPasswordResetLinkEmail')->middleware('throttle:5,1')->name('password.email');
    Route::post('/password/reset', 'resetPassword')->name('password.reset');
});

Route::apiResource('/cars', CarController::class);
