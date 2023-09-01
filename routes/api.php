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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/users', UserController::class);
    Route::post('/upload-file', [FileController::class, 'uploadFile']);
    Route::delete('/delete-file/{fileId}', [FileController::class, 'deleteFile']);
    Route::get('/list-files', [FileController::class, 'listFiles']);
});



Route::prefix('admin')->middleware(['auth:sanctum', 'App\Http\Middleware\IsAdmin'])->group(function () {
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::get('/users/{user}/files', [AdminController::class, 'viewUserFiles']);
});


Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
