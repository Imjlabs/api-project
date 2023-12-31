<?php

use Illuminate\Http\Request;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\InvoiceController;

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


Route::prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::get('/users/{user}/files', [AdminController::class, 'viewUserFiles']);
    Route::get('/users/files', [AdminController::class, 'viewAllUserFiles']);
    Route::get('/total-uploaded-files-count', [AdminController::class, 'totalUploadedFilesCount']);
    Route::get('/uploaded-files-today-count', [AdminController::class, 'uploadedFilesTodayCount']);
    Route::get('/files-per-client', [AdminController::class, 'filesPerClient']);
    Route::get('/users/files/{fileId}', [AdminController::class, 'getFile']);
    Route::get('/admin/users/{userId}/storage/size', [AdminController::class, 'getUserStorageSize']);
});

Route::middleware('auth:sanctum')->group(function () {
Route::middleware(['verified'])->group(function () {

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/{user}', [UserController::class, 'show']);
            Route::put('/users/{user}', [UserController::class, 'update']);
            Route::delete('/users/{user}', [UserController::class, 'destroy']);
            Route::get('/users/storage/size', [UserController::class, 'getUserStorageSize']);
            // Route pour afficher toutes les factures pour l'utilisateur connecté
            Route::get('/invoices', [InvoiceController::class, 'index'])->middleware('auth:api'); // Middleware d'authentification API
            // Route pour afficher une facture spécifique par ID pour l'utilisateur connecté
            Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->middleware('auth:api');
            Route::get('invoices/user/{invoice_id}',[InvoiceController::class, 'downloadInvoice']);
        });

        Route::post('/files/upload', [FileController::class, 'uploadFile']);
        Route::get('/files', [FileController::class, 'listFiles']);
        Route::get('/files/{fileId}', [FileController::class, 'getFile']);
        Route::delete('/files/{fileId}', [FileController::class, 'deleteFile']);
        Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
        Route::post('/confirm-payment', [PaymentController::class, 'confirmPayment']);
        Route::get('user/invoices', [InvoiceController::class, 'getUserInvoices']);
        Route::get('user/invoice/{invoiceId}', [InvoiceController::class, 'getInvoice']);
        Route::post('add-storage-space', [UserController::class, 'addStorageSpace']);


    });
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);

Route::middleware('auth:sanctum')->get('/user/email-verification-date', function () {
    return response()->json([auth()->user()->email_verified_at]);
});
