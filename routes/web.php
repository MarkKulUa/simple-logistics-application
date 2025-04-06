<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ContactControllers\GoogleContactController;

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

Route::middleware('web')->group(function () {
    Route::get('/refresh-google-token', function () {
        Artisan::call('google:refresh-token');
        return 'Token refreshed successfully';
    });

    Route::get('/export-for-google-contacts', [GoogleContactController::class, 'exportToCsv']);

    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});
