<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\GovernmentController;

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
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgetPassword', [PasswordController::class, 'forgetPassword']);
Route::post('checkCode', [PasswordController::class, 'checkCode']);
Route::post('passwordNew', [PasswordController::class, 'passwordNew']);
Route::post('login_admin', [AdminAuthController::class, 'login_admin']);
Route::post('codeAdmin', [AdminAuthController::class, 'codeAdmin']);
Route::post('refreshToken', [AuthController::class, 'refreshToken']);
Route::get('test', [AuthController::class, 'test'])->middleware('jwt.verify');



Route::group(['middleware' => ['jwt.verify']], function () {
    Route::group(['middleware' => ['hasRole:admin']], function () {
        Route::group(['prefix' => 'flights'], function () {
            Route::post('store', [FlightController::class, 'store']);
            Route::Post('update/{id}', [FlightController::class, 'update']);
            Route::delete('delete/{id}', [FlightController::class, 'destroy']);
        });
    });

    Route::group(['middleware' => ['hasRole:admin|user']], function () {
        Route::get('getAllGovernments', [GovernmentController::class, 'index']);
        Route::Post('verification', [EmailController::class, 'verification']);
        Route::group(['prefix' => 'flights'], function () {
            Route::Post('getAll', [FlightController::class, 'index']);
            Route::get('show/{id}', [FlightController::class, 'show']);
        });
    });
});
