<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::group(['namespace' => 'App\Http\Controllers'], function () {

    Route::match(['get', 'post'], '/', 'HomeController@index');
    Route::match(['get', 'post'], '/products', 'HomeController@products');
    Route::match(['get', 'post'], '/product-details', 'HomeController@product_details');
    Route::match(['get', 'post'], '/cart', 'HomeController@cart');
    Route::match(['get', 'post'], '/stores', 'HomeController@stores');
    Route::match(['get', 'post'], '/nutrapass', 'HomeController@nutrapass');
    Route::match(['get', 'post'], '/profile', 'HomeController@profile');
    Route::match(['get', 'post'], '/categories', 'HomeController@categories');
    Route::match(['get', 'post'], '/about', 'HomeController@about');
    Route::match(['get', 'post'], '/wishlist', 'HomeController@wishlist');
    Route::match(['get', 'post'], '/contact', 'HomeController@contact');
    Route::match(['get', 'post'], '/deals', 'HomeController@deals');
    Route::match(['get', 'post'], '/collections/{slug}', 'HomeController@products');
    Route::match(['get', 'post'], '/products/{slug}', 'HomeController@product_details');
    Route::match(['get', 'post'], '/getEstimateDelivery', 'HomeController@getEstimateDelivery');
    Route::match(['get', 'post'], '/sendOTP', 'HomeController@sendOTP');
    Route::match(['get', 'post'], '/login', 'HomeController@login');
    Route::match(['get', 'post'], '/logout', 'HomeController@logout');
    Route::match(['get', 'post'], '/addToCart', 'HomeController@addToCart');
    Route::match(['get', 'post'], '/getCartQty', 'HomeController@getCartQty');
    Route::match(['get', 'post'], '/getCartHtml', 'HomeController@getCartHtml');
    Route::match(['get', 'post'], '/createRazorpayOrder', 'HomeController@createRazorpayOrder');


});