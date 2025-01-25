<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::group(['middleware' => ['localization']], function () {
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
        Route::post('test', [AuthController::class, 'test']);
        Route::post('resetPassword', [PasswordController::class, 'resetPassword']);
        Route::delete('deleteMyAccount', [AuthController::class, 'deleteMyAccount']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

});
