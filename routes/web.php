<?php

use Illuminate\Support\Facades\Route;

Route::name('auth.')->group(function () {
    Route::get('login', 'AuthController@getLoginForm')->name('getLoginForm');
    Route::post('login', 'AuthController@login')->name('doLogin');
});

Route::middleware(['auth'])->group(function() {
  Route::name('dashboard.')->group(function () {
    Route::get('/', 'DashboardController@index')->name('index');
    Route::get('/orders', 'DashboardController@orders')->name('orders');

    Route::get('/products', 'DashboardController@products')->name('products');
    Route::post('/products', 'DashboardController@add_product')->name('add-products');
    Route::put('/products', 'DashboardController@edit_products')->name('edit-products');
    Route::delete('/products/{product}', 'DashboardController@delete_products')->name('delete-products');

    Route::get('/packages', 'DashboardController@packages')->name('packages');
    Route::get('/clients', 'DashboardController@clients')->name('clients');
    Route::get('/beneficiaries', 'DashboardController@beneficiaries')->name('beneficiaries');
    Route::get('/reports', 'DashboardController@reports')->name('reports');
  });

  Route::get('auth/logout', 'AuthController@logout')->name('auth.logout');
});