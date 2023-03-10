<?php

use Arhamlabs\Authentication\Http\Controllers\AuthLoginALController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['web']], function () {

    Route::get('/email/verification/{token}', [AuthLoginALController::class, 'webEmailVerification'])->name('webEmailVerification');

    Route::get('/verified', function () {
        return view('mails.user-email-verified');
    })->name('verified');

    Route::get('/reset-password/{token}', [AuthLoginALController::class, 'webResetPassword'])->name('webResetPassword');
    
    Route::post('/reset/password-change', [AuthLoginALController::class, 'webUpdatePassword']);
});
