<?php

use App\ErrorLog;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerchantController;



Route::get('/', function () {
    return view('welcome');
})->middleware(['auth.shopify'])->name('home');

Route::resource('merchants', 'MerchantController')->middleware(['auth.shopify']);
Route::resource('products', 'ProductsController');
Route::resource('rewards', 'RewardsController');
Route::get('/products/get/clubs/{id}', 'ProductsController@getClubsForProduct');
Route::post('/store/reward/points', 'MerchantController@storeRewardPoints');
Route::post('/cart/get/clubs', 'ProductsController@getClubsForCart');
Route::post('/cart/apply/points', 'ProductsController@cartApplyPoints')->name('cart.apply.points');
Route::post('/cart/apply/pin', 'ProductsController@cartApplyPin')->name('cart.apply.pin');
Route::get('/submit/order', 'ProductsController@submitOrder')->name('submit.order');
Route::get('/create/discount', 'ProductsController@createDiscount')->name('create.discount');
Route::get('/sync/products', 'ProductsController@storeProducts')->name('sync.products');
Route::get('/search', 'ProductsController@search');

Route::get('/order/place', 'ProductsController@orderPlace');

