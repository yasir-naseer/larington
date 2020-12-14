<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
})->middleware(['auth.shopify'])->name('home');

Route::resource('merchants', 'MerchantController')->middleware(['auth.shopify']);
Route::resource('products', 'ProductsController')->middleware(['auth.shopify']);
Route::resource('rewards', 'RewardsController')->middleware(['auth.shopify']);
Route::get('/products/get/clubs/{id}', 'ProductsController@getClubsForProduct');