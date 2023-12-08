<?php

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

Route::name('app.')->prefix('/')->group(function () {
    Route::name('blade')->get('/', function() {
        abort(500);
        //return view('blade-components');
    });
    Route::name('label')->get('/label', function() {
        return view('label');
    });
    Route::name('logo')->get('/logo', function() {
        return view('logo');
    });
    Route::name('path')->get('/path', function() {
        return view('path');
    });
    Route::name('colors')->get('/colors', function() {
        return view('colors');
    });
    Route::name('gradient')->get('/gradient', function() {
        return view('gradient');
    });
});
