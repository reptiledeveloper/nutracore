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
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Session;
use Validator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class HomeController extends Controller
{

    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $data = [];
        $total_sales = 0;
        $total_user = User::count();
        $total_order = Order::count();
        $total_delivery_boy = DeliveryAgents::count();
        $total_product = Products::count();
        $categories = CustomHelper::getCategories();

        $total_sales = Order::where('status', 'DELIVERED')->sum('total_amount');
        $data['total_user'] = $total_user;
        $data['total_order'] = $total_order;
        $data['total_delivery_boy'] = $total_delivery_boy;
        $data['total_product'] = $total_product;
        $data['categories'] = $categories;
        $data['total_sales'] = $total_sales;

        return view('home.index', $data);
    }


    public function save_tab(Request $request): void
    {
        $tab = $request->tab ?? '';
        Session::put('tab', $tab);
        echo 1;
    }

    public function profile(Request $request): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        $data = [];
        $id = Auth::guard('admin')->user()->id ?? '';
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $dbArray = [];
            if (!empty($request->name)) {
                $dbArray['name'] = $request->name;
            }
            if (!empty($request->email)) {
                $dbArray['email'] = $request->email;
            }
            if (!empty($request->phone)) {
                $dbArray['phone'] = $request->phone;
            }
            if (!empty($request->address)) {
                $dbArray['address'] = $request->address;
            }
            if (!empty($request->image)) {
                $file = $request->file('image');
                $dbArray['image'] = CustomHelper::uploadImage($file, 'admin');
            }
            if (!empty($dbArray)) {
                Admin::where('id', $id)->update($dbArray);
            }
            return back();
        }
        return view('home.profile', $data);
    }

    public function change_password(Request $request): \Illuminate\Http\RedirectResponse
    {
        $method = $request->method();
        $id = Auth::guard('admin')->user()->id ?? '';
        if ($method == 'post' || $method == 'POST') {
            $data = $request->validate([
                'old_password' => 'required',
                'new_password' => 'required',
                'confirm_new_password' => 'required|same:new_password',
            ]);
            $request->validate($data);
            $old_password = Auth::guard('admin')->user()->password ?? '';
            $success = Hash::check($request->password, $old_password);
            if ($success) {
                $dbArray = [];
                $dbArray['password_value'] = $request->new_password ?? '';
                $dbArray['password'] = bcrypt($request->new_password);
                Admin::where('id', $id)->update($dbArray);
            }
            return back();
        }
        return back();
    }

    public function store_token(Request $request)
    {
        $id = Auth::guard('admin')->user()->id;
        $token = $request->token ?? '';
        Admin::where('id', $id)->update(['device_token' => $token]);
        echo 1;
    }

    public function get_state(Request $request)
    {
        $country_id = isset($request->country_id) ? $request->country_id : '';
        $html = '<option value="" selected>Select State</option>';
        $state = [];
        if (!empty($country_id)) {
            $state = CustomHelper::getStates($country_id);
            if (!empty($state)) {
                foreach ($state as $st) {
                    $html .= '<option value=' . $st->id . '>' . $st->name . '</option>';
                }
            }
        }
        echo $html;
    }

    public function get_city(Request $request)
    {
        $state_id = isset($request->state_id) ? $request->state_id : '';
        $html = '<option value="" selected>Select City</option>';
        $state = [];
        if (!empty($state_id)) {
            $state = CustomHelper::getCities($state_id);
            if (!empty($state)) {
                foreach ($state as $st) {
                    $html .= '<option value=' . $st->id . '>' . $st->name . '</option>';
                }
            }
        }
        echo $html;
    }

    public function update_status(Request $request)
    {
        $status = $request->status ?? 0;
        $table = $request->table ?? '';
        $id = $request->id ?? '';
        if (!empty($table) && !empty($id)) {
            DB::table($table)->where('id', $id)->update(['status' => $status]);
        }
        echo 1;

    }

    public function settings(Request $request)
    {
        $method = $request->method();

        if ($method == 'post' || $method == 'POST') {
            $dbArray = [];
            if (!empty($request->privacy_policy)) {
                $dbArray['privacy_policy'] = $request->privacy_policy;
            }
            if (!empty($request->terms)) {
                $dbArray['terms'] = $request->terms;
            }
            if (!empty($request->about_us)) {
                $dbArray['about_us'] = $request->about_us;
            }
            if (!empty($request->refund_policy)) {
                $dbArray['refund_policy'] = $request->refund_policy;
            }
            if (!empty($request->grivance_policy)) {
                $dbArray['grivance_policy'] = $request->grivance_policy;
            }
            if (!empty($request->google_map_key)) {
                $dbArray['google_map_key'] = $request->google_map_key;
            }
            if (!empty($request->contact_address)) {
                $dbArray['contact_address'] = $request->contact_address;
            }
            if (!empty($request->contact_phone)) {
                $dbArray['contact_phone'] = $request->contact_phone;
            }
            if (!empty($request->contact_email)) {
                $dbArray['contact_email'] = $request->contact_email;
            }
            if (!empty($request->contact_us)) {
                $dbArray['contact_us'] = $request->contact_us;
            }
            if (!empty($request->admin_commission)) {
                $dbArray['admin_commission'] = $request->admin_commission;
            }
            if (!empty($request->user_commission)) {
                $dbArray['user_commission'] = $request->user_commission;
            }
            if (!empty($request->contact_whatsapp)) {
                $dbArray['contact_whatsapp'] = $request->contact_whatsapp;
            }
            if (!empty($request->razorpay_key_test)) {
                $dbArray['razorpay_key_test'] = $request->razorpay_key_test;
            }
            if (!empty($request->razorpay_secret_test)) {
                $dbArray['razorpay_secret_test'] = $request->razorpay_secret_test;
            }
            if (!empty($request->razorpay_key_live)) {
                $dbArray['razorpay_key_live'] = $request->razorpay_key_live;
            }
            if (!empty($request->razorpay_secret_live)) {
                $dbArray['razorpay_secret_live'] = $request->razorpay_secret_live;
            }
            if (!empty($request->google_map_key)) {
                $dbArray['google_map_key'] = $request->google_map_key;
            }
            if (isset($request->is_live)) {
                $dbArray['is_live'] = $request->is_live;
            }
            if (isset($request->subscription_month)) {
                $dbArray['subscription_month'] = $request->subscription_month;
            }

            if (isset($request->is_handling_charges)) {
                $dbArray['is_handling_charges'] = $request->is_handling_charges;
            }
            if (isset($request->is_surge_fee)) {
                $dbArray['is_surge_fee'] = $request->is_surge_fee;
            }
            if (isset($request->is_platform_fee)) {
                $dbArray['is_platform_fee'] = $request->is_platform_fee;
            }
            if (isset($request->is_small_cart_fee)) {
                $dbArray['is_small_cart_fee'] = $request->is_small_cart_fee;
            }

            if (!empty($request->handling_charges)) {
                $dbArray['handling_charges'] = $request->handling_charges;
            }
            if (!empty($request->surge_fee)) {
                $dbArray['surge_fee'] = $request->surge_fee;
            }
            if (!empty($request->platform_fee)) {
                $dbArray['platform_fee'] = $request->platform_fee;
            }
            if (!empty($request->small_cart_fee)) {
                $dbArray['small_cart_fee'] = $request->small_cart_fee;
            }

            if (!empty($request->refer_amount)) {
                $dbArray['refer_amount'] = $request->refer_amount;
            }
            if (!empty($request->cashback_wallet_use)) {
                $dbArray['cashback_wallet_use'] = $request->cashback_wallet_use;
            }
            if (!empty($request->delhivery_key)) {
                $dbArray['delhivery_key'] = $request->delhivery_key;
            }
            if (!empty($request->delhivery_url)) {
                $dbArray['delhivery_url'] = $request->delhivery_url;
            }


            Setting::where('id', 1)->update($dbArray);
            return back();
        }
        $settings = Setting::first();
        $data = [];
        $data['settings'] = $settings;

        return view('home.settings', $data);

    }

    public function get_sub_category(Request $request)
    {
        $category_id = $request->category_id ?? '';
        $shops = Category::where('parent_id', $category_id)->get();
        $html = '';
        if (!empty($shops)) {
            foreach ($shops as $shop) {
                $html .= '<option value=' . $shop->id . '>' . $shop->name . '</option>';
            }
        }
        echo $html;
    }

    public function get_tags(Request $request)
    {
        $category_id = $request->category_id ?? '';
        $shops = Category::where('id', $category_id)->first();
        $html = '';
        if (!empty($shops)) {
            $tags = explode(",", $shops->tags);
            $is_selected = "";
            $alltags = \App\Models\Tags::where('is_delete',0)->get();
            if (!empty($alltags)) {
                foreach ($alltags as $tag) {
                    $is_selected = "";
                    if(in_array($tag,$tags)){
                        $is_selected = "selected";
                    }
                    $html .= '<option value=' . $tag . ' '.$is_selected.'>' . $tag . '</option>';
                }
            }
        }
        echo $html;
    }


    public function delete_image(Request $request)
    {
        $folder = $request->folder ?? '';
        $feature_id = $request->feature_id ?? '';
        $image_name = $request->image_name ?? '';
        $id = $request->id ?? '';
        if (!empty($folder)) {
            if ($folder == 'featured_section') {
                $feature = FeaturedSection::find($feature_id);
                $images = $feature->image ?? '';
                $images = explode(",", $images);
                $imagesArr = [];
                if (!empty($images)) {
                    foreach ($images as $img) {
                        if ($img != basename($image_name)) {
                            $imagesArr[] = $img;
                        }
                    }
                    $feature->image = implode(",", $imagesArr);
                    $feature->save();
                }
            }
            if ($folder == 'banners') {
                DB::table('category_brand_images')->where('id', $id)->update(['is_delete' => 1]);
            } else {
                DB::table($folder)->where('id', $id)->update(['is_delete' => 1]);
            }

        }

        echo 1;
    }

    public function search_image(Request $request)
    {
        $search = $request->search ?? '';
        $folder = $request->folder ?? '';
        $basePath = dirname(__DIR__, 4) . '/images';
        $folder_name = $basePath . '/' . $folder;
        $files = [];
        $html = '';
        if (!empty($folder_name)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folder_name, FilesystemIterator::SKIP_DOTS)
            );

            $files = [];
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = $file;
                }
            }

            // Apply search filter if needed
            if (!empty($search)) {
                $files = array_filter($files, function ($file) use ($search) {
                    return stripos($file->getFilename(), $search) !== false;
                });
            }
        }

        foreach ($files as $file) {
            $path = $file->getPathname();
            $path_val = \App\Helpers\CustomHelper::getImagePath($path);
            $file_url = env('IMAGE_URL') . '/' . $path_val;
            $image_name = str_replace($folder_name . "/", "", $path_val);
            $html .= '<div class="col-md-4 mt-3">
                            <div class="image-container">
                                <input type="checkbox" class="checkbox" name="image_text[]" value=' . $image_name . '>
                                <img src=' . $file_url . ' alt="Sample Image">

                            </div>
                            <p>' . $path_val . '</p>
                        </div>';
        }
        echo $html;


    }
}
