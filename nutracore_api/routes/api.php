<?php

use App\Http\Controllers\V1\ApiController;
use App\Http\Controllers\V1\PaymentController;
use App\Http\Controllers\V1\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::match(['get', 'post'], '/update_payment_status', [ApiController::class, 'update_payment_status'])->name('update_payment_status');

Route::prefix('v1/')->group(function () {
    Route::match(['get', 'post'], '/update_payment_status', [ApiController::class, 'update_payment_status'])->name('update_payment_status');

    //////10/10/2024
    Route::post('/splash_screens', [ApiController::class, 'splash_screens']);
    Route::post('/send_test_notitication', [ApiController::class, 'send_test_notitication']);
    Route::get('/contact_us', [ApiController::class, 'contact_us']);
    Route::get('/app_version', [ApiController::class, 'app_version']);
    Route::match(['get', 'post'], '/delete_account', [ApiController::class, 'delete_account']);
    Route::match(['get', 'post'], '/send_otp', [ApiController::class, 'send_otp']);
    Route::match(['get', 'post'], '/verify_otp', [ApiController::class, 'verify_otp']);
    Route::get('/state_list', [ApiController::class, 'state_list']);
    Route::post('/city_list', [ApiController::class, 'city_list']);
    Route::match(['get', 'post'], '/login', [ApiController::class, 'login'])->name('login');
    Route::match(['get', 'post'], '/skip_login', [ApiController::class, 'skip_login'])->name('skip_login');
    Route::post('/settings', [ApiController::class, 'settings']);



    Route::match(['get', 'post'], '/send_test_notification', [ApiController::class, 'send_test_notification']);
    Route::match(['get', 'post'], '/check_referal_code', [ApiController::class, 'check_referal_code']);

    ///////////////////////////////////////////////////////////////////

    Route::post('/payment/create-order', [PaymentController::class, 'createOrder']);
    Route::get('/payment/order-status/{orderId}', [PaymentController::class, 'getOrderStatus']);


    Route::match(['get', 'post'], '/search_product', [ApiController::class, 'search_product']);




    Route::middleware(['auth:api'])->group(function () {
//

        Route::match(['get', 'post'], '/sellers_list', [ApiController::class, 'sellers_list'])->name('sellers_list');
        Route::post('/faqs', [ApiController::class, 'faqs']);


        Route::match(['get', 'post'], '/profile', [ApiController::class, 'profile']);
        Route::match(['get', 'post'], '/home', [ApiController::class, 'home']);
        Route::post('/logout', [ApiController::class, 'logout']);
        Route::post('/update_profile', [ApiController::class, 'update_profile']);
        Route::post('/update_cart', [ApiController::class, 'update_cart']);
        Route::post('/cart_list', [ApiController::class, 'cart_list']);
        Route::post('/user_address', [ApiController::class, 'user_address']);
        Route::post('/update_user_address', [ApiController::class, 'update_user_address']);
        Route::post('/notification_list', [ApiController::class, 'notification_list']);
        Route::post('/wishlist', [ApiController::class, 'wishlist']);
        Route::get('/wishlist', [ApiController::class, 'wishlist']);
        Route::post('/add_wallet', [ApiController::class, 'add_wallet']);
        Route::post('/featured_products', [ApiController::class, 'featured_products']);
        Route::post('/place_order', [ApiController::class, 'place_order']);
        Route::post('/my_orders', [ApiController::class, 'my_orders']);
        Route::post('/my_orders_details', [ApiController::class, 'my_orders_details']);
        Route::post('/get_slots', [ApiController::class, 'get_slots']);
        Route::post('/cancel_order', [ApiController::class, 'cancel_order']);
        Route::post('/re_order', [ApiController::class, 're_order']);
        Route::post('/order_ratings', [ApiController::class, 'order_ratings']);
        Route::post('/wallet_offers', [ApiController::class, 'wallet_offers']);
        Route::post('/offers', [ApiController::class, 'offers']);
        Route::match(['get', 'post'], '/invoice', [ApiController::class, 'invoice']);
        Route::match(['get', 'post'], '/cartdata', [ApiController::class, 'CartData']);
        Route::match(['get', 'post'], '/referal_user_list', [ApiController::class, 'referal_user_list']);
        Route::post('/category_list', [ApiController::class, 'category_list']);
        Route::post('/subcategory_list', [ApiController::class, 'subcategory_list']);
        Route::post('/brands', [ApiController::class, 'brands']);
        Route::match(['get','post'],'/products', [ApiController::class, 'products']);
        Route::post('/product_details', [ApiController::class, 'product_details']);
        Route::post('/app_versions', [ApiController::class, 'app_versions']);
        Route::post('/tips_list', [ApiController::class, 'tips_list']);
        Route::post('/settings', [ApiController::class, 'settings']);
        Route::post('/search_location', [ApiController::class, 'search_location']);
        Route::post('/fetch_latlong', [ApiController::class, 'fetch_latlong']);
        Route::post('/app_filters', [ApiController::class, 'app_filters']);
        ////////////////////////

        Route::match(['get', 'post'], '/notification_list', [ApiController::class, 'notification_list']);
        Route::match(['get', 'post'], '/delete_notification', [ApiController::class, 'delete_notification']);

        Route::post('/transactions', [ApiController::class, 'transactions']);
        Route::post('/delete_user_address', [ApiController::class, 'delete_user_address']);
        Route::post('/create_ticket', [ApiController::class, 'create_ticket']);
        Route::post('/tickets_list', [ApiController::class, 'tickets_list']);
        Route::post('/chat_list', [ApiController::class, 'chat_list']);
        Route::post('/submit_chat', [ApiController::class, 'submit_chat']);
        Route::post('/giftcard', [ApiController::class, 'giftcard']);
        Route::post('/buy_giftcard', [ApiController::class, 'buy_giftcard']);



        ////////Subscription Part//////////////////
        Route::post('/subscriptions', [SubscriptionController::class, 'subscriptions']);
        Route::post('/loyality_points', [SubscriptionController::class, 'loyality_points']);
        Route::post('/take_subscription', [SubscriptionController::class, 'take_subscription']);
        Route::post('/subscription_products', [SubscriptionController::class, 'subscription_products']);
        Route::post('/getCalenderData', [SubscriptionController::class, 'getCalenderData']);
        Route::post('/update_cart_subscription', [SubscriptionController::class, 'update_cart_subscription']);
        Route::post('/subscription_cart_list', [SubscriptionController::class, 'subscription_cart_list']);
        Route::post('/place_subscription_order', [SubscriptionController::class, 'place_subscription_order']);
        Route::post('/my_subscription_products', [SubscriptionController::class, 'my_subscription_products']);
        Route::post('/my_subscription_product_details', [SubscriptionController::class, 'my_subscription_product_details']);
        Route::post('/cancel_single_date', [SubscriptionController::class, 'cancel_single_date']);
        Route::post('/cancel_subscription', [SubscriptionController::class, 'cancel_subscription']);
        Route::post('/my_subscription_order_details', [SubscriptionController::class, 'my_subscription_order_details']);
        Route::post('/check_delivery', [ApiController::class, 'check_delivery']);
        Route::post('/supplement', [ApiController::class, 'supplement']);
        Route::post('/return_single_product', [ApiController::class, 'return_single_product']);
        Route::post('/return_order', [ApiController::class, 'return_order']);
    });


});
