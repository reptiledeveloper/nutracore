<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Admin;
use App\Models\Category;
use App\Models\DeliveryAgents;
use App\Models\FeaturedSection;
use App\Models\Offers;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Products;
use App\Models\QRCodes;
use App\Models\Wishlist;
use App\Models\RazorpayOrders;
use App\Models\Setting;
use App\Models\Brand;
use App\Models\StockLog;
use App\Models\SubscriptionPlans;
use App\Models\Subscriptions;
use App\Models\User;
use App\Models\Banner;
use App\Models\Cart;
use App\Models\UserAddress;
use App\Models\VendorProductPrice;
use App\Models\Vendors;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Session;
use Validator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class HomeController extends Controller
{

    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $category_id = $request->input('category_id');

        $products = \App\Models\Product::query()
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            })
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get(['name', 'slug']); // return only necessary fields

        return response()->json($products);
    }

    public function index(Request $request)
    {
//        Cache::flush();
        $user = Auth::guard('user')->user();
        $homepageArr = [];

        /* -------------------------------------------
           CACHEABLE DATA (10 minutes)
        ------------------------------------------- */
        $cacheKey = 'homepage_data';
        $cacheTime = 600; // 10 minutes

        $cachedData = Cache::remember($cacheKey, $cacheTime, function () {
            $data = [];

            // BANNERS + PRODUCTS
            $banners = Banner::where('status', 1)
                ->where('is_delete', 0)
                ->get();

            $allProductIds = collect($banners)
                ->pluck('product_id')
                ->flatMap(fn($pids) => explode(',', $pids))
                ->unique()
                ->filter()
                ->toArray();

//            $products = HomeController::getProductsByIds($allProductIds); // fetch all products once
            $products = []; // fetch all products once

            $data['banners'] = $banners->map(function ($banner) use ($products) {
                $banner->banner_img = CustomHelper::getImageUrl('banners', $banner->banner_img);
                $banner->products = collect(explode(',', $banner->product_id))
                    ->map(fn($pid) => $products[$pid] ?? null)
                    ->filter()
                    ->values();
                return $banner;
            });

            // CATEGORIES
            $data['categories'] = Category::select('id', 'name', 'image', 'priority', 'slug')
                ->where(['status' => 1, 'parent_id' => 0, 'is_goal' => 0, 'is_delete' => 0])
                ->orderBy('priority')
                ->get()
                ->map(fn($cat) => tap($cat, fn($c) => $c->image = CustomHelper::getImageUrl('categories', $c->image)));

            $data['goalcategories'] = Category::select('id', 'name', 'image', 'priority', 'slug')
                ->where(['status' => 1, 'parent_id' => 0, 'is_goal' => 1, 'is_delete' => 0])
                ->orderBy('priority')
                ->get()
                ->map(fn($cat) => tap($cat, fn($c) => $c->image = CustomHelper::getImageUrl('categories', $c->image)));

            // BRANDS
            $data['brands'] = Brand::select('id', 'brand_img', 'brand_name', 'certificate', 'priority', 'slug')
                ->where(['status' => 1, 'is_delete' => 0])
                ->orderBy('priority')
                ->get()
                ->map(fn($brand) => tap($brand, function ($b) {
                    $b->brand_img = CustomHelper::getImageUrl('brands', $b->brand_img);
                    $b->brand_icon = $b->brand_img;
                    $b->certificate = CustomHelper::getImageUrl('brands', $b->certificate);
                }));

            // SUBSCRIPTION PLANS + BEST VALUE
            $data['subscription_plans'] = SubscriptionPlans::where(['status' => 1, 'is_delete' => 0])
                ->get()
                ->map(fn($plan) => tap($plan, function ($p) {
                    $durationDays = $p->duration * 30.44;
                    $p->price_per_day = $durationDays > 0 ? ($p->price / $durationDays) : INF;
                    $p->image = CustomHelper::getImageUrl('subscription_plans', $p->image);
                }));

            $bestPlanId = $data['subscription_plans']->sortBy('price_per_day')->first()?->id;
            $data['subscription_plans']->each(fn($p) => $p->is_best_value = ($p->id == $bestPlanId) ? 1 : 0);

            // TESTIMONIALS + NEW UPDATES
            $data['new_updates'] = DB::table('new_updates')
                ->where(['is_delete' => 0, 'status' => 1])
                ->limit(5)
                ->get()
                ->map(fn($item) => tap($item, fn($i) => $i->product = HomeController::getProductDetails($i->product_id ?? '', null)));

            $data['testimonials'] = DB::table('testimonial')
                ->where(['is_delete' => 0, 'status' => 1])
                ->limit(5)
                ->get()
                ->map(fn($item) => tap($item, fn($i) => $i->image = CustomHelper::getImageUrl('testimonials', $i->image)));

            return $data;
        });

        // Merge cached data
        $homepageArr = array_merge($homepageArr, $cachedData);

        /* -------------------------------------------
           COLLECTION PRODUCTS (dynamic, not cached)
        ------------------------------------------- */
        $homepageArr['best_sellers'] = HomeController::getCollectionProducts(1);
        $homepageArr['best_deals'] = HomeController::getCollectionProducts(2);
        $homepageArr['newArrival'] = HomeController::getCollectionProducts(3);

        /* -------------------------------------------
           USER DATA (dynamic, not cached)
        ------------------------------------------- */
        if ($user) {
            $homepageArr['selected_address'] = CustomHelper::getAddressDetails($user->addressID) ?? '';
            $homepageArr['seller_details'] = self::getSellerDetails($user->seller_id, $user->id);
        }

        return view('home.index', $homepageArr);
    }


    public static function getCollectionProducts($collectionId)
    {
        $collection = DB::table('collections')->find($collectionId);
        if (!$collection) return [];

        $ids = explode(",", $collection->product_ids ?? '');

        return collect(Product::where('status', 1)->whereIn('id', $ids)->get())
            ->map(fn($p) => self::getProductDetails($p->id, null))
            ->filter()
            ->values();
    }

    public function best_sellers(Request $request)
    {
        $best_sellers = [];
        $collections = DB::Table('collections')->where('id', 1)->first();
        $product_ids = explode(",", $collections->product_ids ?? '');
        $best_sellersArr = Product::where('status', 1)->whereIn('id', $product_ids)->latest()->get();

        if (!empty($best_sellersArr)) {
            foreach ($best_sellersArr as $product) {
                $pro_data = self::getProductDetails($product->id, $user->id ?? '');
                if (!empty($pro_data)) {
                    $best_sellers[] = $pro_data;
                }
            }
        }
        $data['products'] = $best_sellers;
        return view('home.products', $data);
    }


    public function best_deals(Request $request)
    {
        $best_deals = [];
        $collections = DB::Table('collections')->where('id', operator: 2)->first();
        $product_ids = explode(",", $collections->product_ids ?? '');
        $best_dealsArr = Product::where('status', 1)->whereIn('id', $product_ids)->latest()->get();

        if (!empty($best_dealsArr)) {
            foreach ($best_dealsArr as $product) {
                $pro_data = self::getProductDetails($product->id, $user->id ?? '');
                if (!empty($pro_data)) {
                    $best_deals[] = $pro_data;
                }
            }
        }
        $data['products'] = $best_deals;
        return view('home.products', $data);
    }

    public function new_arrivals(Request $request)
    {
        $new_arrivals = [];
        $collections = DB::Table('collections')->where('id', 3)->first();
        $product_ids = explode(",", $collections->product_ids ?? '');
        $new_arrivalsArr = Product::where('status', 1)->whereIn('id', $product_ids)->latest()->get();
        if (!empty($new_arrivalsArr)) {
            foreach ($new_arrivalsArr as $product) {
                $pro_data = self::getProductDetails($product->id, $user->id ?? '');
                if (!empty($pro_data)) {
                    $new_arrivals[] = $pro_data;
                }
            }
        }
        $data['products'] = $new_arrivals;
        return view('home.products', $data);
    }

    public function categories(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $categories = Category::where('status', 1)->where('parent_id', 0)->where('is_goal', 0)->where('is_delete', 0)->where('status', 1)->orderBy('priority', 'ASC')->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);

        $data['categories'] = $categories;

        return view('home.categories', $data);
    }

    public function brands(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $brands = $brands = Brand::where('status', 1)->where('is_delete', 0)
            ->orderBy('brand_name', 'asc')
            ->get();
        $data['brands'] = $brands;

        return view('home.brands', $data);
    }

    public function products(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $user = Auth::user() ?? [];
        $data = [];
        $search = $request->search ?? '';
        $type = $request->type ?? '';
        $category_slug = $request->slug ?? '';
        $subcategory_id = $request->subcategory_id ?? '';
        $min_price = $request->min_price ?? '';
        $max_price = $request->max_price ?? '';
        $order_by_price = $request->order_by_price ?? '';
        $brand_id = $request->brand_id ?? '';
        $category_id = '';

        if (!empty($category_slug)) {
            $category_id = Category::where('slug', $category_slug)->first()->id ?? '';
        }
        if (!empty($category_slug)) {
            $brand_id = Brand::where('slug', $category_slug)->first()->id ?? '';
        }


        $products = Product::select(
            'products.id',
            DB::raw('MIN(product_varients.selling_price) as min_price')
        )
            ->where('products.is_delete', 0)
            ->where('products.status', 1)
            ->leftJoin('product_varients', 'products.id', '=', 'product_varients.product_id');

// Price filter
        if (!empty($min_price) && !empty($max_price) && $max_price > 0) {
            $products->whereBetween('product_varients.selling_price', [$min_price, $max_price]);
        }

// Search filter
        if (!empty($search)) {
            $products->where('products.name', 'like', '%' . $search . '%');
        }

// Other filters
        if (!empty($product_id)) {
            $products->whereIn('products.id', $product_id);
        }
        if (!empty($category_id)) {
            $products->where('products.category_id', $category_id);
        }
        if (!empty($brand_id)) {
            $products->where('products.brand_id', $brand_id);
        }
        if (!empty($subcategory_id)) {
            $products->where('products.subcategory_id', $subcategory_id);
        }

// Order by price
//        if ($order_by_price == 'low_to_high') {
//            $products->orderBy('min_price', 'ASC');
//        } elseif ($order_by_price == 'high_to_low') {
//            $products->orderBy('min_price', 'DESC');
//        }

        $products = $products->groupBy('products.id')->paginate(1000);

        $productArr = [];
        if (!empty($products)) {
            foreach ($products as $product_val) {
                $product_data = self::getProductDetails($product_val->id, $user->id ?? '');
                if (!empty($product_data)) {
                    $productArr[] = $product_data;
                }

            }
        }

        if (!empty($category_id)) {
            $category = Category::find($category_id);
            if ($category->is_goal == 1) {
                $product_ids = $category->product_ids ?? '';
                $product_ids = explode(",", $product_ids);
                if (!empty($product_ids)) {
                    foreach ($product_ids as $pro) {
                        $product_data = self::getProductDetails($pro, $user->id ?? '');
                        if (!empty($product_data)) {
                            $productArr[] = $product_data;
                        }
                    }
                }
            }
        }


//        echo "<pre>";
//        print_r($productArr);
//        die;
        usort($productArr, function ($a, $b) use ($order_by_price) {

            // Helper to get first variant price only
            $getPrice = function ($product) {
                if (is_array($product)) {
                    // Array type product
                    if (!empty($product['variants']) && isset($product['variants'][0]['selling_price'])) {
                        return (float)$product['variants'][0]['selling_price'];
                    }
                } else {
                    // Object type product
                    if (!empty($product->variants) && isset($product->variants[0]->selling_price)) {
                        return (float)$product->variants[0]->selling_price;
                    }
                }

                // No variant found
                return 0.0;
            };

            $priceA = $getPrice($a);
            $priceB = $getPrice($b);

            // Normalize order input
            $order = $order_by_price ?: 'low_to_high';

            return $order === "high_to_low"
                ? ($priceB <=> $priceA)   // Descending order
                : ($priceA <=> $priceB);  // Ascending order
        });


        $categories = Category::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
        $data['categories'] = $categories;
        $data['products'] = $productArr;


        return view('home.products', $data);
    }

    public static function getNcCashPercent($user, $amount)
    {
        $is_active = 0;

        $subscription_end_date = '';
        if (!empty($user)) {
            $exist_subscription = Subscriptions::where('user_id', $user->id)->where('paid_status', 1)->latest()->first();
            if (!empty($exist_subscription)) {
                $current_date = date('Y-m-d');
                if (strtotime($user->subscription_end) >= strtotime($current_date)) {
                    $is_active = 1;

                }
            }
        }

        $type = ($is_active == 1) ? 'subscribe' : 'not_subscribe';
        $active_loyalty = DB::table('loyality_system')
            ->where('status', 1)
            ->where('type', $type)
            ->where('from_amount', '<=', $amount)
            ->where('to_amount', '>=', $amount)
            ->first();
        if (!empty($active_loyalty)) {
            return round(((int)$amount * (int)$active_loyalty->cashback) / 100);
        }
        return 0;

    }

    public static function calculateDiscountPer($originalPrice, $discountedPrice)
    {
        if ($originalPrice <= 0) {
            return 0;
        }
        $discount = ((int)$originalPrice - (int)$discountedPrice) / (int)$originalPrice * 100;
        return round($discount);
    }


    public static function getNearestSeller($latitude, $longitude, $radiusHours = 2, $avgSpeed = 40)
    {
        // radiusHours = 2 hrs, avgSpeed = 40 km/h → max distance = 80 km
        $maxDistance = $radiusHours * $avgSpeed;

        return Vendors::select(
            'vendors.*',
            DB::raw("6371 * acos(cos(radians($latitude))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians($longitude))
                    + sin(radians($latitude))
                    * sin(radians(latitude))) AS distance")
        )
            ->havingRaw("distance <= two_hr_radius")
            ->orderBy("distance", "asc")
            ->first();
    }

    public static function getProductDetails($product_id, $user_id = null)
    {
        date_default_timezone_set("Asia/Kolkata");

        // 1️⃣ User & Address
        $user = $user_id ? User::find($user_id) : null;
        $latitude = session('latitude') ?? '17.44757253036007';
        $longitude = session('longitude') ?? '78.30504618870073';
        $pincode = session('pincode') ?? '';

        if ($user) {
            $address = $user->addressID ? UserAddress::find($user->addressID) : null;
            $latitude = $address->latitude ?? $latitude;
            $longitude = $address->longitude ?? $longitude;
            $pincode = $user->pincode ?? $address->pincode ?? $pincode;
        }
//        Cache::flush();
        // 2️⃣ Nearest seller (cache 5 minutes)
        $seller = Cache::remember("nearest_seller_{$user_id}_{$latitude}_{$longitude}", 300, function () use ($latitude, $longitude) {
            return self::getNearestSeller($latitude, $longitude, 2, 40);
        });

        // 3️⃣ Estimated delivery day (cache 10 minutes per user or pincode)
        $estimated_day = self::getEstimatedDayCached($user, $seller, $pincode);

        // 4️⃣ Product + variants with eager loading
        $product = Product::with(['varients' => fn($q) => $q->where('status', 1)->where('is_delete', 0)])
            ->find($product_id);
        if (!$product) return null;

        // 5️⃣ Product images in batch
        $product_images = DB::table('product_images')
            ->where('product_id', $product->id)
            ->get()
            ->groupBy('varient_id');

        // 6️⃣ Brand
        $brand = $product->brand_id ? Brand::find($product->brand_id) : null;

        // 7️⃣ Map variants
        $product_varients = [];
        foreach ($product->varients as $varient) {

            $varient_images = collect($product_images[$varient->id] ?? [])
                ->map(fn($img) => ['id' => $img->id, 'image' => CustomHelper::getImageUrl('products', $img->image)])
                ->toArray();
            $varient_images[] = ['id' => 0, 'image' => CustomHelper::getImageUrl('products', $product->image)];

            $product_varients[] = [
                'id' => $varient->id,
                'product_id' => $product->id,
                'mrp' => $varient->mrp,
                'selling_price' => $varient->selling_price,
                'subscription_price' => $varient->subscription_price,
                'unit' => $varient->unit,
                'qty' => $user ? CustomHelper::getCartQty($user_id, $product->id, $varient->id) : 0,
                'discount_per' => self::calculateDiscountPer($varient->mrp, $varient->selling_price),
                'is_wishlist' => $user ? CustomHelper::checkWishlist($user_id, $product->id, $varient->id) : 0,
                'images' => $varient_images,
                'nc_cash' => self::getNcCashPercent($user, $varient->selling_price),
                'is_out_of_stock' => CustomHelper::checkOutofStock($product->id, $varient->id),
            ];
        }

        // 8️⃣ Product-level images
        $images = collect($product_images[0] ?? [])
            ->map(fn($img) => ['id' => $img->id, 'image' => CustomHelper::getImageUrl('products', $img->image)])
            ->toArray();
        $images[] = ['id' => 0, 'image' => CustomHelper::getImageUrl('products', $product->image)];

        // 9️⃣ Final product object
        if (empty($product_varients)) {

            $product_varients[] = [
                'id' => 0,
                'product_id' => $product->id,
                'mrp' => $product->product_mrp,
                'unit' => "",
                'subscription_price' => $product->product_subscription_price,
                'selling_price' => $product->product_selling_price,
                'qty' => $user ? CustomHelper::getCartQty($user_id, $product->id, 0) : 0,
                'discount_per' => $product->product_mrp && $product->product_selling_price
                    ? round((($product->product_mrp - $product->product_selling_price) / $product->product_mrp) * 100)
                    : 0,
                'is_wishlist' => $user ? CustomHelper::checkWishlist($user_id, $product->id, 0) : 0,
                'images' => $images,
                'nc_cash' => self::getNcCashPercent($user, $product->product_selling_price),
                'is_out_of_stock' => CustomHelper::checkOutofStock($product->id, 0),
            ];
        }

        $product->varients = $product_varients;
        $product->images = $images;
        $product->image = CustomHelper::getImageUrl('products', $product->image);
        $product->seller = $seller;
        $product->estimated_day = $estimated_day;
        $product->certificate = CustomHelper::getImageUrl('brands', $brand->certificate ?? '');
        $product->options = CustomHelper::getProductOptions($product->id, $product->option_name ?? '');
        $product->rating = "0";

        return $product;
    }

    public static function calculateEstimatedDay($user = null, $seller = null, $pincode = null)
    {
        date_default_timezone_set("Asia/Kolkata");

        // Default
        $estimated_day = '';

        // Get cutoff time once
        $cutoff_time = CustomHelper::getSettingKey('cutoff_time') ?? '17:00:00';
        $current_time = date('H:i:s');

        // 1️⃣ If seller is available, calculate fast delivery
        if (!empty($seller)) {
            if ($current_time < $cutoff_time) {
                $nextHour = strtotime('+1 hour', strtotime(date('Y-m-d H:00:00')));
                $delivery_time = date('h:i A', strtotime('+2 hours', $nextHour));
                $day_time_text = "Today " . $delivery_time;
            } else {
                $day_time_text = "Tomorrow 11 AM";
            }

            $estimated_day = "Get it By " . $day_time_text;

            // Save seller_id for logged-in user
            if ($user) {
                $user->seller_id = $seller->id ?? null;
                $user->save();
            }
        }

        // 2️⃣ If still no estimated day, use shipment service
        if (empty($estimated_day)) {
            $pincode = $pincode ?? ($user->pincode ?? session('pincode') ?? null);
            if ($pincode) {
                $shipment_data = CustomHelper::checkDelivery($pincode);
                if (!empty($shipment_data)) {
                    $data = json_decode($shipment_data, true);
                    if (!empty($data['data']['available_courier_companies'])) {
                        // Take the first courier's estimated days
                        $days = $data['data']['available_courier_companies'][0]['estimated_delivery_days'] ?? '';
                        if (!empty($days)) {
                            if ($user) {
                                $user->estimated_day = $days;
                                $user->save();
                            }
                            $estimated_day = "Get it Within {$days} Days";
                        }
                    }
                }
            }
        }

        // 3️⃣ If user already has estimated_day saved
        if (empty($estimated_day) && $user?->estimated_day) {
            $estimated_day = "Get it Within " . $user->estimated_day . " Days";
        }

        return $estimated_day ?: "";
    }


    public static function getEstimatedDayCached($user = null, $seller = null, $pincode = null)
    {
        $cacheKey = '';

        if ($user) {
            // Cache per user
            $cacheKey = "estimated_day_user_{$user->id}";
        } elseif ($pincode) {
            // Cache per pincode for guest users
            $cacheKey = "estimated_day_pincode_{$pincode}";
        } else {
            return "";
        }

        // Cache for 10 minutes
        return Cache::remember($cacheKey, 600, function () use ($user, $seller, $pincode) {
            return self::calculateEstimatedDay($user, $seller, $pincode);
        });
    }


    public function getCartProductDetails($cart, $user_id = null)
    {
        $user = [];
        if (!empty($user_id)) {
            $user = User::find($user_id);
        }
        $product = Products::where('id', $cart->product_id)->first();
        if (!empty($product)) {

            if (empty($product->slug)) {
                $product->slug = CustomHelper::GetSlug('products', 'id', $product->id, $product->name);
                $product->save();
            }
            $share_link = '';
            $product->share_link = $share_link;
            $dbArray = [];
            $images = [];
            $dbArray['id'] = 0;
            $dbArray['image'] = CustomHelper::getImageUrl('products', $product->image);
            $images[] = $dbArray;
            $varients = $product->varients()->where('id', $cart->variant_id)->where('is_delete', 0)->where('status', 1)->get();
            if (!empty($varients) && count($varients) > 0) {
                foreach ($varients as $varient) {
                    $qty = 0;
                    if (!empty($user)) {
                        $qty = CustomHelper::getCartQty($user_id, $product->id, $varient->id);
                    }
                    $varient->qty = $qty;
                    $varient->discount_per = self::calculateDiscountPer($varient->mrp ?? 0, $varient->selling_price ?? 0);
                    $is_wishlist = 0;
                    if (!empty($user)) {
                        $is_wishlist = CustomHelper::checkWishlist($user_id, $product->id, $varient->id);
                    }
                    $varient_images = [];
                    $varient->is_wishlist = $is_wishlist;
                    $varient->subscription_price = $varient->subscription_price ?? 0;
                    $dbArray = [];
                    $dbArray['id'] = 0;
                    $dbArray['image'] = CustomHelper::getImageUrl('products', $product->image);
                    $varient_images[] = $dbArray;
                    $product_images = DB::table('product_images')->where('product_id', $product->id)->where('varient_id', $varient->id)->get();
                    if (!empty($product_images)) {
                        foreach ($product_images as $product_image) {
                            $dbArray = [];
                            $dbArray['id'] = $product_image->id ?? '';
                            $dbArray['image'] = CustomHelper::getImageUrl('products', $product_image->image);
                            $varient_images[] = $dbArray;
                        }
                    }

                    $product_images = DB::table('product_images')->where('product_id', $product->id)->where('varient_id', null)->get();
                    if (!empty($product_images)) {
                        foreach ($product_images as $product_image) {
                            $dbArray = [];
                            $dbArray['id'] = $product_image->id ?? '';
                            $dbArray['image'] = CustomHelper::getImageUrl('products', $product_image->image);
                            $varient_images[] = $dbArray;
                        }
                    }
                    $varient->images = $varient_images;
                    $nc_cash = self::getNcCashPercent($user, $varient->selling_price ?? '');

                    $varient->nc_cash = $nc_cash;

                }
            } else {
                $varient_images = [];
                $dbArray = [];
                $dbArray['id'] = 0;
                $dbArray['image'] = CustomHelper::getImageUrl('products', $product->image);
                $varient_images[] = $dbArray;
                $nc_cash = self::getNcCashPercent($user, $product->product_selling_price ?? '');
                $product_images = DB::table('product_images')->where('product_id', $product->id)->get();
                if (!empty($product_images)) {
                    foreach ($product_images as $product_image) {
                        $dbArray = [];
                        $dbArray['id'] = $product_image->id ?? '';
                        $dbArray['image'] = CustomHelper::getImageUrl('products', $product_image->image);
                        $varient_images[] = $dbArray;
                    }
                }
                $varients = [[
                    'id' => 0, // You can keep it product_id or generate a fake ID
                    'product_id' => $product->id,
                    'mrp' => $product->product_mrp,
                    'selling_price' => $product->product_selling_price,
                    'actual_price' => null,
                    'unit' => null, // You can set this dynamically if available
                    'unit_value' => null,
                    'subscription_price' => $product->product_subscription_price,
                    'reward_points' => null,
                    'status' => $product->status,
                    'is_delete' => $product->is_delete,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                    'varient_sku' => $product->sku,
                    'qty' => $cart->qty ?? 0,
                    'discount_per' => $product->product_mrp && $product->product_selling_price
                        ? round((($product->product_mrp - $product->product_selling_price) / $product->product_mrp) * 100)
                        : 0,
                    'is_wishlist' => 0,
                    'images' => $varient_images,
                    'nc_cash' => $nc_cash
                ]];
            }
            $product_images = DB::table('product_images')->where('product_id', $product->id)->get();
            if (!empty($product_images)) {
                foreach ($product_images as $product_image) {
                    $dbArray = [];
                    $dbArray['id'] = $product_image->id ?? '';
                    $dbArray['image'] = CustomHelper::getImageUrl('products', $product_image->image);
                    $images[] = $dbArray;
                }
            }
            $product->images = $images;
            $product->image = CustomHelper::getImageUrl('products', $product->image);
            $product->varients = $varients;

            $product->options = CustomHelper::getProductOptions($product->id ?? '', $product->option_name ?? '');
            $attribute_values = explode(',', $product->attribute_values ?? '');
            $option_name = explode(',', $product->option_name ?? '');
            $product->get_no_coins = 0;
            $brand = [];
            if (!empty($product->brand_id)) {
                $brand = Brand::find($product->brand_id);
            }

            $product->certificate = CustomHelper::getImageUrl('brands', $brand->certificate ?? '');


//            print_r($product);die;
            if (!empty($varients) && count($varients) > 0) {
                return $product;
            }
        }

        return null;
    }


    public function getCartQty(Request $request)
    {
        $user = Auth::user();
        $product_id = $request->product_id ?? '';
        $variant_id = $request->variant_id ?? '';
        $qty = 1;
        $total_qty = 0;
        if (!empty($user)) {
            $qty = CustomHelper::getCartQty($user->id, $product_id, $variant_id);
            $total_qty = Cart::where('user_id', $user->id)->sum('qty');
        }
        if ($qty <= 0) {
            $qty = 1;
        }

        return json_encode(['qty' => $qty, 'total_qty' => $total_qty]);

    }

    public function product_details(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $user = [];
        $user = Auth::user() ?? null;

        $slug = $request->slug ?? '';
        $product = Products::where('slug', $slug)->first();

        $product_data = self::getProductDetails($product->id, $user->id ?? '');
        $data['product_data'] = $product_data;
        $products = Products::where('status', 1)->limit(10)->get();
        if (!empty($products)) {
            foreach ($products as $product_val) {
                $product_data = self::getProductDetails($product_val->id, $user->id ?? '');
                if (!empty($product_data)) {
                    $productArr[] = $product_data;
                }

            }
        }
        $data['products'] = $productArr;
        return view('home.product_details', $data);
    }

    public function cart(Request $request)
    {
        $data = [];
        $user = Auth::user();
        if (empty($user)) {
            return redirect()->to(url('/'));
        }
        $productArr = [];
        $carts = Cart::where('user_id', $user->id)->get();
        if (!empty($carts)) {
            foreach ($carts as $cart) {
                $product_data = self::getCartProductDetails($cart, $user->id ?? '');
                if (!empty($product_data)) {
                    $productArr[] = $product_data;
                }
            }
        }
        $data['cart_products'] = $productArr;
        return view('home.cart', $data);
    }

    public function getCartData($request)
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect()->to(url('/'));
        }

        $coupon_code = $request->coupon_code ?? '';
        $freebees_id = $request->freebees_id ?? '';
        $slot_date = $request->slot_date ?? '';
        $subscription_id = $request->subscription_id ?? '';
        $slot_time = $request->slot_time ?? '';
        $delivery_type = $request->delivery_type ?? '';
        $applied_cashback = $request->applied_cashback ?? '';
        $cart_data = CustomHelper::cartData($user->id, $coupon_code, $request, $user);

        $cartValue = $cart_data['cartValue'] ?? null;
        $cart_price = $cartValue['cart_price'] ?? null;
        $cart_products = $cartValue['cart_products'] ?? '';
        $cart_products_category = $cartValue['cart_products_category'] ?? '';
        $cartArr = $cart_data['cart_list'] ?? '';
        $message = $cart_data['message'] ?? 'Successfully';
        $result = $cart_data['result'] ?? true;
        $free_delivery = CustomHelper::getFreeDeliveryAmount();
        $user_address = null;
        if (!empty($user->addressID)) {
            $user_address = UserAddress::where('id', $user->addressID)->first();
        }

        $recommendation_product = [];
        $apply_cashback = $request->apply_cashback ?? false;
        $recommendation = [];
        $last_order = [];
        $deal_1 = [];

        $last_order_data = Order::where('userID', $user->id)->latest()->limit(5)->pluck('id')->toArray();
        $order_items_id = OrderItems::whereIn('order_id', $last_order_data)->pluck('product_id')->toArray();

        if (!empty($order_items_id)) {
            $products = Product::whereIn('id', $order_items_id)->get();
            if (!empty($products)) {
                foreach ($products as $product) {
                    $last_order[] = self::getProductDetails($product->id, $user->id ?? '');
                }
            }
        }

        /////$recommendation////
        if (!empty($cart_products_category)) {
            $categories = Category::whereIn('id', $cart_products_category)->get();
            if (!empty($categories)) {
                foreach ($categories as $category) {
                    $product_ids = explode(",", $category->product_ids) ?? '';
                    if (!empty($product_ids)) {
                        foreach ($product_ids as $pro_id) {
                            $recommendation[] = self::getProductDetails($pro_id, $user->id ?? '');
                        }
                    }
                }
            }

        }


        $recommendation_product['recommendation'] = $recommendation;
        $recommendation_product['last_order'] = $last_order;
        $recommendation_product['deal_1'] = $deal_1;
        $date = $request->date ?? date('Y-m-d');
        $delivery_instructions = Setting::where('id', 1)->first()->delivery_instructions ?? '';
        $tips = ['10', '20', '30', '50', 'Others'];
        $delivery_details = [];
        $total_price = $cartValue['total_price'] ?? 0;
        $settings = Setting::where('id', 1)->first();
        $cashback_wallet = $user->cashback_wallet ?? 0;
        $max_applied_cashback = 0;
        $applied_cashback = 0;
        if ($cashback_wallet > 0) {
            $cashback_wallet_use = $settings->cashback_wallet_use ?? 0;
            if ($cashback_wallet_use > 0) {
                $applied_cashback = ($total_price * $cashback_wallet_use) / 100;
                if ($applied_cashback <= $cashback_wallet) {
                    $max_applied_cashback = (int)$applied_cashback;
                }
                if ($applied_cashback > $cashback_wallet) {
                    $max_applied_cashback = (int)$cashback_wallet;
                }
            }
        }
        $cartValue['applied_cashback'] = "0";
        if (!empty($cartValue)) {
            $cartValue['max_applied_cashback'] = (int)$max_applied_cashback;
            if (filter_var($apply_cashback, FILTER_VALIDATE_BOOLEAN)) {
                $cartValue['applied_cashback'] = (string)$max_applied_cashback;
                $cartValue['total_price'] = $total_price - (int)$max_applied_cashback;
            }
        }
        $freebees_product = [];

        $freebees_product = DB::table('freebees_product')
            ->where('from_amount', '<=', $cart_price)
            ->where('to_amount', '>=', $cart_price)
            ->where('is_delete', 0)
            ->get();
        if (!empty($freebees_product)) {
            foreach ($freebees_product as $pro) {
                $product = self::getProductDetails($pro->product_id, $user->id ?? '');
                $pro->product_name = $product->name ?? '';
                $pro->image = $product->image ?? '';
            }
        }
        $selected_freebees_product = null;
        if (!empty($freebees_id)) {
            $selected_freebees_product = DB::table('freebees_product')
                ->where('id', $freebees_id)->first();
            if (!empty($selected_freebees_product)) {
                $product = self::getProductDetails($selected_freebees_product->product_id, $user->id ?? '');
                $selected_freebees_product->product_name = $product->name ?? '';
                $selected_freebees_product->image = $product->image ?? '';
                $cartValue['total_price'] = (int)$cartValue['total_price'] + (int)$selected_freebees_product->amount;
            }
        }

        $delivery_data = null;
        if (!empty($user_address)) {
            $cart_price = $cartValue['cart_price'] ?? 0;
            // Express slot
            $expressSlot = null;

            $check = self::checkExpressEligible($user->id ?? '');
            if ($check) {
                $expressSlot = DB::table('delivery_charges')
                    ->where('type', 'express')
                    ->where('status', 1)
                    ->where('is_delete', 0)
                    ->whereRaw('? BETWEEN order_amount AND order_amount2', [$cart_price])
                    ->first();
            }

// Normal slot
            $normalSlot = DB::table('delivery_charges')
                ->where('type', 'normal')
                ->where('status', 1)
                ->where('is_delete', 0)
                ->whereRaw('? BETWEEN order_amount AND order_amount2', [$cart_price])
                ->first();

            $delivery_data['expressSlot'] = $expressSlot;
            $delivery_data['normalSlot'] = $normalSlot;
        }
        $subscription_plans = null;
        if (CustomHelper::checkSubscription($user) == 1) {
            $subscription_plans = SubscriptionPlans::where('is_delete', 0)->where('status', 1)->where('is_show', "0")->first();
        }

        $subscription_plans_new = [];
        if (CustomHelper::checkSubscription($user) == 1) {
            $subscription_plans_new = SubscriptionPlans::where('is_delete', 0)->where('status', 1)->orderBy('duration', "ASC")->get();
        }

        $delivery_details['delivery_time'] = 10;
        $nc_coins = self::getNcCashPercent($user, $cartValue['cart_price'] ?? '');
        $you_save = $cartValue['total_mrp_discount'] ?? 0;
        $data = [
            'result' => $result,
            'message' => $message,
            'cartValue' => $cartValue,
            'cart_list' => $cartArr,
            'user_address' => $user_address,
            'recommendation_product' => $recommendation_product,
            'tips' => $tips,
            'nc_coins' => $nc_coins,
            'you_save' => $you_save,
            'freebees_product' => $freebees_product,
            'delivery_data' => $delivery_data,
            'selected_freebees_product' => $selected_freebees_product,
            'subscription_plans' => $subscription_plans,
            'subscription_id' => $subscription_id,
            'subscription_plans_new' => $subscription_plans_new,
            'is_subscribe' => CustomHelper::checkSubscription($user),
        ];
        return $data;

    }

    public function checkExpressEligible($user_id)
    {
        if (!empty($user_id)) {
            $user = User::find($user_id);
            $latitude = $user->latitude ?? '17.44757253036007';
            $longitude = $user->longitude ?? '78.30504618870073';
            if (!empty($user->addressID)) {
                $address = UserAddress::where('id', $user->addressID)->first();
                if (!empty($address)) {
                    $latitude = $address->latitude ?? '17.44757253036007';
                    $longitude = $address->longitude ?? '78.30504618870073';
                }
            }
            $pincode = $user->pincode ?? '';
            if (empty($pincode)) {
                $pincode = $address->pincode ?? '';
            }
            $seller = self::getNearestSeller($latitude, $longitude, 2, 40);

            if (!empty($seller)) {
                return true;
            }
        }
        return false;
    }

    public function getCartHtml(Request $request)
    {
        $data = [];
        $user = Auth::user();
        if (empty($user)) {
            return back();
        }
        $productArr = [];
        $carts = Cart::where('user_id', $user->id)->get();
        if (!empty($carts)) {
            foreach ($carts as $cart) {
                $product_data = self::getCartProductDetails($cart, $user->id ?? '');
                if (!empty($product_data)) {
                    $productArr[] = $product_data;
                }
            }
        }

        $data['cart_products'] = $productArr;
        $cart_data = self::getCartData($request);
        $data['cart_data'] = $cart_data;
        $cartValue = $cart_data['cartValue'] ?? '';
        $applied_cashback = $cartValue['applied_cashback'] ?? '';
        $subscription_plans_new = self::getMembershipPlans($user->id);
        $data['subscription_plans_new'] = $subscription_plans_new;
        $html = view('home.cart_html', $data)->render();
        return response()->json(['html' => $html, 'cart_data' => $cart_data, 'applied_cashback' => $applied_cashback]);
    }

    public function getMembershipPlans($user_id)
    {
        if (empty($user_id)) {
            $cartValue['message'] = "User ID is required";
            return response()->json($cartValue, 200);
        }
        $user = User::where('id', $user_id)->first();
        $subscription_plansArr = [];
        if (CustomHelper::checkSubscription($user) == 0 && $user->is_ban == 0) {
            $subscription_plans = SubscriptionPlans::where('is_delete', 0)->where('status', 1)->orderBy('duration', "ASC")->get();
            if (!empty($subscription_plans)) {
                foreach ($subscription_plans as $subs_plan) {
                    if (!empty($subs_plan->max_applied_time)) {
                        $exist_count = Subscriptions::where('user_id', $user_id)->where('subscription_id', $subs_plan->id)->count();
                        if ($exist_count < $subs_plan->max_applied_time) {
                            $subscription_plansArr[] = $subs_plan;
                        }
                    } else {
                        $subscription_plansArr[] = $subs_plan;
                    }
                }
            }
        }

        return $subscription_plansArr;
    }

    public function createRazorpayOrder(Request $request)
    {
        $total_price = $request->total_price ?? '';
        $user = Auth::user();
        $orderData = self::generateRazorpayOrder($total_price, $user->id);
        $settings = CustomHelper::razorpayKey();
        return response()->json(['orderData' => $orderData, 'razopayKeys' => $settings]);
    }


    private function generateRazorpayOrder($price, $user_id)
    {
        $payment_data = [
            'receipt' => 'order_rcpt_' . time(),
            "amount" => $price * 100,
            "currency" => "INR",
            'payment_capture' => 1,
            "notes" => [
                "user_id" => $user_id
            ]
        ];
        $settings = CustomHelper::razorpayKey();
        $key = $settings['key'] ?? '';
        $secret = $settings['secret'] ?? '';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.razorpay.com/v1/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payment_data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($key . ':' . $secret)
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }


    public function stores(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $latitude = session('latitude') ?? '17.449605321963755';
        $longitude = session('longitude') ?? '78.30484842205092';
        $data = [];
        $sellers_list = [];
        $lat = $request->lat ?? $latitude;
        $lon = $request->long ?? $longitude;
        $search = $request->search ?? '';
        if (empty($lat)) {
            $lat = $user->latitude ?? '';
        }
        if (empty($lon)) {
            $lat = $user->latitude ?? '';
        }
        if (empty($lat) || empty($lon)) {

        }
        $haversine = "(6371 * acos(cos(radians($lat))
                        * cos(radians(latitude))
                        * cos(radians(longitude)
                        - radians($lon))
                        + sin(radians($lat))
                        * sin(radians(latitude))))";
        $sellers = Vendors::select('id', 'name', 'image', 'user_phone', 'address', 'image', 'avg_rating', 'total_rating', 'payment_method', 'delivery_time', 'radius', 'open_time', 'close_time', 'latitude', 'longitude')->selectRaw("$haversine AS distance");
        //        ->havingRaw("distance < ?", [$radius]);
        if (!empty($search)) {
            $sellers->where('name', 'like', '%' . $search . '%');
        }
        $sellers = $sellers->where('status', 1)->where('is_delete', 0)->orderBy('distance')->paginate(20);
        if (!empty($sellers)) {
            foreach ($sellers as $seller) {
                $is_deliver = 0;
                $seller->distance = number_format((float)$seller->distance, 2, '.', '');
                if ((float)$seller->distance <= (float)$seller->radius) {
                    $is_deliver = 1;
                }
                $seller->image = CustomHelper::getImageUrl('sellers', $seller->image);
                $payment_method = $seller->payment_method ?? '';
                $seller->is_deliver = $is_deliver;
                $seller->delivery_time = $seller->delivery_time ?? '';
                $seller->phone = $seller->user_phone ?? '';
                $seller->payment_method = $payment_method;
                $seller->open_time = date('h:i A', strtotime($seller->open_time)) ?? '';
                $seller->close_time = date('h:i A', strtotime($seller->close_time)) ?? '';
//                if ($is_deliver == 1) {
                $sellers_list[] = $seller;
//                }
            }
        }
        $data['sellers_list'] = $sellers_list;

        return view('home.stores', $data);
    }

    public function nutrapass(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $user = Auth::guard('user')->user();

        $data['user'] = $user;
        return view('home.nutrapass', $data);
    }

    public function suppliment_recommendation(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.suppliment_recommendation', $data);
    }

    public function suppliment_recommendation_list(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.suppliment_recommendation_list', $data);
    }

    public function my_orders(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $user = Auth::user();

        $ordersArr = [];
        $orders = Order::select('id', 'created_at', 'status', 'total_amount')->where('userID', $user->id)->where('is_delete', 0)->latest();
        $orders = $orders->paginate(30);
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $order_items = CustomHelper::getOrderItemsWithProduct($order->id);
                if (!empty($order_items) && count($order_items) > 0) {
                    $count_order_items = count($order_items);
                    $order->order_date_time = date('d M Y h:i A', strtotime($order->created_at));
                    $order->count_order_items = $count_order_items;
                    $first_product_name = '';
                    $image = '';

                    if ($count_order_items > 1) {
                        $first_product_name = $order_items[0]['name'] ?? '';
                        $product_id = $order_items[0]['id'] ?? '';
                        $product = Product::where('id', $product_id)->first();
                        $image = CustomHelper::getImageUrl('products', $product->image ?? '');
                        $minus_1 = $count_order_items - 1;
                        $first_product_name .= ' & ' . $minus_1 . " More.";
                    } else {
                        $first_product_name = $order_items[0]['name'] ?? '';
                        $product_id = $order_items[0]['id'] ?? '';
                        $product = Product::where('id', $product_id)->first();
                        $image = CustomHelper::getImageUrl('products', $product->image ?? '');
                    }

                    $order->first_product_name = $first_product_name;
                    $order->image = $image;

                    $my_ratings = DB::table('order_ratings')->where('user_id', $user->id)->where('order_id', $order->id)->first();

                    $order->my_ratings = $my_ratings;

                }
            }
        }
        $data['orders'] = $orders;


        return view('home.my_orders', $data);
    }

    public function order_details(Request $request)
    {
        $order_id = $request->id ?? '';
        $data = [];
        $user = Auth::user();
        $data['id'] = $order_id;
        $orders = Order::where('userID', $user->id)->where('id', $order_id)->where('is_delete', 0)->first();
        if (!empty($orders)) {
            $order_items = CustomHelper::getOrderItemsWithProduct($orders->id);
            if (!empty($order_items)) {
                foreach ($order_items as $order_item) {
                    $varients = VendorProductPrice::where('id', $order_item['variant_id'])->first();
                    $product = Product::where('id', $order_item['id'])->first();
                    $image = CustomHelper::getImageUrl('products', $product->image ?? '');
                    $order_item->subscription_price = $varients->subscription_price ?? 0;
                    $order_item->mrp = $varients->mrp ?? 0;
                    $order_item->unit = $varients->unit ?? 0;
                    $order_item->unit_value = $varients->unit_value ?? 0;
                    $images = [];
                    if (!empty($product)) {
                        $images = ProductImage::select('id', 'image')->where('product_id', $product->id)->where('status', 1)->where('is_delete', 0)->first();
                        if (!empty($images)) {
                            $images = CustomHelper::getImageUrl('products', $images->image);
                        }
                    }
                    $order_item->images = $images;
                    $order_item->image = $image;
                    $my_ratings = [];
                    if (!empty($order_item->order_items_id)) {
                        $my_ratings = DB::table('order_ratings')->where('user_id', $user->id)->where('item_id', $order_item->order_items_id)->where('order_id', $orders->id)->first();

                    }
                    $order_item->ratings = $my_ratings;
                }
            }

            $count_order_items = count($order_items);
            $orders->order_date_time = date('d M Y h:i A', strtotime($orders->created_at));
            $orders->count_order_items = $count_order_items;
            $first_product_name = '';
            $image = '';

            if ($count_order_items > 1) {
                $first_product_name = $order_items[0]['name'] ?? '';
                $product_id = $order_items[0]['id'] ?? '';
                $product = Product::where('id', $product_id)->first();
                $minus_1 = $count_order_items - 1;
                $first_product_name .= ' & ' . $minus_1 . " More.";
            } else {
                $first_product_name = $order_items[0]['name'] ?? '';
                $product_id = $order_items[0]['id'] ?? '';
                $product = Product::where('id', $product_id)->first();
            }

            $orders->first_product_name = $first_product_name;


            $orders->order_items = $order_items;
            $address = DB::table('user_address')->where('id', $orders->address_id)->first();
            $orders->address = $address;
            $my_ratings = DB::table('order_ratings')->where('user_id', $user->id)->where('order_id', $orders->id)->first();
            $orders->my_ratings = $my_ratings;
            $orders->order_date_time = date('d M Y h:i A', strtotime($orders->created_at));
            $payment_method = $orders->payment_method ?? '';
            if ($orders->payment_method == 'cod' || $orders->payment_method == 'COD') {
                $payment_method = 'COD';
            }

            $orders->payment_method = $payment_method;
            $seller_details = self::getSellerDetails($orders->vendor_id, $user->id);


            $agent_details = self::getDeliveryBoyDetails($orders->agent_id ?? '');
            $orders->agent_details = $agent_details;
            $time_data = null;

            $orders->time_data = $time_data;

        } else {
            return back();
        }


        $selected_freebees_product = null;
        if (!empty($orders->freebees_id)) {
            $selected_freebees_product = DB::table('freebees_product')
                ->where('id', $orders->freebees_id)->first();
            if (!empty($selected_freebees_product)) {
                $product = self::getProductDetails($selected_freebees_product->product_id, $user->id ?? '');
                $selected_freebees_product->product_name = $product->name ?? '';
                $selected_freebees_product->image = $product->image ?? '';
                $selected_freebees_product->amount = $orders->freebees_price ?? '';
            }
        }

        $seller_details = [];

        $data['orders'] = $orders;
        $data['selected_freebees_product'] = $selected_freebees_product;
        $data['order_status'] = CustomHelper::getOrderStatusData($order_id);
        $data['seller_details'] = $seller_details;
        $data['is_return'] = self::checkReturn($orders);
        return view('home.order_details', $data);
    }

    public function getDeliveryBoyDetails($delivery_boy_id)
    {
        $agents = DB::table('delivery_agent')->where('id', $delivery_boy_id)->first();
        if (!empty($agents)) {
            $agents->image = CustomHelper::getImageUrl('agents', $agents->image);
        }
        return $agents;
    }


    public function getSellerDetails($seller_id, $user_id)
    {
        $sellersData = null;
        $user_data = User::find($user_id);
        $address = [];
        if (!empty($user_data)) {
            $address = UserAddress::find($user_data->addressID);
        }
        $lat = $address->latitude ?? '';
        $lon = $address->longitude ?? '';
        $seller = [];
        if (!empty($lat) && !empty($lon)) {
            $haversine = "(6371 * acos(cos(radians($lat))
                        * cos(radians(latitude))
                        * cos(radians(longitude)
                        - radians($lon))
                        + sin(radians($lat))
                        * sin(radians(latitude))))";
            $sellers = Vendors::select('id', 'name', 'image', 'address', 'image', 'avg_rating', 'total_rating', 'payment_method', 'delivery_time', 'radius', 'open_time', 'close_time', 'latitude', 'longitude')->selectRaw("$haversine AS distance");
            //        ->havingRaw("distance < ?", [$radius]);

            $sellers->where('id', $seller_id);

            $seller = $sellers->where('status', 1)->where('is_delete', 0)->first();

        }
        if (!empty($seller)) {

            $is_deliver = 0;
            $seller->distance = number_format((float)$seller->distance, 2, '.', '');
            if ((float)$seller->distance <= (float)$seller->radius) {
                $is_deliver = 1;
            }
            $seller->image = CustomHelper::getImageUrl('sellers', $seller->image);
            $payment_method = $seller->payment_method ?? '';
            $seller->is_deliver = $is_deliver;
            $seller->delivery_time = $seller->delivery_time ?? '';
            $seller->open_time = date('h:i A', strtotime($seller->open_time)) ?? '';
            $seller->close_time = date('h:i A', strtotime($seller->close_time)) ?? '';
            $seller->payment_method = $payment_method;
            $sellersData = $seller;
        }

        return $sellersData;
    }

    public function checkReturn($order)
    {
        $canReturn = false;
        if (!empty($order)) {
            $createdAt = $order->created_at ?? '';
            $returnDeadline = $createdAt->copy()->addDays(2);
            if (now()->lessThanOrEqualTo($returnDeadline)) {
                if ($order->status == 'DELIVERED') {
                    $canReturn = true;  // Return is possible
                }

            } else {
                $canReturn = false; // Return not possible
            }
        }
        return $canReturn;

    }

    public function explore(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $categories = Category::where('status', 1)->where('parent_id', 0)->where('is_goal', 0)->where('is_delete', 0)->where('status', 1)->orderBy('priority', 'ASC')->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $category->image = CustomHelper::getImageUrl('categories', $category->image ?? '');
            }
        }

        $goalcategories = Category::where('status', 1)->where('parent_id', 0)->where('is_goal', 1)->where('is_delete', 0)->where('status', 1)->orderBy('priority', 'ASC')->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
        if (!empty($goalcategories)) {
            foreach ($goalcategories as $category) {
                $category->image = CustomHelper::getImageUrl('categories', $category->image ?? '');
            }
        }

        $data['categories'] = $categories;
        $brands = Brand::where('status', 1)->where('is_delete', 0)->where('status', 1)->orderBy('priority', "ASC")->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
        if (!empty($brands)) {
            foreach ($brands as $brand) {
                $brand->icon = CustomHelper::getImageUrl('brands', $brand->brand_img);
                $brand->image = CustomHelper::getImageUrl('brands', $brand->brand_img);
                $brand->brand_img = CustomHelper::getImageUrl('brands', $brand->brand_img);
                $brand->brand_icon = CustomHelper::getImageUrl('brands', $brand->brand_img);
                $brand->certificate = CustomHelper::getImageUrl('brands', $brand->certificate);
            }
        }
        $data['brands'] = $brands;
        $data['goalcategories'] = $goalcategories;


        return view('home.explore', $data);
    }

    public function store_location(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        session([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'pincode' => $request->pincode,
            'address' => $request->address
        ]);

        return response()->json(['status' => 'success']);
    }

    public function profile(Request $request)
    {
        $data = [];
        $method = $request->method();
        $user = Auth::user();
        if ($method == 'post' || $method == "POST") {
            $userData = User::find($user->id);
            if (!empty($request->name)) {
                $userData->name = $request->name;
            }
            if (!empty($request->email)) {
                $userData->email = $request->email;
            }
            if (!empty($request->gender)) {
                $userData->gender = $request->gender;
            }
            if (!empty($request->dob)) {
                $userData->dob = $request->dob;
            }
            if (!empty($request->height)) {
                $userData->height = $request->height;
            }
            if (!empty($request->weight)) {
                $userData->weight = $request->weight;
            }
            if (!empty($request->health_profile)) {
                $userData->health_profile = $request->health_profile;
            }
            if (!empty($request->activity)) {
                $userData->activity = $request->activity;
            }
            if (!empty($request->food_choice)) {
                $userData->food_choice = $request->food_choice;
            }
            if (!empty($request->address)) {
                $userData->address = $request->address;
            }

            if (!empty($request->state_id)) {
                $userData->state_id = $request->state_id;
            }
            if (!empty($request->city_id)) {
                $userData->city_id = $request->city_id;
            }
            if (!empty($request->addressID)) {
                $userData->addressID = $request->addressID;
            }

            if (!empty($request->latitude)) {
                $userData->latitude = $request->latitude;
            }
            if (!empty($request->longitude)) {
                $userData->longitude = $request->longitude;
            }
            if (!empty($request->seller_id)) {
                $userData->seller_id = $request->seller_id;
            }

            if (!empty($request->pincode)) {
                $userData->pincode = $request->pincode;
            }
            if (!empty($request->aniversery)) {
                $userData->aniversery = $request->aniversery;
            }
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = CustomHelper::UploadImage($file, 'users');
                $userData->image = $fileName;
            }
            $userData->save();
            return back();
        }

        return view('users.profile', $data);
    }

    public function nc_cash(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('users.nc_cash', $data);
    }

    public function privacy_policy(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.privacy_policy', $data);
    }

    public function return_policy(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.return_policy', $data);
    }

    public function shipping_policy(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.shipping_policy', $data);
    }

    public function terms(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.terms', $data);
    }

    public function transactions(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('users.transactions', $data);
    }

    public function address(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('users.address', $data);
    }

    public function giftcard(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('users.giftcard', $data);
    }

    public function refer_earn(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('users.refer_earn', $data);
    }

    public function coupons(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $user = Auth::user();
        $offersArr = [];
        $search = $request->search ?? '';
        $offers = [];
        $vendor_id = $user->seller_id ?? '';
        /////User Coupons
        if (!empty($user)) {
            $offers = Offers::where('status', 1)->where('is_show', 1)->where('user_id', $user->id);
            if (!empty($search)) {
                $offers->where('offer_code', 'like', '%' . $search . '%');
            }
            $offers = $offers->whereDate('end_date', '>=', date('Y-m-d'))->get();
        }
        if (!empty($offers)) {
            foreach ($offers as $offer) {
                $offer->is_expired = 0;
                $offer->image = CustomHelper::getImageUrl('offers', $offer->image);
                $product_ids = explode(",", $offer->product_ids ?? '');
                $productsArr = [];
                $proarr = Product::where('status', 1)->whereIn('id', $product_ids)->latest()->get();
                if (!empty($proarr)) {
                    foreach ($proarr as $product) {
                        $pro_data = self::getProductDetails($product->id, $user->id ?? '');
                        if (!empty($pro_data)) {
                            $productsArr[] = $pro_data;
                        }
                    }
                }
                $offer->products = $productsArr;
                $offersArr[] = $offer;
            }
        }


        /////Admin Global Coupons

        $offers = Offers::where('status', 1)->where('is_show', 1);
        if (!empty($search)) {
            $offers->where('offer_code', 'like', '%' . $search . '%');
        }
        $offers = $offers->whereDate('end_date', '>=', date('Y-m-d'))->get();
        if (!empty($offers)) {
            foreach ($offers as $offer) {
                $offer->is_expired = 0;
                $offer->image = CustomHelper::getImageUrl('offers', $offer->image);
                $product_ids = explode(",", $offer->product_ids ?? '');
                $productsArr = [];
                $proarr = Product::where('status', 1)->whereIn('id', $product_ids)->latest()->get();
                if (!empty($proarr)) {
                    foreach ($proarr as $product) {
                        $pro_data = self::getProductDetails($product->id, $user->id ?? '');
                        if (!empty($pro_data)) {
                            $productsArr[] = $pro_data;
                        }
                    }
                }
                $offer->products = $productsArr;
                if (empty($offer->user_id)) {
                    $offersArr[] = $offer;
                }
            }
        }
        /////Vendor Coupons
        $offers = Offers::where('status', 1)->where('vendor_id', $vendor_id);
        if (!empty($search)) {
            $offers->where('offer_code', 'like', '%' . $search . '%');
        }
        $offers = $offers->whereDate('end_date', '>=', date('Y-m-d'))->get();
        if (!empty($offers)) {
            foreach ($offers as $offer) {
                $offer->is_expired = 0;
                $offer->image = CustomHelper::getImageUrl('offers', $offer->image);
                $product_ids = explode(",", $offer->product_ids ?? '');
                $productsArr = [];
                $proarr = Product::where('status', 1)->whereIn('id', $product_ids)->latest()->get();
                if (!empty($proarr)) {
                    foreach ($proarr as $product) {
                        $pro_data = self::getProductDetails($product->id, $user->id ?? '');
                        if (!empty($pro_data)) {
                            $productsArr[] = $pro_data;
                        }
                    }
                }
                $offer->products = $productsArr;
                if (empty($offer->user_id)) {
                    $offersArr[] = $offer;
                }
            }
        }

        $banners = Banner::where('status', 1)->where('type', 'offers')->where('is_delete', 0)->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
        if (!empty($banners)) {
            foreach ($banners as $banner) {
                $banner->banner_img = CustomHelper::getImageUrl('banners', $banner->banner_img);
                $product_id = explode(",", $banner->product_id);
                $productsArr = [];
                if (!empty($product_id)) {
                    foreach ($product_id as $prod_id) {
                        $pro_data = self::getProductDetails($prod_id, $user->id ?? '');
                        if (!empty($pro_data)) {
                            $productsArr[] = $pro_data;
                        }
                    }
                }
                $banner->products = $productsArr;
            }
        }
        $data['offers'] = $offersArr;
        $data['banners'] = $banners;
        return view('users.coupons', $data);
    }

    public function about(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.about', $data);
    }

    public function contact(Request $request)
    {
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $dbArray = [];
            $dbArray['name'] = $request->name ?? '';
            $dbArray['email'] = $request->email ?? '';
            $dbArray['telephone'] = $request->telephone ?? '';
            $dbArray['subject'] = $request->subject ?? '';
            $dbArray['message'] = $request->message ?? '';

            $htmlContent = view('emails.contact', $dbArray)->render();

            $emaildata = [
                "to_email" => "support@nutracore.in",
                "name" => $request->name ?? '',
                "subject" => $request->subject ?? '',
                "htmlContent" => $htmlContent ?? '',
            ];

            $success = CustomHelper::sendEmailApi($emaildata);

            return back();
        }
        $data = [];
        $latitude = session('latitude') ?? '17.449605321963755';
        $longitude = session('longitude') ?? '78.30484842205092';
        $data = [];
        $sellers_list = [];
        $lat = $request->lat ?? $latitude;
        $lon = $request->long ?? $longitude;
        $search = $request->search ?? '';
        if (empty($lat)) {
            $lat = $user->latitude ?? '';
        }
        if (empty($lon)) {
            $lat = $user->latitude ?? '';
        }
        if (empty($lat) || empty($lon)) {

        }
        $haversine = "(6371 * acos(cos(radians($lat))
                        * cos(radians(latitude))
                        * cos(radians(longitude)
                        - radians($lon))
                        + sin(radians($lat))
                        * sin(radians(latitude))))";
        $sellers = Vendors::select('id', 'name', 'image', 'user_phone', 'address', 'image', 'avg_rating', 'total_rating', 'payment_method', 'delivery_time', 'radius', 'open_time', 'close_time', 'latitude', 'longitude')->selectRaw("$haversine AS distance");
        //        ->havingRaw("distance < ?", [$radius]);
        if (!empty($search)) {
            $sellers->where('name', 'like', '%' . $search . '%');
        }
        $sellers = $sellers->where('status', 1)->where('is_delete', 0)->orderBy('distance')->paginate(20);
        if (!empty($sellers)) {
            foreach ($sellers as $seller) {
                $is_deliver = 0;
                $seller->distance = number_format((float)$seller->distance, 2, '.', '');
                if ((float)$seller->distance <= (float)$seller->radius) {
                    $is_deliver = 1;
                }
                $seller->image = CustomHelper::getImageUrl('sellers', $seller->image);
                $payment_method = $seller->payment_method ?? '';
                $seller->is_deliver = $is_deliver;
                $seller->delivery_time = $seller->delivery_time ?? '';
                $seller->phone = $seller->user_phone ?? '';
                $seller->payment_method = $payment_method;
                $seller->open_time = date('h:i A', strtotime($seller->open_time)) ?? '';
                $seller->close_time = date('h:i A', strtotime($seller->close_time)) ?? '';
//                if ($is_deliver == 1) {
                $sellers_list[] = $seller;
//                }
            }
        }
        $data['sellers_list'] = $sellers_list;

        return view('home.contact', $data);
    }

    public function deals(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.deals', $data);
    }

    public function wishlist(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $user = Auth::user();
        $products = [];
        if (!empty($user)) {
            $product_ids = Wishlist::where('user_id', $user->id)->groupBy('product_id')->pluck('product_id')->toArray();
            if (!empty($product_ids)) {
                foreach ($product_ids as $product_id) {
                    $product = self::getProductDetails($product_id, $user->id);
                    if (!empty($product)) {
                        $products[] = $product;
                    }
                }
            }
        }
        $data['products'] = $products;

        return view('home.wishlist', $data);
    }

    public function getEstimateDelivery(Request $request)
    {
        $delivery_data = CustomHelper::checkDelivery($request->pincode);
        $estimate_delivery = '';
        $message = '';
        if (!empty($delivery_data)) {
            $delivery_data = json_decode($delivery_data, true);
            $message = $delivery_data['message'] ?? '';
            $data = $delivery_data['data'] ?? '';
            $recommended_courier_company_id = $data['recommended_courier_company_id'] ?? '';
            $available_courier_companies = $data['available_courier_companies'] ?? '';
            if (!empty($available_courier_companies)) {
                foreach ($available_courier_companies as $available_courier_company) {
                    if ($available_courier_company['courier_company_id'] == $recommended_courier_company_id) {
                        $estimate_delivery = $available_courier_company['etd'] ?? '';
                    }
                }
            }


        }
        return ['status' => true, 'delivery_data' => $estimate_delivery, 'message' => $message];
    }

    public function sendOTP(Request $request)
    {
        $phone = $request->phone ?? '';
        if ($phone == '7065452862' || $phone == '6370371406') {
            $otp = 1234;
        } else {
            // $otp = rand(1111, 9999);
            $otp = 1234;
        }
        $expired_at = Carbon::now()->addMinutes(10);
        User::updateOrCreate(
            ['phone' => $phone],
            [
                'device_id' => $request->device_id ?? '',
                'device_token' => $request->device_token ?? '',
                'otp' => $otp,
                'expired_at' => $expired_at,
                'name' => $request->name ?? 'Guest',
                'email' => $request->email ?? uniqid() . '@example.com',
            ]
        );


        $exist = User::where(['phone' => $phone])->first();
        if (!empty($exist)) {
            // $role_id = $exist->role_id;
            // if (empty($exist->referral_code)) {
            //     $referral_code_val = self::getReferalCode(8);
            //     $exist->referral_code = $referral_code_val;
            //     $exist->save();
            // }
        }

        return response()->json([
            'result' => true,
            'message' => 'OTP Sent',
            // 'response' => $response,
        ], 200);
    }


    function getReferalCode($length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = md5(uniqid(rand(), true)) . $characters;
        $randomString = substr(str_shuffle($characters), 0, $length);
        return "NC" . strtoupper($randomString);
    }

    public function login(Request $request)
    {
        $otp = $request->otp ?? '';
        $phone = $request->phone ?? '';
        $user = User::where(['phone' => $phone])->where('is_delete', 0)->first();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => 'User Not Found',
            ], 200);
        }
        $success = User::where(['phone' => $phone, 'otp' => $otp])->where('is_delete', 0)->first();

        if ($success) {
            Auth::loginUsingId($success->id);
            return response()->json([
                'result' => true,
                'message' => 'Login SuccessFully',
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => 'User Not Found',
            ], 200);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        return back();
    }

    public function addToCart(Request $request)
    {
        $user = Auth::user();
        $product_id = $request->product_id ?? '';
        $variant_id = $request->variant_id ?? '';
        $qty = $request->qty ?? '';
        $product = Products::where('id', $product_id)->first();
        if (!empty($product)) {
            $check_varient = CustomHelper::checkProductPrice($product_id, $variant_id);
            if (empty($check_varient) && $variant_id != 0) {
                return response()->json([
                    'result' => false,
                    'message' => 'Product Not Available',
                ], 200);
            }
            $exist = Cart::where(['product_id' => $product_id, 'variant_id' => $variant_id, 'user_id' => $user->id])->first();
            $dbArray = [];
            $dbArray['product_id'] = $product_id;
            $dbArray['variant_id'] = $variant_id;
            $dbArray['user_id'] = $user->id;
            $dbArray['qty'] = $qty;
            if (empty($exist)) {
                if ($qty > 0) {
                    Cart::insert($dbArray);
                }
            } else {
                if ($qty <= 0) {
                    Cart::where('id', $exist->id)->delete();
                }
                if ($qty > 0) {
                    Cart::where('id', $exist->id)->update($dbArray);
                }
            }
        }
        return response()->json([
            'result' => true,
            'message' => 'Cart Updated SuccessFully',
        ], 200);
    }

    public function update_selected_address(Request $request)
    {
        $user = Auth::user();
        $addressID = $request->addressID ?? '';
        User::where('id', $user->id)->update(['addressID' => $addressID]);
        $address = UserAddress::where('user_id', $user->id)->where('id', $addressID)->first();
        $envia_data = json_decode($address->envia_data);

        $stateName = $envia_data->state->name ?? '';
        return json_encode(['address' => $address, 'stateName' => $stateName]);

    }

    public function save_address_old(Request $request)
    {
        $user = Auth::user();
        $address = new UserAddress();
        $address->user_id = $user->id;
        $address->address_type = $request->address_type;
        $address->flat_no = $request->flat_no;
        $address->building_name = $request->building_name;
        $address->landmark = $request->landmark;
        $address->pincode = $request->pincode;
        $address->latitude = $request->latitude;
        $address->longitude = $request->longitude;
        $address->pincode = $request->pincode;
        $address->location = $request->location;
        $address->save();
        User::where('id', $user->id)->update(['addressID' => $address->id]);
        return response()->json(['address' => $address]);
    }

    public function save_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "pincode" => "required",
            "latitude" => "required",
            "longitude" => "required",
            "location" => "required",
            "flat_no" => "required",
            "contact_person_name" => "required",
            "contact_person_mobile" => "required",
        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        $user = Auth::user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        if ($user->phone == "9999999999") {
            return response()->json([
                'result' => false,
                'message' => 'Unauthorised',
            ], 401);
        }
        $dbArray = [];
        $addressID = 0;
        $id = $request->id ?? '';
        $dbArray['user_id'] = $user->id ?? '';
        $dbArray['location'] = $request->location ?? '';
        $dbArray['flat_no'] = $request->flat_no ?? '';
        $dbArray['building_name'] = $request->building_name ?? '';
        $dbArray['landmark'] = $request->landmark ?? '';
        $dbArray['address_type'] = $request->address_type ?? '';
        $dbArray['pincode'] = $request->pincode ?? '';
        $dbArray['latitude'] = $request->latitude ?? '';
        $dbArray['longitude'] = $request->longitude ?? '';
        $dbArray['contact_person_name'] = $request->contact_person_name ?? '';
        $dbArray['contact_person_mobile'] = $request->contact_person_mobile ?? '';
        $dbArray['is_delete'] = $request->is_delete ?? 0;
        $dbArray['note'] = $request->note ?? '';
        $dbArray['is_active'] = 'Y';

        if ($request->is_default == 'Y') {
            DB::table('user_address')->where('user_id', $user->id)->update(['is_default' => 'N']);
        }

        $dbArray['is_default'] = $request->is_default ?? 'N';
        if (!empty($id)) {
            DB::table('user_address')->where('id', $id)->update($dbArray);
            User::where('id', $user->id)->update(['addressID' => $id, 'estimated_day' => '']);
            $addressID = $id;
        } else {
            $addressID = DB::table('user_address')->insertGetId($dbArray);
            User::where('id', $user->id)->update(['addressID' => $addressID, 'latitude' => $request->latitude, 'longitude' => $request->longitude, 'estimated_day' => '']);
        }

        if (!empty($request->pincode)) {
            $response = CustomHelper::getPincodeDataEnvia($request->pincode);
            if (!empty($response)) {
                $response = $response[0] ?? '';
                $dbArray = [];
                $variable = '2digit';
                $dbArray['country'] = $response->country->code ?? '';
                $dbArray['state'] = $response->state->code->$variable ?? '';
                $dbArray['envia_data'] = json_encode($response) ?? '';
                UserAddress::where('id', $addressID)->update($dbArray);
            }
        }
        User::where('id', $user->id)->update(['addressID' => $addressID]);
        $address = UserAddress::where('id', $addressID)->first();
        return response()->json(['address' => $address]);
    }


