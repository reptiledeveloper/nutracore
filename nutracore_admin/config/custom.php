<?php

return [

    'RAZORPAY_KEY' => env('RAZORPAY_KEY'),
    'RAZORPAY_SECRET' => env('RAZORPAY_SECRET'),
    'ADMIN_ROUTE_NAME' => 'admin',
    'admin_email' => env('ADMIN_EMAIL'),
    'from_email' => '',
    'order_prefix' => 'SJ',

    'order_status_arr' => [
        'PLACED' => 'PLACED',
        'CONFIRM' => 'CONFIRM',
        'CANCEL' => 'CANCEL',
        'OUT_FOR_DELIVERY' => 'OUT_FOR_DELIVERY',
        //'SHIPPED' => 'SHIPPED',
        'DELIVERED' => 'DELIVERED',
    ],
    'home_styles_arr' => [
        'navBar' => 'navBar',
        'recommendedOffers' => 'recommendedOffers',
        'bannerWidget' => 'bannerWidget',
        'exploreByCategoryWidget' => 'exploreByCategoryWidget',
        'subscriptionOfferedProducts' => 'subscriptionOfferedProducts',
        'largeBannerWidget' => 'largeBannerWidget',
        'mostBoughtProductsWidget' => 'mostBoughtProductsWidget',
        'exclusiveOfferWidget' => 'exclusiveOfferWidget',
        'trendingProducts' => 'trendingProducts',
    ],

    'our_free_service_types' => [
        'near_by' => 'Nearby',
        'events' => 'Events',
        'insurance' => 'Insurance',
        'estore' => 'E-store',
        'helpline' => 'Helpline',
        'parking' => 'Parking',
        'sos' => 'SOS',
        'others' => 'Others',
    ],

    "signs" => [
        ">",
        "<",
        "<=",
        ">=",
    ]
];
