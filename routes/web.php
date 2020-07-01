<?php

use Illuminate\Support\Facades\Route;

Route::name('auth.') ->group(function () {
    Route::get('login', 'AuthController@getLoginForm')->name('getLoginForm');
    Route::post('login', 'AuthController@login')->name('doLogin');
});

Route::middleware(['auth'])->group(function() {
  Route::get('/', 'DashboardController@index')->name('dashboard');

  Route::get('auth/logout', 'AuthController@logout')->name('auth.logout');
});