<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

//rotas usuario não autenticado
Route::post('user', 'App\Http\Controllers\User@insert');
Route::get('config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    // return what you want
});

Route::group(['middleware' => ['api']], function() {
    Route::post('session', 'App\Http\Controllers\Session@login');
    Route::delete('session', 'App\Http\Controllers\Session@logout');

    Route::group(['middleware' => ['aut']], function() {
        Route::post('avatar', 'App\Http\Controllers\User@insertAvatar');
    });
});