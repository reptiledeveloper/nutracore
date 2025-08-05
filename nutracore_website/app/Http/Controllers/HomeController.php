<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Admin;
use App\Models\Category;
use App\Models\DeliveryAgents;
use App\Models\FeaturedSection;
use App\Models\Order;
use App\Models\Products;
use App\Models\QRCodes;
use App\Models\Setting;
use App\Models\Brand;
use App\Models\User;
use App\Models\Banner;
use App\Models\Cart;
use Auth;
use DB;
use Illuminate\Http\Request;
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
        $data = [];
        $categories = Category::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
        $data['categories'] = $categories;

        $brands = Brand::where('status', 1)
            ->get();
        if (!empty($brands)) {
            foreach ($brands as $brand) {
                $brand->slug = CustomHelper::GetSlug('brands', 'id', $brand->id, $brand->brand_name);
                $brand->save();
            }
        }
        $data['brands'] = $brands;
        $productArr = [];
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


        $banner = Banner::where('status', 1)->where('is_delete', 0)->where('type', 'Fixed_banner1')->first()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
        if (!empty($banners)) {
            $banner->banner_img = CustomHelper::getImageUrl('banners', $banner->banner_img);
            $product_id = explode(",", $banner->product_id);
            $productsArr = [];
            if (!empty($product_id)) {
                foreach ($product_id as $prod_id) {
                    $pro_data = self::getProductDetails($prod_id, $user->id ??'');
                    if (!empty($pro_data)) {
                        $productsArr[] = $pro_data;
                    }
                }
            }
            $banner->products = $productsArr;
        }


        $data['fixed_banner_1'] = $banner;




        return view('home.index', $data);
    }
    public function categories(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $categories = Category::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
        $data['categories'] = $categories;

        return view('home.categories', $data);
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
        $products = Products::select('products.id', 'product_varients.selling_price')->where('products.is_delete', 0)  // Explicitly specify the table
            ->where('products.status', 1)
            ->leftJoin('product_varients', function ($join) {
                $join->on('products.id', '=', 'product_varients.product_id');
            });
        if (isset($min_price) && isset($max_price)) {
            if ($max_price > 0 && $max_price > 0) {
                $products->where('product_varients.selling_price', '>=', $min_price);
                $products->where('product_varients.selling_price', '<=', $max_price);
            }
        }
        if (!empty($search)) {
            $products->where('products.name', 'like', '%' . $search . '%'); // Explicitly specify the table
        }
        if (!empty($category_id)) {
            $products->where('products.category_id', $category_id); // Explicitly specify the table
        }
        if (!empty($brand_id)) {
            $products->where('products.brand_id', $brand_id); // Explicitly specify the table
        }
        if (!empty($subcategory_id)) {
            $products->where('products.subcategory_id', $subcategory_id); // Explicitly specify the table
        }
        if ($order_by_price == 'low_to_high') {
            $products->orderByRaw('COALESCE(product_varients.selling_price, 999999) ASC'); // Ascending order
        }
        if ($order_by_price == 'high_to_low') {
            $products->orderByRaw('COALESCE(product_varients.selling_price, 0) DESC'); // Descending order
        }
        $products = $products->groupBy('products.id')->paginate(50);
        $productArr = [];
        if (!empty($products)) {
            foreach ($products as $product_val) {
                $product_data = self::getProductDetails($product_val->id, $user->id ?? '');
                if (!empty($product_data)) {
                    $productArr[] = $product_data;
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


    public function getProductDetails($product_id, $user_id = null)
    {
        $user = [];
        if (!empty($user_id)) {
            $user = User::find($user_id);
        }
        $product = Products::where('id', $product_id)->first();
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


            if (!empty($varients)) {
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

                    $product_images = DB::table('product_images')->where('product_id', $product->id)->where('varient_id', $varient->id)->get();
                    if (!empty($product_images)) {
                        foreach ($product_images as $product_image) {
                            $dbArray = [];
                            $dbArray['id'] = $product_image->id ?? '';
                            $dbArray['image'] = CustomHelper::getImageUrl('products', $product_image->image);
                            $varient_images[] = $dbArray;
                        }
                    }
                    $varient->images = $varient_images;




                }
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
    public function getCartProductDetails($product_id, $varient_id, $user_id = null)
    {
        $user = [];
        if (!empty($user_id)) {
            $user = User::find($user_id);
        }
        $product = Products::where('id', $product_id)->first();
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
            $varients = $product->varients()->where('id', $varient_id)->where('is_delete', 0)->where('status', 1)->get();
            if (!empty($varients)) {
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

                    $product_images = DB::table('product_images')->where('product_id', $product->id)->where('varient_id', $varient->id)->get();
                    if (!empty($product_images)) {
                        foreach ($product_images as $product_image) {
                            $dbArray = [];
                            $dbArray['id'] = $product_image->id ?? '';
                            $dbArray['image'] = CustomHelper::getImageUrl('products', $product_image->image);
                            $varient_images[] = $dbArray;
                        }
                    }
                    $varient->images = $varient_images;
                }
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
    public function calculateDiscountPer($originalPrice, $discountedPrice)
    {
        if ($originalPrice <= 0) {
            return 0;
        }
        $discount = ((int) $originalPrice - (int) $discountedPrice) / (int) $originalPrice * 100;
        return round($discount);
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
    public function cart(Request $request){
        $data = [];
        $user = Auth::user();
        if (empty($user)) {
            return redirect()->to(url('/'));
        }
        $productArr = [];
        $carts = Cart::where('user_id', $user->id)->get();
        if (!empty($carts)) {
            foreach ($carts as $cart) {
                $product_data = self::getCartProductDetails($cart->product_id, $cart->variant_id, $user->id ?? '');
                if (!empty($product_data)) {
                    $productArr[] = $product_data;
                }
            }
        }
        $data['cart_products'] = $productArr;
        return view('home.cart', $data);
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
                $product_data = self::getCartProductDetails($cart->product_id, $cart->variant_id, $user->id ?? '');
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
        $data = [];


        return view('home.stores', $data);
    }
    public function nutrapass(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.nutrapass', $data);
    }
    public function profile(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.profile', $data);
    }
    public function about(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


        return view('home.about', $data);
    }
    public function contact(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];


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
            if (empty($check_varient)) {
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



}
