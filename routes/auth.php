<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Indonesian Auth URLs
    Route::get('masuk', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('masuk', [AuthenticatedSessionController::class, 'store']);

    // Fallback URL for login
    Route::get('login', function () {
        return redirect()->route('login');
    });
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('lupa-kata-sandi', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('lupa-kata-sandi', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('atur-ulang-sandi/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('atur-ulang-sandi', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware(['auth:admin,guru,siswa,web'])->group(function () {
    Route::get('verifikasi-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verifikasi-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/notifikasi-verifikasi', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('konfirmasi-kata-sandi', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('konfirmasi-kata-sandi', [ConfirmablePasswordController::class, 'store']);

    Route::put('kata-sandi', [PasswordController::class, 'update'])->name('password.update');

    // Indonesian Logout URL
    Route::post('keluar', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Fallback logout URL
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
});
