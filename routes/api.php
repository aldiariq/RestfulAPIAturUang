<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UtamaController;
use App\Http\Controllers\Api\Util\VerificationController;
use App\Http\Controllers\Api\Util\ResetPasswordController;

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

Route::post('/daftar', [AuthController::class, 'daftar']);
Route::post('/masuk', [AuthController::class, 'masuk']);

Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

Route::post('/kirimpermintaanresetpassword', [ResetPasswordController::class, 'kirimpermintaanresetpassword'])->name('password.email');
Route::get('/halamanresetpassword/{token}', [ResetPasswordController::class, 'halamanresetpassword'])->name('password.reset');
Route::post('/aksiresetpassword', [ResetPasswordController::class, 'aksiresetpassword'])->name('password.update');

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('/gantipassword', [AuthController::class, 'gantipassword']);
    Route::get('/profil', [AuthController::class, 'profil']);
    Route::get('/keluar', [AuthController::class, 'keluar']);

    Route::get('/lihatuang', [UtamaController::class, 'show']);
    Route::post('/pemasukanpengeluaran', [UtamaController::class, 'store']);
    Route::get('/resetcatatan', [UtamaController::class, 'destroy']);
});