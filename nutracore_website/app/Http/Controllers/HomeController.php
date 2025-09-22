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
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Products;
use App\Models\QRCodes;
use App\Models\Setting;
use App\Models\Brand;
use App\Models\SubscriptionPlans;
use App\Models\Subscriptions;
use App\Models\User;
use App\Models\Banner;
use App\Models\Cart;
use App\Models\UserAddress;
use App\Models\VendorProductPrice;
use App\Models\Vendors;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Session;
use Validator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;
use Carbon\Carbon;

class HomeController extends Controller
{

    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $user = Auth::guard('user')->user();


        $data = [];
        $banners = Banner::where('status', 1)->where('is_delete', 0)->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
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
        $categories = Category::where('status', 1)->where('parent_id', 0)->where('is_goal', 0)->where('is_delete', 0)->orderBy('priority', 'ASC')->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $category->image = CustomHelper::getImageUrl('categories', $category->image ?? '');
            }
        }
        $goalcategories = Category::where('status', 1)->where('parent_id', 0)->where('is_goal', 1)->where('is_delete', 0)->orderBy('priority', 'ASC')->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
        if (!empty($goalcategories)) {
            foreach ($goalcategories as $category) {
                $category->image = CustomHelper::getImageUrl('categories', $category->image ?? '');
            }
        }
        $homepageArr['goalcategories'] = $goalcategories;
        $brands = Brand::where('status', 1)->where('is_delete', 0)->orderBy('priority', "ASC")->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
        if (!empty($brands)) {
            foreach ($brands as $brand) {
                $brand->icon = CustomHelper::getImageUrl('brands', $brand->brand_img);
                $brand->image = CustomHelper::getImageUrl('brands', $brand->brand_img);
                $brand->brand_img = CustomHelper::getImageUrl('brands', $brand->brand_img);
                $brand->brand_icon = CustomHelper::getImageUrl('brands', $brand->brand_img);
                $brand->certificate = CustomHelper::getImageUrl('brands', $brand->certificate);
            }
        }
        $homepageArr['categories'] = $categories;

        $homepageArr['brands'] = $brands;
        $homepageArr['banners'] = $banners;
        $seller_id = $user->seller_id ?? $request->seller_id ?? '';
        $selected_address = null;
        $seller_details = null;
        if (!empty($user)) {
            if (!self::checkGuest($user)) {
                $selected_address = CustomHelper::getAddressDetails($user->addressID);
            }

            $seller_details = self::getSellerDetails($user->seller_id, $user->id ?? '');

            $user->selected_address = $selected_address;
            $user->seller_details = $seller_details;
        }

        $subscription_plans = SubscriptionPlans::where('status', 1)->where('is_delete', 0)->get();
        $minPricePerDay = PHP_FLOAT_MAX;
        $bestValuePlanId = null;
