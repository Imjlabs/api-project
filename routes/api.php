<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;

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


Route::prefix('admin')->middleware(['auth:sanctum', 'App\Http\Middleware\IsAdmin'])->group(function () {
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::get('/users/{user}/files', [AdminController::class, 'viewUserFiles']);
    Route::get('/users/files', [AdminController::class, 'viewAllUserFiles']);


});

Route::middleware(['verified'])->group(function () {
    Route::middleware('auth:sanctum')->group(function () {

        Route::middleware(['auth:sanctum'])->group(function () {

            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/{user}', [UserController::class, 'show']);
            Route::put('/users/{user}', [UserController::class, 'update']);
            Route::delete('/users/{user}', [UserController::class, 'destroy']);
        });

        Route::post('/upload-file', [FileController::class, 'uploadFile']);
        Route::delete('/delete-file/{fileId}', [FileController::class, 'deleteFile']);
        Route::get('/list-files', [FileController::class, 'listFiles']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);

Route::middleware('auth:sanctum')->get('/user/email-verification-date', function () {
    return auth()->user()->email_verified_at;
});
