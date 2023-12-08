<?php

use Illuminate\Support\Facades\Route;
use Da\QrCode\Controllers\LaravelResourceController;

Route::prefix('da-qrcode')->name('da-qrcode.')->group(function () {
    Route::get('/build', LaravelResourceController::class)->name('build');
});