// First pass: Find plan with best price per day
        foreach ($subscription_plans as $plan) {
            // Assume duration is in days. If months, convert to days.
            $durationInDays = $plan->duration * 30.44;

            if ($durationInDays > 0) {
                $pricePerDay = (int)$plan->price / $durationInDays;

                if ($pricePerDay < $minPricePerDay) {
                    $minPricePerDay = $pricePerDay;
                    $bestValuePlanId = $plan->id;
                }
            }
        }

        if (!empty($subscription_plans)) {
            foreach ($subscription_plans as $plan) {
                $plan->image = CustomHelper::getImageUrl('subscription_plans', $plan->image);
                $is_best_value = 0;
                if ($plan->id == $bestValuePlanId) {
                    $is_best_value = 1;
                }
                $plan->is_best_value = $is_best_value;
            }
        }
        $subscription_data = [];
        $new_updates = [];
        $testimonials = [];
        $best_seller = [];
        $new_arrivals = [];

        $subscription_data['description'] = '🔥  10% OFF every order <br>
                                            🚚  Free Express Delivery <br>
                                            🎁  Monthly Freebie Box <br>
                                            ⏰  Early Access & Secret Sales';

        $products = Product::where('status', 1)->latest()->limit(4)->get();
        if (!empty($products)) {
            foreach ($products as $product) {
                $pro_data = self::getProductDetails($product->id, $user->id ?? '');
                if (!empty($pro_data)) {
                    $best_seller[] = $pro_data;
                }
            }
        }
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


        $new_updates = DB::table('new_updates')->where('is_delete', 0)->where('status', 1)->latest()->limit(5)->get();
        if (!empty($new_updates)) {
            foreach ($new_updates as $new_update) {
                $new_update->product = self::getProductDetails($new_update->product_id ?? '', $user->id ?? '');
            }
        }
        $testimonials = DB::table('testimonial')->where('is_delete', 0)->where('status', 1)->latest()->limit(5)->get();
        if (!empty($testimonials)) {
            foreach ($testimonials as $testimonial) {
                $testimonial->image = CustomHelper::getImageUrl('testimonials', $testimonial->image);
            }
        }


        $homepageArr['products'] = $products;
        $homepageArr['best_deals'] = $best_deals;
        $homepageArr['best_sellers'] = $best_sellers;
        $homepageArr['selected_address'] = $selected_address;
        $homepageArr['seller_details'] = $seller_details;
        $homepageArr['subscription_plans'] = $subscription_plans;
        $homepageArr['subscription_data'] = $subscription_data;
        $homepageArr['new_updates'] = $new_updates;
        $homepageArr['testimonials'] = $testimonials;
        $homepageArr['newArrival'] = $new_arrivals;


        return view('home.index', $homepageArr);
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
        $user = [];
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
        if ($order_by_price == 'low_to_high') {
            $products->orderBy('min_price', 'ASC');
        } elseif ($order_by_price == 'high_to_low') {
            $products->orderBy('min_price', 'DESC');
        }

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

        $categories = Category::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
        $data['categories'] = $categories;
        $data['products'] = $productArr;


        return view('home.products', $data);
    }

    public function getNcCashPercent($user, $amount)
    {
        $is_active = 0;

        $subscription_end_date = '';
        if (!empty($user)) {
            $exist_subscription = Subscriptions::where('user_id', $user->id)->where('paid_status', 1)->latest()->first();
            if (!empty($exist_subscription)) {
                $current_date = date('Y-m-d');
                if (strtotime($exist_subscription->end_date) >= strtotime($current_date)) {
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
            return round(($amount * (int)$active_loyalty->cashback) / 100);
        }
        return 0;

    }

    public function calculateDiscountPer($originalPrice, $discountedPrice)
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

    public function getProductDetails($product_id, $user_id = null)
    {
        $user = [];


        $user = [];
        $estimated_day = "Get it By Tomorrow 11 AM";

        if (!empty($user_id)) {
            $user = User::find($user_id);
            $pincode = $user->pincode ?? '';
            $latitude = $user->latitude ?? '';
            $longitude = $user->longitude ?? '';

            $seller = self::getNearestSeller($latitude, $longitude, 2, 40);

            if (!empty($seller)) {
                $cutoff_time = CustomHelper::getSettingKey('cutoff_time');
                $user->seller_id = $seller->id ?? "";
                $user->save();
                if (date('H:i:s') < $cutoff_time) {
                    // Add 2 hours to current time
                    $nextHour = strtotime('+1 hour', strtotime(date('Y-m-d H:00:00')));
                    // Add 2 hours to delivery time
                    $delivery_time = date('h:i A', strtotime('+2 hours', $nextHour));
                    $day_time_text = "Today " . $delivery_time;
                } else {
                    $day_time_text = "Tomorrow 11 AM";
                }
//                $day_time_text = (date('H:i:s') < $cutoff_time) ? 'Today 8 PM' : 'Tomorrow 11 AM';
                $estimated_day_cache = "Get it By " . $day_time_text;
                $estimated_day = $estimated_day_cache;
            }
        }


        $product = Product::where('id', $product_id)->first();
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
            $varients = $product->varients()->where('is_delete', 0)->where('status', 1)->get();
            $product->estimated_day = $estimated_day;

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
                    'qty' => $product->stock ?? 0,
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
            $product->rating = "0";
            $nc_cash = 0;

            $product->certificate = CustomHelper::getImageUrl('brands', $brand->certificate ?? '');
//            if (!empty($varients) && count($varients) > 0) {
//                return $product;
//            }
            return $product;
        }

        return null;
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

    public function getCartData()
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
        $cart_data = CustomHelper::cartData($user->id, $coupon_code, $request, $user);
        $cartValue = $cart_data['cartValue'] ?? '';
        $cart_price = $cartValue['cart_price'] ?? '';
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
            $expressSlot = DB::table('delivery_charges')
                ->where('type', 'express')
                ->where('status', 1)
                ->where('is_delete', 0)
                ->whereRaw('? BETWEEN order_amount AND order_amount2', [$cart_price])
                ->first();

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
            'subscription_plans_new' => $subscription_plans_new,
            'is_subscribe' => CustomHelper::checkSubscription($user),
        ];
        return $data;

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

        $html = view('home.cart_html', $data)->render();
        return response()->json(['html' => $html]);
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

            $htmlContent = view('emails.contact',$dbArray)->render();

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
        return json_encode(['address' => $address]);

    }

    public function save_address(Request $request)
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


}
