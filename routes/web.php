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

    // Products
    Route::get('/products', 'DashboardController@products')->name('products');
    Route::post('/products', 'DashboardController@add_product')->name('add-products');
    Route::put('/products/{product}', 'DashboardController@update_product')->name('update-product');
    Route::delete('/products/{product}', 'DashboardController@delete_product')->name('delete-product');

    // Agents
    Route::get('/agents', 'DashboardController@agents')->name('agents');
    Route::post('/agents', 'DashboardController@add_agent')->name('add-agents');
    Route::put('/agents/{agent}', 'DashboardController@update_agent')->name('update-agent');
    Route::delete('/agents/{agent}', 'DashboardController@delete_agent')->name('delete-agent');

    // Admins
    Route::get('/admins', 'DashboardController@admins')->name('admins');
    Route::post('/admins', 'DashboardController@add_admin')->name('add-admins');
    Route::put('/admins/{admin}', 'DashboardController@update_admin')->name('update-admin');
    Route::delete('/admins/{admin}', 'DashboardController@delete_admin')->name('delete-admin');

    // Packages
    Route::get('/packages', 'DashboardController@packages')->name('packages');
    Route::post('/packages', 'DashboardController@add_package')->name('add-packages');
    Route::put('/packages/{package}', 'DashboardController@update_package')->name('update-package');
    Route::delete('/packages/{package}', 'DashboardController@delete_package')->name('delete-package');

    Route::get('/clients', 'DashboardController@clients')->name('clients');
    Route::get('/agents', 'DashboardController@agents')->name('agents');
    Route::get('/beneficiaries', 'DashboardController@beneficiaries')->name('beneficiaries');
    Route::get('/reports', 'DashboardController@reports')->name('reports');
  });

  Route::get('auth/logout', 'AuthController@logout')->name('auth.logout');
});