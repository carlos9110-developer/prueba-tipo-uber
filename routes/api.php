<?php

use App\Http\Controllers\Mobile\MobileController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\User\UserController;
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

Route::group(['middleware' => ['validate.token']], function () {
    
    Route::prefix('services')->group(
        function () {
            Route::post('get', [ServiceController::class, 'get']);
            Route::post('save', [ServiceController::class, 'save']);
            Route::post('availableServicesByDriver', [ServiceController::class, 'availableServicesByDriver']);
            Route::post('assignDriverToService', [ServiceController::class, 'assignDriverToService']);
            Route::post('getLocationDriverService', [ServiceController::class, 'getLocationDriverService']);
            Route::post('cancelService', [ServiceController::class, 'cancelService']);
            Route::post('endService', [ServiceController::class, 'endService']);
        }
    );

    Route::prefix('mobiles')->group(
        function () {
            Route::post('saveLocation', [MobileController::class, 'saveLocation']);
        }
    );

    Route::prefix('users')->group(
        function () {
            Route::post('logout', [UserController::class, 'logout']);
            Route::post('saveUserMobil', [UserController::class, 'saveUserMobil']);
        }
    );

});

Route::prefix('users')->group(
    function () {
        Route::post('save', [UserController::class, 'store']);
        Route::post('login', [UserController::class, 'login']);
    }
);


