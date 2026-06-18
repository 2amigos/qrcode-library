<?php

use Illuminate\Support\Facades\Route;
use Da\QrCode\Bridge\Laravel\ResourceController;

Route::prefix('da-qrcode')->name('da-qrcode.')->group(function () {
    Route::get('/build', ResourceController::class)->name('build');
});
