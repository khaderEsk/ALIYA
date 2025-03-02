<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\GovernmentController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\SuperAdminController;

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


    Route::Post('verification', [EmailController::class, 'verification']);
    Route::prefix('flights')->group(function () {
        Route::get('getAllGovernments', [GovernmentController::class, 'index']);
        Route::group(['middleware' => ['hasRole:admin|user']], function () {
            Route::Post('getAll', [FlightController::class, 'index']);
            Route::get('show/{id}', [FlightController::class, 'show']);
            Route::Post('reservation/{id}', [PassengerController::class, 'store']);
        });
        Route::middleware('hasRole:admin')->group(function () {
            Route::controller(FlightController::class)->group(function () {
                Route::post('store', 'store');
                Route::Post('update/{id}', 'update');
                Route::delete('delete/{id}', 'destroy');
                Route::get('getMyFlight', 'getMyFlight');
            });
            Route::get('passenger/{id}', [PassengerController::class, 'show']);
        });
    });

    Route::group(['middleware' => ['hasRole:superAdmin']], function () {
        Route::prefix('superAdmin')->group(function () {
            Route::Post('addCompany', [SuperAdminController::class, 'store']);
            Route::get('getAllCompany', [SuperAdminController::class, 'index']);
            Route::get('getAllUser', [SuperAdminController::class, 'getAllUser']);

            Route::get('block-user/{id}', [BlockController::class, 'store']);
            Route::delete('unblock-user/{id}', [BlockController::class, 'destroy']);
        });
    });
});
