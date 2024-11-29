<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum', 'token.expiration')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum', 'token.expiration')->group(function () {
    Route::put('/user', [UserController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user/comments', [CommentController::class, 'show']);
    Route::post('/user/comments', [CommentController::class, 'store']);
    Route::put('/user/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/user/comments/{id}', [CommentController::class, 'destroy']);

    Route::get('/comments/{id}/history', [CommentController::class, 'history']);
});

Route::middleware('auth:sanctum', 'admin', 'token.expiration')->prefix('admin')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
});

Route::get('/comments', [CommentController::class, 'index']);

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link resent.']);
})->middleware(['auth:sanctum'])->name('verification.send');



