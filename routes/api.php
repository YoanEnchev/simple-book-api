<?php

use Illuminate\Http\Request;


Route::group(['middleware' => 'api.auth'], function () {
    
    // User is authenticated.
    Route::apiResource('books', 'BookController')->only('index', 'show');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group(['middleware' => 'api.author'], function () {
        
        // User is author.
        Route::apiResource('books', 'BookController')->only('store');
        
        Route::group(['middleware' => 'api.book-author'], function () {
            
            // Book is published by the author.
            Route::apiResource('books', 'BookController')->only('update', 'destroy');
            Route::patch('/books/{book}/update-cover', 'BookController@updateCover');
        });
    });
});

Route::post('register' ,'Auth\ApiAuthController@register')->name('api.register');
Route::post('login' ,'Auth\ApiAuthController@login')->name('api.login');