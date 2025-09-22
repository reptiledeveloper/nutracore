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
    Route::match(['get', 'post'], '/save_address', 'HomeController@save_address');
    Route::match(['get', 'post'], '/brands', 'HomeController@brands');
    Route::match(['get', 'post'], '/store_location', 'HomeController@store_location');
    Route::match(['get', 'post'], '/explore', 'HomeController@explore');
    Route::match(['get', 'post'], '/stores', 'HomeController@stores');
    Route::match(['get', 'post'], '/nutrapass', 'HomeController@nutrapass')->name('nutrapass');
    Route::match(['get', 'post'], '/profile', 'HomeController@profile')->name('profile');
    Route::match(['get', 'post'], '/my_orders', 'HomeController@my_orders')->name('my_orders');
    Route::match(['get', 'post'], '/suppliment_recommendation', 'HomeController@suppliment_recommendation')->name('suppliment_recommendation');
    Route::match(['get', 'post'], '/suppliment_recommendation_list', 'HomeController@suppliment_recommendation_list')->name('suppliment_recommendation_list');
    Route::match(['get', 'post'], '/privacy_policy', 'HomeController@privacy_policy')->name('privacy_policy');
    Route::match(['get', 'post'], '/return_policy', 'HomeController@return_policy')->name('return_policy');
    Route::match(['get', 'post'], '/shipping_policy', 'HomeController@shipping_policy')->name('shipping_policy');
    Route::match(['get', 'post'], '/best_sellers', 'HomeController@best_sellers')->name('best_sellers');
    Route::match(['get', 'post'], '/best_deals', 'HomeController@best_deals')->name('best_deals');
    Route::match(['get', 'post'], '/new_arrivals', 'HomeController@new_arrivals')->name('new_arrivals');
    Route::match(['get', 'post'], '/order_details/{id}', 'HomeController@order_details')->name('order_details');
    Route::match(['get', 'post'], '/terms', 'HomeController@terms')->name('terms');
    Route::match(['get', 'post'], '/nc_cash', 'HomeController@nc_cash')->name('nc_cash');
    Route::match(['get', 'post'], '/giftcard', 'HomeController@giftcard')->name('giftcard');
    Route::match(['get', 'post'], '/refer_earn', 'HomeController@refer_earn')->name('refer_earn');
    Route::match(['get', 'post'], '/coupons', 'HomeController@coupons')->name('coupons');
    Route::match(['get', 'post'], '/transactions/{type}', 'HomeController@transactions')->name('transactions');
    Route::match(['get', 'post'], '/address', 'HomeController@address')->name('address');
    Route::match(['get', 'post'], '/categories', 'HomeController@categories');
    Route::match(['get', 'post'], '/update_selected_address', 'HomeController@update_selected_address');
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