//    vikas kumar
    public function TimeToGainMoreVikas()
    {
        return view('home.TimeToGainMoreVikas');
    }

    public function place_order(Request $request): \Illuminate\Http\JsonResponse
    {

        $messages = [
            'selected_addressID.not_in' => 'Please select a valid address.',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'selected_addressID' => 'required|not_in:0',
            'payment_method' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        $user = Auth::user();

        $address_id = $request->selected_addressID ?? '';
        $address = UserAddress::where('id', $address_id)->first();
        if (!empty($address)) {
            if (empty($address->landmark) || empty($address->pincode) || empty($address->latitude) || empty($address->longitude)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Please Add a New Address ! This is  Imcomplete Address',
                ], 200);
            }
        } else {
            return response()->json([
                'result' => false,
                'message' => 'Address Required',
            ], 200);
        }


        $lockKey = 'place_order_' . $user->id;

        // Try to acquire the lock for 5 seconds
        if (Cache::has($lockKey)) {
            return response()->json([
                'result' => false,
                'message' => 'Order is being processed. Please wait a few seconds.',
            ], 200); // Too Many Requests
        }

        // Set lock for 5 seconds
        Cache::put($lockKey, true, now()->addSeconds(5));

        try {
            $coupon_code = $request->coupon_code ?? '';
            $handling_charges = $request->handling_charges ?? '';
            $payment_method = $request->payment_method ?? '';
            $seller_id = $request->seller_id ?? '';
            $tips = $request->tips ?? '';
            $seller_id = $request->seller_id ?? '';
            $giftcard_code = $request->giftcard_code ?? '';
            $image = '';


            $wallet_applied = $request->wallet_applied ?? false;
            $apply_cashback = $request->apply_cashback ?? false;
            $cashback_wallet = $request->cashback_wallet ?? 0;
            $applied_cashback = $request->applied_cashback ?? 0;
            $subscription_id = $request->subscription_id ?? 0;
            $tips = (int)$request->tips ?? 0;
            $cart_data = CustomHelper::cartData($user->id, $coupon_code, $request, $user);
            $online_payment = null;

            $order_id = 0;
            if (!empty($cart_data)) {
                $cartValue = $cart_data['cartValue'] ?? '';
                $cart_list = $cart_data['cart_list'] ?? '';
                $image = $cartValue['image'] ?? '';
                if (empty($cart_list)) {
                    return response()->json([
                        'result' => false,
                        'message' => 'Cart is Empty',
                    ], 200);
                }

                $order_amount = $cartValue['total_price'] ?? 0;
                $applied_wallet_amount = 0;
                $online_amount = 0;
                if ($wallet_applied) {
                    if ($payment_method == 'COD' || $payment_method == 'cod') {
                        $wallet = $user->wallet ?? 0;
                        $order_id = $this->saveOrders($request, $cart_data, $user->id, 'COD', $wallet);
                        if ($order_id) {
                            self::sendOrderNotification($order_id);
                            Cart::where('user_id', $user->id)->delete();
                        }
                        CustomHelper::sendPlaceNewOrder($user->phone ?? '', $order_id);
                        $event = 'Place Order';
                        $traits = [

                        ];
                        CustomHelper::trackEvent($user->id, $event, $traits);
                    }
                    if ($payment_method == 'ONLINE' || $payment_method == 'online') {
                        $wallet = $user->wallet ?? 0;
                        $total_price = $cartValue['total_price'] ?? 0;
                        $order_id = $this->saveOrders($request, $cart_data, $user->id, 'online', $wallet);
                        if ((int)$user->wallet <= $total_price) {
                            $online_amount = (int)$total_price - (int)$user->wallet;
                        }
                        $request['amount'] = $online_amount + $tips;
                        $request['type'] = 'order';
                        $request['order_id'] = $order_id;
                        $request['subscription_id'] = $subscription_id;
                        $online_payment = $this->create_payment($request);
                        if ($order_id) {
                            //                        Cart::where('user_id', $user->id)->delete();
                        }
                    }
                } else {
                    if ($payment_method == 'COD' || $payment_method == 'cod') {
                        $order_id = $this->saveOrders($request, $cart_data, $user->id, 'COD', $seller_id);
                        if ($order_id) {
                            self::sendOrderNotification($order_id);
                            Cart::where('user_id', $user->id)->delete();
                            CustomHelper::sendPlaceNewOrder($user->phone ?? '', $order_id);
                            $event = 'Place Order';
                            $traits = [

                            ];
                            CustomHelper::trackEvent($user->id, $event, $traits);
                        }
                    }
                    if ($payment_method == 'ONLINE' || $payment_method == 'online') {
                        $order_id = $this->saveOrders($request, $cart_data, $user->id, 'online', $seller_id);
                        $tota_price = $cartValue['total_price'] ?? 0;
                        $tota_price -= (int)$applied_cashback;
                        $request['amount'] = $tota_price + (int)$tips + (int)$handling_charges;
                        $request['type'] = 'order';
                        $request['order_id'] = $order_id;
                        $request['subscription_id'] = $subscription_id;
                        $online_payment = $this->create_payment($request);
                        if ($order_id) {
                            //                        Cart::where('user_id', $user->id)->delete();
                        }
                    }
                }
            }

            ///
            // $token = $user->device_token ?? '';
            // $not = CustomHelper::getNotifyData('place_order');
            // $description = $not->description ?? '';
            // $description = str_replace("##order_id##", $order_id, $description);
            // $data = [
            //     'orderID' => $order_id,
            //     'title' => $not->title ?? '',
            //     'body' => $description,
            //     'image' => $image,
            // ];
            // $sucess = null;
            // if (!empty($token)) {
            //     // $sucess = CustomHelper::fcmNotification($token, $data);
            // }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
            ], 200);
        } finally {
            Cache::forget($lockKey);
        }


        return response()->json([
            'result' => true,
            'message' => "Order Placed Successfully",
            'online_payment' => $online_payment->original ?? null,
            'order_id' => $order_id,

        ], 200);
    }

    public function saveOrders($request, $cart_data, $user_id, $payment_method, $seller_id, $wallet = 0)
    {

        $order_id = 0;
        $user_data = User::find($user_id);
        if (!empty($cart_data)) {
            $freebees_price = 0;
            if (!empty($request->freebees_id)) {
                $freebees_pro = DB::table('freebees_product')->where('id', $request->freebees_id)->first();
                if (!empty($freebees_pro)) {
                    $freebees_price = $freebees_pro->amount ?? 0;
                }
            }
            $wallet_applied = (bool)$request->apply_cashback ?? false;
            $address = UserAddress::where('id', $request->address_id)->first();
            $cartValue = $cart_data['cartValue'] ?? '';
            $cart_list = $cart_data['cart_list'] ?? '';

            $dbArray = [];
            $dbArray['unique_id'] = Order::generateOrderId();
            $dbArray['userID'] = $user_id;
            $dbArray['wallet'] = $request->applied_wallet_amount ?? 0;
            $dbArray['address_id'] = $request->address_id ?? '';
            $dbArray['delivery_type'] = $request->delivery_type ?? 'home_delivery';
            $dbArray['customer_name'] = $address->contact_person_name ?? '';
            $dbArray['delivery_date'] = date('Y-m-d', strtotime($request->delivery_date)) ?? '';
            $dbArray['delivery_slot'] = $request->delivery_slot ?? '';
            $dbArray['contact_no'] = $address->contact_person_mobile ?? '';
            $dbArray['house_no'] = $address->flat_no ?? '';
            $dbArray['apartment'] = $address->building_name ?? '';
            $dbArray['landmark'] = $address->landmark ?? '';
            $dbArray['location'] = $address->location ?? '';
            $dbArray['latitude'] = $address->latitude ?? '';
            $dbArray['vendor_id'] = $seller_id ?? '';
            $dbArray['subscription_id'] = $request->subscription_id ?? '';
            $dbArray['freebees_id'] = $request->freebees_id ?? '';
            $dbArray['freebees_price'] = $freebees_price ?? '';
            $dbArray['applied_cashback'] = (int)$request->applied_cashback ?? '';
            $dbArray['longitude'] = $address->longitude ?? '';
            $dbArray['address_type'] = $address->address_type ?? '';
            $dbArray['instruction'] = $request->instruction ?? '';
            $dbArray['coupon_code'] = $cartValue['coupon_code'] ?? '';
            $dbArray['coupon_discount'] = $cartValue['coupon_discount'] ?? '';
            $dbArray['delivery_charges'] = $cartValue['delivery_charges'] ?? '';
            $dbArray['order_amount'] = $cartValue['cart_price'] ?? '';
            $dbArray['total_amount'] = $cartValue['total_price'] ?? '';
            $dbArray['total_discount'] = $cartValue['total_discount'] ?? '';

            $dbArray['surge_fee'] = $cartValue['surge_fee'] ?? '';
            $dbArray['platform_fee'] = $cartValue['platform_fee'] ?? '';
            $dbArray['handling_charges'] = $cartValue['handling_charges'] ?? '';
            $dbArray['small_cart_fee'] = $cartValue['small_cart_fee'] ?? '';
            $dbArray['rain_fee'] = $cartValue['rain_fee'] ?? '';

            $dbArray['delivery_otp'] = rand(1111, 9999);

            $dbArray['payment_method'] = $payment_method;
            $dbArray['invoice_no'] = self::generateNextInvoiceNo();
            $dbArray['tips'] = $request->tips ?? '';
            $dbArray['delivery_instruction'] = $request->delivery_instruction ?? '';

            $dbArray['is_subscribe'] = CustomHelper::checkSubscription($user_data);


            $dbArray['status'] = 'PLACED';
            $dbArray['order_from'] = 'WEBSITE';
            if ($payment_method == 'COD') {
                $dbArray['cod_amount'] = (int)$cartValue['total_price'] - (int)$request->applied_cashback;
            }
            if ($payment_method == 'online') {
                $dbArray['online_amount'] = (int)$cartValue['total_price'] - (int)$request->applied_cashback;
                $dbArray['is_delete'] = 1;
            }
            $total_price = $cartValue['total_price'] ?? 0;
            $applied_wallet_amount = 0;
            $cod_amount = 0;
            $online_amount = 0;
            if ($wallet_applied) {
                if ($payment_method == 'COD') {
                    if ((float)$user_data->wallet <= (float)$total_price) {
                        $applied_wallet_amount = $wallet;
                        $cod_amount = (float)$total_price - (float)$wallet;
                    } else {
                        $applied_wallet_amount = $total_price;
                    }
                    $dbArray['cod_amount'] = $cod_amount;
                    $dbArray['wallet'] = $applied_wallet_amount;
                }
                if ($payment_method == 'online') {
                    if ((float)$user_data->wallet <= (float)$total_price) {
                        $applied_wallet_amount = $wallet;
                        $online_amount = (float)$total_price - (float)$wallet;
                    } else {
                        $applied_wallet_amount = $total_price;
                    }
                    $dbArray['online_amount'] = $online_amount;
                    $dbArray['wallet'] = $applied_wallet_amount;
                }
            }


            $order_id = Order::insertGetId($dbArray);
            if ($applied_wallet_amount > 0) {
                $new_wallet = (float)$wallet - $applied_wallet_amount;
                User::where('id', $user_id)->update(['wallet' => $new_wallet]);
                ///////Save Transaction Needed
            }
            if (!empty($request->applied_cashback)) {

                if ($payment_method == 'COD') {
                    $new_wallet = (int)$user_data->cashback_wallet - (int)$request->applied_cashback;
                    User::where('id', $user_id)->update(['cashback_wallet' => $new_wallet]);
                    ///////Save Transaction Needed
                    ////Save Transaction////
                    $dbArray = [];
                    $dbArray['userID'] = $user_id;
                    $dbArray['type'] = 'DEBIT';
                    $dbArray['amount'] = (int)$request->applied_cashback ?? 0;
                    $dbArray['against_for'] = 'cashback_wallet';
                    $dbArray['wallet_type'] = 'cashback_wallet';
                    $dbArray['remarks'] = "Amount Debited From NC Cash";
                    $transaction_id = Transaction::insertGetId($dbArray);
                    Transaction::where('id', $transaction_id)->update(['txn_no' => "NC" . rand(111111, 9999999999)]);
                    CustomHelper::Redeeming_NC_Cash($user_data->phone, $request->applied_cashback ?? '', $new_wallet ?? '');
                }

            }


            if (!empty($cart_list)) {
                foreach ($cart_list as $key => $value) {
                    $itemsArr = [];
                    $itemsArr['order_id'] = $order_id;
                    $itemsArr['product_id'] = $value['product_id'] ?? '';
                    $itemsArr['variant_id'] = $value['varient_id'] ?? '';
                    $itemsArr['qty'] = $value['qty'] ?? '';
                    $itemsArr['price'] = $value['selling_price'] ?? '';
                    $itemsArr['subscription_price'] = $value['subscription_price'] ?? '';
                    $itemsArr['net_price'] = $value['total_price'] ?? '';
                    $itemsArr['net_subscription_price'] = $value['net_subscription_price'] ?? '';
                    $itemsArr['status'] = 'PLACED';
                    $itemsArr['vendor_id'] = $seller_id;
                    OrderItems::insert($itemsArr);
                    Order::where('id', $order_id)->update(['delivery_speed' => $value['type'] ?? '']);
                }
            }
        }

        if ($payment_method == 'COD') {
            self::updateStock($order_id);
        }
        self::updateOrderStatus($order_id, "PLACED");

        return $order_id;
    }

    public function generateNextInvoiceNo()
    {
        // Get the last invoice number among non-deleted orders
        $lastOrder = Order::where('is_delete', 0)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder && $lastOrder->invoice_no) {
            // Extract the numeric part and increment
            $lastNumber = (int)substr($lastOrder->invoice_no, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1; // start from 1 if no previous orders
        }

        // Format as INV000001, INV000002, etc.
        $invoiceNo = 'INV' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return $invoiceNo;
    }

    public function updateOrderStatus($order_id, $status)
    {
        $dbArray = [];
        $dbArray['order_id'] = $order_id;
        $dbArray['status'] = $status;
        $dbArray['updated_by'] = 'user';
        OrderStatus::insert($dbArray);
        return true;
    }


    public function create_payment(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            "type" => 'required',
        ]);
        $user = Auth::user();
        $type = $request->type ?? '';
        $order_id = '';
        if ($type == 'order') {
            $amount = $request->amount ?? 0;
            if ($amount <= 0) {
                return response()->json([
                    'result' => false,
                    'message' => "Invalid Amount",
                    'order_id' => null,
                    'keys' => null,
                ], 200);
            }
            $orders = $this->generateRazorpayOrder($amount, $user->id);
            if (!empty($orders)) {
                if (empty($orders->error)) {
                    $order_id = $orders->id;
                    $dbArray = [];
                    $dbArray['user_id'] = $user->id;
                    $dbArray['subscription_id'] = $request->subscription_id ?? '';
                    $dbArray['order_id'] = $request->order_id ?? '';
                    $dbArray['amount'] = $amount;
                    $dbArray['wallet'] = 0;
                    $dbArray['type'] = $type;
                    $dbArray['payment_status'] = 0;
                    $dbArray['razorpay_order_id'] = $order_id;
                    RazorpayOrders::insert($dbArray);
                }
            }
        }

        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'order_id' => $order_id,
            'keys' => CustomHelper::getRazorpayKeys(),
            'orders' => $orders,
        ], 200);
    }

    public function updateStock($order_id)
    {
        $order = Order::find($order_id);
        if (!empty($order)) {
            $order_items = OrderItems::where('order_id', $order_id)->get();
            if (!empty($order_items)) {
                foreach ($order_items as $order_item) {
                    $product_id = $order_item->product_id ?? '';
                    $variant_id = $order_item->variant_id ?? '';
                    $qty = $order_item->qty ?? '';
                    $exist = DB::table('stock_batches')->where('product_id', $product_id);
                    if (!empty($variant_id)) {
                        $exist->where('variant_id', $variant_id);
                    }
                    $exist = $exist->where('quantity', '>', 0)->orderBy('mfg_date', 'ASC')->first();
                    if (!empty($exist)) {
                        if ((int)$exist->quantity <= (int)$qty) {
                            $new_qty = (int)$exist->quantity - (int)$qty;
                            DB::table('stock_batches')->where('id', $exist->id)->update(['quantity' => $new_qty]);
                            StockLog::create([
                                'product_id' => $product_id,
                                'variant_id' => $variant_id,
                                'store_id' => $exist->store_id ?? '',
                                'action' => "sale",
                                'quantity' => $qty,
                                'closing_stock' => $new_qty,
                                'related_id' => 0,
                                'related_type' => "Sale",
                                'created_by' => auth()->id(),
                                'order_id' => $order_id,
                            ]);
                        } else {

                        }
                    }
                }
            }
        }

    }

    public function sendOrderNotification($order_id)
    {
        $order = Order::find($order_id);
        if (!empty($order)) {
            if (empty($order->agent_id)) {
                $total_item = OrderItems::where('order_id', $order_id)->count();
                $agents = DeliveryAgents::where('vendor_id', $order->vendor_id)->where('work_status', 1)->get();
                if (!empty($agents)) {
                    foreach ($agents as $agent) {
                        $token = $agent->deviceToken ?? '';
                        $data = [
                            "type" => "order",
                            "title" => "A New Order Placed",
                            "body" => "A New Order Placed",
                            "latitude" => $order->latitude ?? '',
                            "order_id" => $order_id ?? '',
                            "longitude" => $order->longitude ?? '',
                            "address" => $order->location ?? '',
                            "total_item" => $total_item ?? '',
                            "order_status" => $order->status ?? '',
                            "total_amount" => $order->total_amount ?? '',
                        ];
                        $responce = CustomHelper::fcmNotification($token, $data);
                    }
                }
            }
        }
    }


    public function nc_consult(Request $request)
    {
        $data = [];


        return view('home.nc_consult', $data);

    }

    public function pages_app(Request $request)
    {
        $data = [];


        return view('home.pages_app', $data);

    }

    public function consultation_save(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
            'gender' => 'required|in:male,female,other',
            'mobile' => 'required|digits:10',
            'email' => 'nullable|email',
            'primaryGoal' => 'required|in:weight_loss,muscle_gain,maintenance',
            'currentWeight' => 'required|numeric',
            'targetWeight' => 'required|numeric',
            'dietPreference' => 'required|in:vegetarian,non_vegetarian,vegan',
            'activityLevel' => 'required|in:low,moderate,high',
            'healthConditions' => 'nullable|string',
            'consultMode' => 'required|in:video,phone',
            'preferredDate' => 'required|date|after_or_equal:today',
            'timeSlot' => 'required|string',
            'termsCheck' => 'accepted',
        ]);

        DB::table('consultations')->insert([
            'full_name' => $request->fullName,
            'age' => $request->age,
            'gender' => $request->gender,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'primary_goal' => $request->primaryGoal,
            'current_weight' => $request->currentWeight,
            'target_weight' => $request->targetWeight,
            'diet_preference' => $request->dietPreference,
            'activity_level' => $request->activityLevel,
            'health_conditions' => $request->healthConditions,
            'consultation_mode' => $request->consultMode,
            'preferred_date' => $request->preferredDate,
            'time_slot' => $request->timeSlot,
            'terms_agreed' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Consultation booked successfully!');

    }

    public function getPincodeDetails($pincode)
    {
        $pincode_data = CustomHelper::getPincodeDataEnvia($pincode);

        if (!empty($pincode_data)) {

            return [
                'city' => $pincode_data[0]->locality ?? '',
                'state' => $pincode_data[0]->state->name ?? '',
            ];
        }

        return response()->json(['error' => 'Invalid Pincode'], 400);
    }

    public function buy_giftcard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "amount" => "required",
            "qty" => "required"
        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }

        $user = Auth::user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }

        $amount = (int)$request->amount * (int)$request->qty;
        $orders = $this->generateRazorpayOrder($amount, $user->id);
        if (!empty($orders)) {
            if (empty($orders->error)) {
                $order_id = $orders->id;
                $dbArray = [];
                $dbArray['user_id'] = $user->id;
                $dbArray['subscription_id'] = "";
                $dbArray['amount'] = $amount;
                $dbArray['giftcard_amount'] = $request->amount ?? 0;
                $dbArray['giftcard_type'] = $request->type ?? '';
                $dbArray['giftcard_qty'] = $request->qty ?? 1;
                $dbArray['wallet'] = 0;
                $dbArray['type'] = "giftcard";
                $dbArray['payment_status'] = 0;
                $dbArray['razorpay_order_id'] = $order_id;
                RazorpayOrders::insert($dbArray);
            }
        }


        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'order_id' => $order_id,
            'image' => url('public/assets/images/logo.png'),
            'keys' => CustomHelper::getRazorpayKeys(),
            'orders' => $orders,
        ], 200);
    }

    public function wishlist_save(Request $request)
    {
        $user = null;
        $user = Auth::user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            "product_id" => 'required',
            "variant_id" => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        $exist = Wishlist::where('user_id', $user->id)->where('product_id', $request->product_id)->where('varient_id', $request->variant_id)->first();
        if (empty($exist)) {
            $dbArray = [];
            $dbArray['user_id'] = $user->id;
            $dbArray['seller_id'] = $request->seller_id ?? '';
            $dbArray['product_id'] = $request->product_id ?? '';
            $dbArray['varient_id'] = $request->variant_id ?? '';
            Wishlist::insert($dbArray);
            return response()->json([
                'result' => true,
                'status' => "added",
                'message' => "Added Successfully",
            ], 200);
        } else {
            $exist->delete();
            return response()->json([
                'result' => true,
                'status' => "removed",
                'message' => "Remove Successfully",
            ], 200);
        }
    }


    public function take_subscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "subscription_id" => "required"
        ]);
        $user = Auth::user();
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        if ($user->phone == "9999999999") {
            return response()->json([
                'result' => false,
                'message' => 'Unauthorised',
            ], 401);
        }
        $subscription_id = $request->subscription_id ?? '';
        $subscription_plans = SubscriptionPlans::where('id', $subscription_id)->first();
        if (empty($subscription_plans)) {
            return response()->json([
                'result' => false,
                'message' => "Subscription Not Exist",
            ], 200);
        }
        $order_id = "";
        $is_ban = $user->is_ban ?? 0;
        if ($is_ban == 1) {
            return response()->json([
                'result' => false,
                'message' => "User is Ban",
            ], 200);
        }
        $amount = $subscription_plans->price ?? 0;
        $orders = $this->generateRazorpayOrder($amount, $user->id);
        if (!empty($orders)) {
            if (empty($orders->error)) {
                $order_id = $orders->id;
                $dbArray = [];
                $dbArray['user_id'] = $user->id;
                $dbArray['subscription_id'] = $request->subscription_id;
                $dbArray['amount'] = $amount;
                $dbArray['wallet'] = 0;
                $dbArray['type'] = "subscription";
                $dbArray['payment_status'] = 0;
                $dbArray['razorpay_order_id'] = $order_id;
                RazorpayOrders::insert($dbArray);
            }
        }


        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'order_id' => $order_id,
            'image' => url('public/assets/images/logo.png'),
            'keys' => CustomHelper::getRazorpayKeys(),
            'orders' => $orders,
        ], 200);
    }

    public function invoice(Request $request)
    {
        $orderID = $request->id;
        $orders = Order::where('id', $orderID)->first();
        $seller_details = Vendors::where('id', $orders->id)->first();
        if($orders->status == "DELIVERED"){
//            CustomHelper::generateInvoiceNo();
        }
        $data = [
            'orders' => $orders,
            'seller_details' => $seller_details
        ];
        $pdf = Pdf::loadView('home.saleinvoice_a4_new', $data)
            ->setPaper('a4')->setOptions([
                'isRemoteEnabled' => true, // <-- enable remote images
            ]);
        $filename = 'Invoice_order_' . rand(111, 999999) . time() . '.pdf';

        return Response::make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\""
        ]);

    }
}
