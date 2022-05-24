<?php

use Illuminate\Http\Request;


Route::group(['middleware' => 'api.auth'], function () {
    Route::apiResource('books', 'BookController')->only('index', 'show');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group(['middleware' => 'api.author'], function () {
        Route::apiResource('books', 'BookController')->only('store', 'update', 'destroy');
    });
});

Route::post('register' ,'Auth\ApiAuthController@register')->name('api.register');
Route::post('login' ,'Auth\ApiAuthController@login')->name('api.login');