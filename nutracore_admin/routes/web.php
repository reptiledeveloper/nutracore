<?php

use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

/**
 * @return void
 */

Route::group(['namespace' => 'App\Http\Controllers'], function () {

    Route::match(['get', 'post'], '/', 'LoginController@index');
    Route::match(['get', 'post'],'/login_submit', 'LoginController@index')->name('admin.login_submit');
    $ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    Route::match(['get', 'post'], '/google_auth', 'GoogleController@google_auth')->name('admin.google_auth');
    Route::match(['get', 'post'], '/googlecallback', 'GoogleController@googlecallback')->name('admin.googlecallback');
    Route::match(['get', 'post'], '/sendFCM', 'GoogleController@sendFCM')->name('admin.sendFCM');

    Route::group(['prefix' => $ADMIN_ROUTE_NAME, 'middleware' => ['authadmin']], function () {
        Route::match(['get', 'post'], '/', 'HomeController@index')->name('home');
        Route::match(['get', 'post'], '/profile', 'HomeController@profile')->name('profile');
        Route::match(['get', 'post'], '/logout', 'LoginController@logout')->name('admin.logout');
        Route::match(['get', 'post'], '/get_branches', 'HomeController@get_branches')->name('admin.get_branches');
        Route::match(['get', 'post'], '/save_tab', 'HomeController@save_tab')->name('admin.save_tab');
        Route::match(['get', 'post'], '/profile', 'HomeController@profile')->name('admin.profile');
        Route::match(['get', 'post'], '/change_password', 'HomeController@change_password')->name('admin.change_password');
        Route::match(['get', 'post'], '/store_token', 'HomeController@store_token')->name('admin.store_token');
        Route::match(['get', 'post'], '/get_state', 'HomeController@get_state')->name('admin.get_state');
        Route::match(['get', 'post'], '/get_city', 'HomeController@get_city')->name('admin.get_city');
        Route::match(['get', 'post'], '/update_status', 'HomeController@update_status')->name('admin.update_status');
        Route::match(['get', 'post'], 'uploadScreenshot', 'QRCodesController@uploadScreenshot')->name('uploadScreenshot');
        Route::match(['get', 'post'], 'settings', 'HomeController@settings')->name('admin.settings');
        Route::match(['get', 'post'], '/get_sub_category', 'HomeController@get_sub_category')->name('admin.get_sub_category');
        Route::match(['get', 'post'], '/get_tags', 'HomeController@get_tags')->name('admin.get_tags');
        Route::match(['get', 'post'], '/delete_image', 'HomeController@delete_image')->name('admin.delete_image');
        Route::match(['get', 'post'], '/search_image', 'HomeController@search_image')->name('admin.search_image');

////banners
        Route::group(['prefix' => 'banners', 'as' => 'banners', 'middleware' => ['allowedmodule:banners,list']], function () {
            Route::get('/', 'BannerController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'BannerController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'BannerController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'BannerController@delete')->name('.delete');
        });
////new_updates
        Route::group(['prefix' => 'new_updates', 'as' => 'new_updates', 'middleware' => ['allowedmodule:new_updates,list']], function () {
            Route::get('/', 'NewUpdateController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'NewUpdateController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'NewUpdateController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'NewUpdateController@delete')->name('.delete');
        });
////testimonial
        Route::group(['prefix' => 'testimonial', 'as' => 'testimonial', 'middleware' => ['allowedmodule:testimonial,list']], function () {
            Route::get('/', 'TestimonialController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'TestimonialController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'TestimonialController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'TestimonialController@delete')->name('.delete');
        });
////collections
        Route::group(['prefix' => 'collections', 'as' => 'collections', 'middleware' => ['allowedmodule:collections,list']], function () {
            Route::get('/', 'CollectionsController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'CollectionsController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'CollectionsController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'CollectionsController@delete')->name('.delete');
        });


        ////reports
        Route::group(['prefix' => 'reports', 'as' => 'reports', 'middleware' => ['allowedmodule:reports,list']], function () {
            Route::get('/delivery_agent', 'ExportController@delivery_agent')->name('.delivery_agent');
            Route::get('/sellers', 'ExportController@sellers')->name('.sellers');
            Route::get('/categories', 'ExportController@categories')->name('.categories');
            Route::get('/subcategories', 'ExportController@subcategories')->name('.subcategories');
            Route::get('/users', 'ExportController@users')->name('.users');

        });

        ////app_settings
        Route::group(['prefix' => 'app_settings', 'as' => 'app_settings', 'middleware' => ['allowedmodule:app_settings,list']], function () {
            Route::match(['get', 'post'], '/', 'AppSettingController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'AppSettingController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'AppSettingController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'AppSettingController@delete')->name('.delete');
        });
////pincode
        Route::group(['prefix' => 'pincode', 'as' => 'pincode', 'middleware' => ['allowedmodule:pincode,list']], function () {
            Route::get('/', 'PincodeController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'PincodeController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'PincodeController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'PincodeController@delete')->name('.delete');
        });
//        cities
        Route::group(['prefix' => 'cities', 'as' => 'cities', 'middleware' => ['allowedmodule:cities,list']], function () {
            Route::get('/', 'CitiesController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'CitiesController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'CitiesController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'CitiesController@delete')->name('.delete');
        });

        Route::group(['prefix' => 'faqs', 'as' => 'faqs', 'middleware' => ['allowedmodule:faqs,list']], function () {
            Route::get('/', 'FaqController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'FaqController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'FaqController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'FaqController@delete')->name('.delete');

        });
        //delivery_charges
        Route::group(['prefix' => 'delivery_charges', 'as' => 'delivery_charges', 'middleware' => ['allowedmodule:delivery_charges,list']], function () {
            Route::get('/', 'DeliveryChargesController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'DeliveryChargesController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'DeliveryChargesController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'DeliveryChargesController@delete')->name('.delete');

        });

        Route::group(['prefix' => 'transaction', 'as' => 'transaction', 'middleware' => ['allowedmodule:transaction,list']], function () {
            Route::get('/', 'TransactionController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'TransactionController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'TransactionController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'TransactionController@delete')->name('.delete');

        });


        //subscription_orders
        Route::group(['prefix' => 'subscription_orders', 'as' => 'subscription_orders', 'middleware' => ['allowedmodule:subscription_orders,list']], function () {
            Route::get('/', 'SubscriptionOrderController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'SubscriptionOrderController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'SubscriptionOrderController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'SubscriptionOrderController@delete')->name('.delete');
            Route::match(['get', 'post'], 'update_subscription/{id}', 'SubscriptionOrderController@update_subscription')->name('.update_subscription');
            Route::match(['get', 'post'], 'generate_subscription_order', 'SubscriptionOrderController@generate_subscription_order')->name('.generate_subscription_order');

        });

        // roles
        Route::group(['prefix' => 'roles', 'as' => 'roles', 'middleware' => ['allowedmodule:roles,list']], function () {
            Route::get('/', 'RoleController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'RoleController@add')->name('.add');
            Route::match(['get', 'post'], 'get_roles', 'RoleController@get_roles')->name('.get_roles');
            Route::match(['get', 'post'], 'change_role_status', 'RoleController@change_role_status')->name('.change_role_status');
            Route::match(['get', 'post'], 'edit/{id}', 'RoleController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'RoleController@delete')->name('.delete');
        });
        // admins
        Route::group(['prefix' => 'admins', 'as' => 'admins', 'middleware' => ['allowedmodule:admins,list']], function () {
            Route::get('/', 'AdminController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'AdminController@add')->name('.add');
            Route::match(['get', 'post'], 'get_roles', 'AdminController@get_roles')->name('.get_roles');
            Route::match(['get', 'post'], 'change_role_status', 'AdminController@change_role_status')->name('.change_role_status');
            Route::match(['get', 'post'], 'edit/{id}', 'AdminController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'AdminController@delete')->name('.delete');
        });


        // permission
        Route::group(['prefix' => 'permission', 'as' => 'permission', 'middleware' => ['allowedmodule:permission,list']], function () {
            Route::get('/', 'PermissionController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'PermissionController@add')->name('.add');
            Route::match(['get', 'post'], 'get_roles', 'PermissionController@get_roles')->name('.get_roles');
            Route::match(['get', 'post'], 'change_role_status', 'PermissionController@change_role_status')->name('.change_role_status');
            Route::match(['get', 'post'], 'edit/{id}', 'PermissionController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'PermissionController@delete')->name('.delete');
            Route::match(['get', 'post'], '/update_permission', 'PermissionController@update_permission')->name('.update_permission');
        });

        ////categories
        Route::group(['prefix' => 'categories', 'as' => 'categories', 'middleware' => ['allowedmodule:categories,list']], function () {
            Route::get('/', 'CategoryController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'CategoryController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'CategoryController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'CategoryController@delete')->name('.delete');
        });


          ////loyality_system
        Route::group(['prefix' => 'loyality_system', 'as' => 'loyality_system', 'middleware' => ['allowedmodule:loyality_system,list']], function () {
            Route::get('/', 'LoyalitySystemController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'LoyalitySystemController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'LoyalitySystemController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'LoyalitySystemController@delete')->name('.delete');
        });

          ////free_product
        Route::group(['prefix' => 'free_product', 'as' => 'free_product', 'middleware' => ['allowedmodule:free_product,list']], function () {
            Route::get('/', 'FreeProductController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'FreeProductController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'FreeProductController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'FreeProductController@delete')->name('.delete');
        });

        ////abandoned_cart
        Route::group(['prefix' => 'abandoned_cart', 'as' => 'abandoned_cart', 'middleware' => ['allowedmodule:abandoned_cart,list']], function () {
            Route::get('/', 'AbandonedCartController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'AbandonedCartController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'AbandonedCartController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'AbandonedCartController@delete')->name('.delete');
        });
                ////inventory_management
        Route::group(['prefix' => 'inventory_management', 'as' => 'inventory_management', 'middleware' => ['allowedmodule:inventory_management,list']], function () {
            Route::get('/', 'InventoryController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'InventoryController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'InventoryController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'InventoryController@delete')->name('.delete');
            Route::match(['get', 'post'], 'stock_out', 'InventoryController@stock_out')->name('.stock_out');
            Route::match(['get', 'post'], 'stock_in', 'InventoryController@stock_in')->name('.stock_in');
            Route::match(['get', 'post'], 'stock_transfer', 'InventoryController@stock_transfer')->name('.stock_transfer');

        });

          Route::group(['prefix' => 'export', 'as' => 'export', 'middleware' => ['allowedmodule:export,list']], function () {

            Route::get('/stock_data', 'ExportController@stock_data')->name('.stock_data');
            Route::post('/stock_data_import', 'ExportController@stock_data_import')->name('.stock_data_import');
            Route::get('/transaction', 'ExportController@transaction')->name('.transaction');

        });




        ////subcategories
        Route::group(['prefix' => 'subcategories', 'as' => 'subcategories', 'middleware' => ['allowedmodule:subcategories,list']], function () {
            Route::get('/', 'SubCategoryController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'SubCategoryController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'SubCategoryController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'SubCategoryController@delete')->name('.delete');

        });

        ////suppliers
        Route::group(['prefix' => 'suppliers', 'as' => 'suppliers', 'middleware' => ['allowedmodule:suppliers,list']], function () {
            Route::get('/', 'SupplierController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'SupplierController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'SupplierController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'SupplierController@delete')->name('.delete');

        });


        ////invoices
        Route::group(['prefix' => 'invoices', 'as' => 'invoices', 'middleware' => ['allowedmodule:invoices,list']], function () {
            Route::get('/', 'InvoiceController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'InvoiceController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'InvoiceController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'InvoiceController@delete')->name('.delete');
            Route::match(['get', 'post'], 'show/{invoice}', 'InvoiceController@show')->name('.show');

        });
 ////stocks
        Route::group(['prefix' => 'stocks', 'as' => 'stocks', 'middleware' => ['allowedmodule:stocks,list']], function () {
            Route::get('/', 'StockController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'StockController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'StockController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'StockController@delete')->name('.delete');
            Route::match(['get', 'post'], 'closingStockList', 'StockController@closingStockList')->name('.closingStockList');
            Route::match(['get', 'post'], 'stockLogs', 'StockController@stockLogs')->name('.stockLogs');

        });
////return_request
        Route::group(['prefix' => 'return_request', 'as' => 'return_request', 'middleware' => ['allowedmodule:return_request,list']], function () {
            Route::get('/', 'StockController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'StockController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'StockController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'StockController@delete')->name('.delete');
            Route::match(['get', 'post'], 'closingStockList', 'StockController@closingStockList')->name('.closingStockList');
            Route::match(['get', 'post'], 'stockLogs', 'StockController@stockLogs')->name('.stockLogs');

        });

        ////stock_transfers
        Route::group(['prefix' => 'stock_transfers', 'as' => 'stock_transfers', 'middleware' => ['allowedmodule:stock_transfers,list']], function () {
            Route::get('/', 'StockTransferController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'StockTransferController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'StockTransferController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'StockTransferController@delete')->name('.delete');
            Route::match(['get', 'post'], 'approve/{id}', 'StockTransferController@approve')->name('.approve');
            Route::match(['get', 'post'], 'reject/{id}', 'StockTransferController@reject')->name('.reject');

        });

        ////child_categories
        Route::group(['prefix' => 'child_categories', 'as' => 'child_categories', 'middleware' => ['allowedmodule:child_categories,list']], function () {
            Route::get('/', 'ChildCategoryController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'ChildCategoryController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'ChildCategoryController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'ChildCategoryController@delete')->name('.delete');

        });


        ////brands
        Route::group(['prefix' => 'brands', 'as' => 'brands', 'middleware' => ['allowedmodule:brands,list']], function () {
            Route::get('/', 'BrandController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'BrandController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'BrandController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'BrandController@delete')->name('.delete');

        });

        ////support_tickets
        Route::group(['prefix' => 'support_tickets', 'as' => 'support_tickets', 'middleware' => ['allowedmodule:support_tickets,list']], function () {
            Route::get('/', 'SupportTicketController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'SupportTicketController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'SupportTicketController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'SupportTicketController@delete')->name('.delete');
            Route::match(['get', 'post'], 'get_chats', 'SupportTicketController@get_chats')->name('.get_chats');
            Route::match(['get', 'post'], 'submit_chat', 'SupportTicketController@submit_chat')->name('.submit_chat');
            Route::match(['get', 'post'], 'update_status', 'SupportTicketController@update_status')->name('.update_status');

        });

        ////slots
        Route::group(['prefix' => 'slots', 'as' => 'slots', 'middleware' => ['allowedmodule:slots,list']], function () {
            Route::get('/', 'SlotController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'SlotController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'SlotController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'SlotController@delete')->name('.delete');

        });

        ////manufacturer
        Route::group(['prefix' => 'manufacturer', 'as' => 'manufacturer', 'middleware' => ['allowedmodule:manufacturer,list']], function () {
            Route::get('/', 'ManufacturerController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'ManufacturerController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'ManufacturerController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'ManufacturerController@delete')->name('.delete');

        });


        ////delivery_agents
        Route::group(['prefix' => 'delivery_agents', 'as' => 'delivery_agents', 'middleware' => ['allowedmodule:delivery_agents,list']], function () {
            Route::get('/', 'DeliveryAgentsController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'DeliveryAgentsController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'DeliveryAgentsController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'DeliveryAgentsController@delete')->name('.delete');
            Route::match(['get', 'post'], 'view/{id}', 'DeliveryAgentsController@view')->name('.view');
            Route::match(['get', 'post'], 'transactions/{id}', 'DeliveryAgentsController@transactions')->name('.transactions');

        });
        ////subscriptions
        Route::group(['prefix' => 'subscriptions', 'as' => 'subscriptions', 'middleware' => ['allowedmodule:subscriptions,list']], function () {
            Route::get('/', 'SubscriptionController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'SubscriptionController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'SubscriptionController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'SubscriptionController@delete')->name('.delete');
            Route::match(['get', 'post'], 'view/{id}', 'SubscriptionController@view')->name('.view');
            Route::match(['get', 'post'], 'transactions/{id}', 'SubscriptionController@transactions')->name('.transactions');
            Route::match(['get', 'post'], 'add_subscription', 'SubscriptionController@add_subscription')->name('.add_subscription');

        });
        ////featured_section
        Route::group(['prefix' => 'featured_section', 'as' => 'featured_section', 'middleware' => ['allowedmodule:featured_section,list']], function () {
            Route::get('/', 'FeaturedSectionController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'FeaturedSectionController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'FeaturedSectionController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'FeaturedSectionController@delete')->name('.delete');
            Route::match(['get', 'post'], 'view/{id}', 'FeaturedSectionController@view')->name('.view');
            Route::match(['get', 'post'], 'transactions/{id}', 'FeaturedSectionController@transactions')->name('.transactions');

        });

        ////attributes
        Route::group(['prefix' => 'attributes', 'as' => 'attributes', 'middleware' => ['allowedmodule:attributes,list']], function () {
            Route::get('/', 'AttributesController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'AttributesController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'AttributesController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'AttributesController@delete')->name('.delete');
        });
        ////gift_card
        Route::group(['prefix' => 'gift_card', 'as' => 'gift_card', 'middleware' => ['allowedmodule:gift_card,list']], function () {
            Route::get('/', 'GiftCardController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'GiftCardController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'GiftCardController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'GiftCardController@delete')->name('.delete');
        });
     ////tags
        Route::group(['prefix' => 'tags', 'as' => 'tags', 'middleware' => ['allowedmodule:tags,list']], function () {
            Route::get('/', 'TagsController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'TagsController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'TagsController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'TagsController@delete')->name('.delete');
        });


        ////tax
        Route::group(['prefix' => 'tax', 'as' => 'tax', 'middleware' => ['allowedmodule:tax,list']], function () {
            Route::get('/', 'TaxController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'TaxController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'TaxController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'TaxController@delete')->name('.delete');
        });
        ////subscription_plans
        Route::group(['prefix' => 'subscription_plans', 'as' => 'subscription_plans', 'middleware' => ['allowedmodule:subscription_plans,list']], function () {
            Route::get('/', 'SubscriptionPlanController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'SubscriptionPlanController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'SubscriptionPlanController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'SubscriptionPlanController@delete')->name('.delete');
        });

        ////sellers
        Route::group(['prefix' => 'sellers', 'as' => 'sellers', 'middleware' => ['allowedmodule:sellers,list']], function () {
            Route::get('/', 'SellerController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'SellerController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'SellerController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'SellerController@delete')->name('.delete');
            Route::match(['get', 'post'], 'view/{id}', 'SellerController@view')->name('.view');
            Route::match(['get', 'post'], 'commission/{id}', 'SellerController@commission')->name('.commission');
            Route::match(['get', 'post'], 'products/{id}', 'SellerController@products')->name('.products');
            Route::match(['get', 'post'], 'admins/{id}', 'SellerController@admins')->name('.admins');
            Route::match(['get', 'post'], 'roles/{id}', 'SellerController@roles')->name('.roles');
            Route::match(['get', 'post'], 'permission/{id}', 'SellerController@permission')->name('.permission');
            Route::match(['get', 'post'], 'orders/{id}', 'SellerController@orders')->name('.orders');
            Route::match(['get', 'post'], 'add_role', 'SellerController@add_role')->name('.add_role');
            Route::match(['get', 'post'], 'update_permission', 'SellerController@update_permission')->name('.update_permission');
        });

        ////products
        Route::group(['prefix' => 'products', 'as' => 'products', 'middleware' => ['allowedmodule:products,list']], function () {
            Route::get('/', 'ProductController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'ProductController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'ProductController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'ProductController@delete')->name('.delete');
            Route::match(['get', 'post'], 'view/{id}', 'ProductController@view')->name('.view');
            Route::match(['get', 'post'], 'search', 'ProductController@search')->name('.search');
            Route::match(['get', 'post'], 'sample', 'ProductController@sample')->name('.sample');
            Route::match(['get', 'post'], 'import', 'ProductController@import')->name('.import');
            Route::match(['get', 'post'], 'import_product', 'ProductController@import_product')->name('.import_product');
            Route::match(['get', 'post'], 'approve_product', 'ProductController@approve_product')->name('.approve_product');
            Route::match(['get', 'post'], 'assign_product', 'ProductController@assign_product')->name('.assign_product');
        });

        ////orders
        Route::group(['prefix' => 'orders', 'as' => 'orders', 'middleware' => ['allowedmodule:orders,list']], function () {
            Route::get('/', 'OrderController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'OrderController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'OrderController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'OrderController@delete')->name('.delete');
            Route::match(['get', 'post'], 'view/{id}', 'OrderController@view')->name('.view');
            Route::match(['get', 'post'], 'generateInvoicePdf/{id}', 'OrderController@generateInvoicePdf')->name('.generateInvoicePdf');
            Route::match(['get', 'post'], 'update_order_status', 'OrderController@update_order_status')->name('.update_order_status');
            Route::match(['get', 'post'], 'update_logistics', 'OrderController@update_logistics')->name('.update_logistics');
            Route::match(['get', 'post'], 'bookshipment_shiprocket/{id}', 'OrderController@bookshipment_shiprocket')->name('.bookshipment_shiprocket');
            Route::match(['get', 'post'], 'update_address/{id}', 'OrderController@update_address')->name('.update_address');
            Route::match(['get', 'post'], 'get_varients', 'OrderController@get_varients')->name('.get_varients');
            Route::match(['get', 'post'], 'get_varient_detail', 'OrderController@get_varient_detail')->name('.get_varient_detail');
            Route::match(['get', 'post'], 'update_order', 'OrderController@update_order')->name('.update_order');
        });
        ////users
        Route::group(['prefix' => 'users', 'as' => 'users', 'middleware' => ['allowedmodule:users,list']], function () {
            Route::get('/', 'UserController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'UserController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'UserController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'UserController@delete')->name('.delete');
            Route::match(['get', 'post'], 'view/{id}', 'UserController@view')->name('.view');
            Route::match(['get', 'post'], 'transactions/{id}', 'UserController@transactions')->name('.transactions');
            Route::match(['get', 'post'], 'orders/{id}', 'UserController@orders')->name('.orders');
            Route::match(['get', 'post'], 'search', 'UserController@search')->name('.search');
            Route::match(['get', 'post'], 'update_wallet', 'UserController@update_wallet')->name('.update_wallet');
            Route::match(['get', 'post'], 'import', 'UserController@import')->name('.import');
        });
        ////gallery
        Route::group(['prefix' => 'gallery', 'as' => 'gallery', 'middleware' => ['allowedmodule:gallery,list']], function () {
            Route::get('/', 'GalleryController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'GalleryController@add')->name('.add');
            Route::match(['get', 'post'], 'delete', 'GalleryController@delete')->name('.delete');
        });

        ////offers
        Route::group(['prefix' => 'offers', 'as' => 'offers', 'middleware' => ['allowedmodule:offers,list']], function () {
            Route::get('/', 'OfferController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'OfferController@add')->name('.add');
            Route::match(['get', 'post'], 'edit/{id}', 'OfferController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'OfferController@delete')->name('.delete');
            Route::match(['get', 'post'], 'fetch_user', 'OfferController@fetch_user')->name('.fetch_user');
        });


        ////withdraw_request
        Route::group(['prefix' => 'withdraw_request', 'as' => 'withdraw_request', 'middleware' => ['allowedmodule:withdraw_request,list']], function () {
            Route::get('/', 'WithdrawController@index')->name('.index');
            Route::match(['get', 'post'], 'update_status', 'WithdrawController@update_status')->name('.update_status');
        });

        ////notifications
        Route::group(['prefix' => 'notifications', 'as' => 'notifications', 'middleware' => ['allowedmodule:notifications,list']], function () {
            Route::get('/', 'NotificationController@index')->name('.index');
            Route::match(['get', 'post'], 'add', 'NotificationController@add')->name('.add');
            Route::match(['get', 'post'], 'send/{id}', 'NotificationController@send')->name('.send');
            Route::match(['get', 'post'], 'edit/{id}', 'NotificationController@add')->name('.edit');
            Route::match(['get', 'post'], 'delete/{id}', 'NotificationController@delete')->name('.delete');
        });


    });
});
