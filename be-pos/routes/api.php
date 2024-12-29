<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\UserRoleController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\BusinessDataController;
use App\Http\Controllers\API\ForgotPasswordController;

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

Route::prefix('v1')->group(function () {

    // Register - Login
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/me', [AuthController::class, 'getUser'])->middleware('auth:api');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

        // Generate OTP Code - Verification Email
        Route::post('/generate-otp-code', [AuthController::class, 'generateOtpCode']);
        Route::post('/verification-email', [AuthController::class, 'verificationEmail'])->middleware('auth:api');

        // Generate OTP Code - Verification Login
        Route::prefix('login')->group(function () {
            Route::post('/', [AuthController::class, 'login']);
            Route::post('/cashier', [AuthController::class, 'loginCashier']);
            Route::post('/generate-otp-code', [AuthController::class, 'generateOtpCodeLogin'])->middleware('auth:api');
            Route::post('/verification-login', [AuthController::class, 'verificationLogin'])->middleware('auth:api');
        });
    });

    // Business
    Route::middleware(['auth:api', 'isOwner'])->prefix('business')->group(function () {
        Route::post('/additionalData', [BusinessDataController::class, 'storeBusinessInfo']);
    });

    // Forgot Password
    Route::prefix('forgot-password')->group(function () {
        Route::post('/send-email', [ForgotPasswordController::class, 'forgotPassword']);
        Route::post('/verify-otp-code', [ForgotPasswordController::class, 'verifyOtpForgotPassword']);
        Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->middleware('verifyPasswordResetToken');
    });

    // Role
    Route::middleware(['auth:api', 'isOwner'])->prefix('userRole')->group(function () {
        Route::get('/', [UserRoleController::class, 'index']);
        Route::post('/', [UserRoleController::class, 'store']);
        Route::get('/{id}', [UserRoleController::class, 'show']);
        Route::put('/{id}', [UserRoleController::class, 'update']);
        Route::delete('/', [UserRoleController::class, 'bulkDestroy']);
    });

    // Profile
    Route::middleware('auth:api')->prefix('profile')->group(function () {
        Route::post('/', [ProfileController::class, 'updateOrCreate']);
        Route::get('/', [ProfileController::class, 'getProfile']);
    });

    // Product
    Route::middleware('auth:api')->prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::put('/reduce-quantity/{id}', [ProductController::class, 'reduceQuantity']);
        Route::delete('/', [ProductController::class, 'bulkDestroy']);
    });

    // Transaction
    Route::middleware('auth:api')->prefix('transaction')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::post('/pay', [TransactionController::class, 'pay']);
        Route::post('/save', [TransactionController::class, 'save']);
    });

    // Customer
    Route::middleware(['auth:api'])->prefix('customer')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
    });
});
