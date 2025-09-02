<?php

namespace App\Helpers;

use App\Models\ActivityLogs;
use App\Models\Admin;
use App\Models\Attributes;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\CaseInvoices;
use App\Models\Cases;
use App\Models\Category;
use App\Models\CategoryWiseCommission;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\DeliveryAgents;
use App\Models\DocumentRequired;
use App\Models\EventImages;
use App\Models\Manufacturer;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\OrderStatus;
use App\Models\Permission;
use App\Models\Products;
use App\Models\ProductVarient;
use App\Models\PropertyBorrower;
use App\Models\PropertyDocuments;
use App\Models\PropertyOwners;
use App\Models\PropertyOwnerType;
use App\Models\PropertyType;
use App\Models\QRCodes;
use App\Models\Roles;
use App\Models\SellerRoles;
use App\Models\Shop;
use App\Models\State;
use App\Models\StockBatch;
use App\Models\StockLog;
use App\Models\SubscriptionPlans;
use App\Models\Transaction;
use App\Models\User;

use App\Models\ProductStock;


use App\Models\UserAddress;
use App\Models\VendorProductPrice;
use App\Models\VendorProductPrice as VendorProductPriceAlias;
use App\Models\Vendors;
use Carbon\Carbon;
use DateTime;
use DB;
use Google\Auth\AccessToken\AccessToken;
use Illuminate\Support\Facades\Auth;
use Image;
use League\OAuth2\Client\Provider\GenericProvider;
use Mail;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Storage;
use Validator;


class CustomHelper
{

    /**
     * Render S3 image URL
     *
     * @param $name
     * @param bool $thumbnail
     * @return string
     */

    public static function getAdminRouteName()
    {
        $ADMIN_ROUTE_NAME = config('custom.ADMIN_ROUTE_NAME');

        if (empty($ADMIN_ROUTE_NAME)) {
            $ADMIN_ROUTE_NAME = 'admin';
        }

        return $ADMIN_ROUTE_NAME;
    }


    public static function getNoOfStock($product_id, $varient_id, $vendor_id)
    {
        $stock_data = ProductStock::where('product_id', $product_id)->where('varient_id', $varient_id)->where('vendor_id', $vendor_id)->first();
        return $stock_data->no_of_stock ?? 0;
    }


    public static function logStock($product_id, $variant_id, $store_id, $action, $quantity, $related_id = null, $related_type = null)
    {
        // Current closing stock from StockBatch
        $closing = StockBatch::where('product_id', $product_id)
            ->where('variant_id', $variant_id)
            ->where('store_id', $store_id)
            ->sum('quantity');

        StockLog::create([
            'product_id' => $product_id,
            'variant_id' => $variant_id,
            'store_id' => $store_id,
            'action' => $action,
            'quantity' => $quantity,
            'closing_stock' => $closing,
            'related_id' => $related_id,
            'related_type' => $related_type,
            'created_by' => auth()->id(),
        ]);
    }

    public static function generateGiftCardCode(
        int     $length = 12,
        string  $prefix = 'NC-',
        ?string $table = 'gift_card',
        string  $column = 'code'
    ): string
    {
        $alphabet = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $maxAttempts = 5;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            // Build code
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $finalCode = $prefix . trim(chunk_split($code, 4, '-'), '-');

            // If no DB table check required, return immediately
            if (empty($table)) {
                return $finalCode;
            }

            // Check if already exists in DB
            $exists = DB::table($table)
                ->where($column, $finalCode)
                ->exists();

            if (!$exists) {
                return $finalCode;
            }
        }

        throw new \RuntimeException('Unable to generate a unique gift card code after multiple attempts.');
    }

    public static function getDuration($start_time = '', $end_time = '')
    {
        $duration = '';
        if (!empty($start_time) && !empty($end_time)) {
            $start_datetime = new DateTime($start_time);
            $diff = $start_datetime->diff(new DateTime($end_time));
            $duration = $diff->h . ' H ' . $diff->i . ' M ' . $diff->s . ' S ';
        }
        return $duration;
    }

    public static function getCompanyData($company_id)
    {
        $company = Company::find($company_id);
        return $company;
    }


    public static function getShopData($company_id)
    {
        $company = Shop::find($company_id);
        return $company;
    }


    public static function loginShipRocket()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "email": "nutracore.in@gmail.com",
    "password": "Nutra@5115"
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public static function checkDelivery($pincode)
    {
        $login_data = self::loginShipRocket();
        $token = $login_data->token ?? '';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/serviceability',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '{
    "pickup_postcode": "500019",
    "delivery_postcode": ' . $pincode . ',
    "weight": "1",
    "cod": "1"
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public static function getProducts()
    {
        $products = Products::select('id', 'name')->where('status', 1)->where('is_delete', 0)->get();
        return $products;
    }

    public static function getLoanNo($string)
    {
        $words = explode(" ", $string);
        $acronym = "";
        foreach ($words as $w) {
            $acronym .= mb_substr($w, 0, 1);
        }
        $acronym = $acronym . rand(000, 999999);
        return strtoupper($acronym);
    }


    public static function getEMIStatus($emis)
    {
        $result = [];
        $is_over_due = 0;
        $is_paid = 0;
        $is_unpaid = 0;
        $is_partially_paid = 0;
        $current_date = date('Y-m-d');
        if ($current_date > $emis->date) {
            if ($emis->due == 0) {
                $is_paid = 1;
            }
            if ($emis->due > 0 && $emis->due < $emis->amount) {
                $is_partially_paid = 1;
            }
            if ($emis->due == $emis->amount) {
                $is_over_due = 1;
            }

        } else {
            if ($emis->due == 0) {
                $is_paid = 1;
            }
            if ($emis->due < $emis->amount) {
                $is_partially_paid = 1;
            }
            if ($emis->due == $emis->amount) {
                $is_unpaid = 1;
            }
        }
        $result = [
            'is_over_due' => $is_over_due,
            'is_paid' => $is_paid,
            'is_unpaid' => $is_unpaid,
            'is_partially_paid' => $is_partially_paid,
        ];
        return $result;
    }

    public static function updateCustomerEmi($emi_id, $paid_amount, $fine = 0, $remarks = '')
    {
        $user_id = Auth::guard('admin')->user()->id ?? '';
        $emi_data = DB::table('emis')->where('id', $emi_id)->first();
        if (!empty($emi_data)) {
            $new_paid_amount = (int)$emi_data->paid_amount + (int)$paid_amount;
            $new_due_amount = (int)$emi_data->amount - (int)$new_paid_amount;

            $dbArray = [];
            $dbArray['paid_amount'] = $new_paid_amount;
            $dbArray['due'] = $new_due_amount;
            $dbArray['paid_date'] = date('Y-m-d');
            $dbArray['fine'] = $fine;
            $dbArray['remarks'] = $remarks;

            DB::table('emis')->where('id', $emi_id)->update($dbArray);

            $dbArray1 = [];
            $dbArray1['emi_id'] = $emi_id;
            $dbArray1['admin_id'] = $user_id;
            $dbArray1['customer_id'] = $emi_data->customer_id;
            $dbArray1['paid_amount'] = $paid_amount;
            $dbArray1['date'] = date('Y-m-d');
            $dbArray1['remarks'] = $remarks;
            $dbArray1['fine'] = $fine;
            DB::table('emi_transaction')->insert($dbArray1);
            return true;
        }

    }

    public static function getLoginVendorId()
    {
        $user = Auth::guard('admin')->user();
        $vendor_id = 0;
        if ($user->role_id == 0) {
            $vendor_id = 0;
        } else {
            $vendor_id = $user->id;
        }
        return $vendor_id;
    }


    public static function getCompanies()
    {
        $companies = Company::where('status', 1)->where('is_delete', 0)->get();

        return $companies;
    }


    public static function getContentCount($course_id, $type_slug, $subject_id = '')
    {
        $type = '';
        if ($type_slug == 'test-series') {
            $type = 'exam';
        }
        if ($type_slug == 'video-course') {
            $type = 'video';
        }
        if ($type_slug == 'e-books') {
            $type = 'notes';
        }
        if ($type_slug == 'books') {
            $type = 'books';
        }

        if ($type_slug == 'all') {
            $contents = DB::table('contents')->where('course_id', $course_id)->where('status', 1)->where('is_delete', 0);
        } else {
            $contents = DB::table('contents')->where('course_id', $course_id)->where('status', 1)->where('is_delete', 0);
            $contents->where('hls_type', strtolower($type));
        }
        if (!empty($subject_id)) {
            $contents->where('subject_id', $subject_id);
        }
        $contents = $contents->count();

        return $contents;

    }

    public static function getSubscriptionPlans()
    {
        $plans = SubscriptionPlans::where('status', 1)->where('is_delete', 0)->get();
        return $plans;

    }


    public static function getAgents($seller_id = '')
    {
        $users = DeliveryAgents::select('id', 'name', 'phone')->where('status', 1)->where('is_delete', 0);
        if (!empty($seller_id)) {
            $users->where('vendor_id', $seller_id);
        }
        $users = $users->get();
        return $users;
    }

    public static function getDeliveryAgents()
    {
        $users = DeliveryAgents::select('id', 'name')->where('is_delete', 0)->get();
        return $users;
    }

    public static function getAgentData($agentID = '')
    {
        $users = [];
        if (!empty($agentID)) {
            $users = DeliveryAgents::where('is_delete', 0)->where('id', $agentID)->first();
        }
        return $users;
    }


    public static function getContents($course_id, $subject_id = '', $chapter_id = '', $type_slug)
    {
        $type = '';
        if ($type_slug == 'test-series') {
            $type = 'exam';
        }
        if ($type_slug == 'video-course') {
            $type = 'video';
        }
        if ($type_slug == 'e-books') {
            $type = 'notes';
        }
        if ($type_slug == 'books') {
            $type = 'books';
        }

        if ($type_slug == 'all') {
            $contents = DB::table('contents')->where('course_id', $course_id)->where('status', 1)->where('is_delete', 0);
        } else {
            $contents = DB::table('contents')->where('course_id', $course_id)->where('status', 1)->where('is_delete', 0);
            $contents->where('hls_type', strtolower($type));
        }
        if (!empty($subject_id)) {
            $contents->where('subject_id', $subject_id);
        }
        if (!empty($chapter_id)) {
            $contents->where('topic_id', $chapter_id);
        }
        $contents = $contents->get();

        return $contents;

    }

    public static function getAdminName($user_id)
    {
        $admin = Admin::where('id', $user_id)->first();

        return $admin->name ?? '';
    }

    public static function getUserDetails($user_id)
    {
        $user = User::where('id', $user_id)->first();

        return $user;
    }

    public static function getLiveClassTypes()
    {
        $types = config('custom.live_class_types');
        return $types;
    }


    public static function getFaculties()
    {
        $admins = Admin::where('role_id', 1)->get();
        return $admins;
    }


    public static function getNewsType()
    {
        $news_types = config('custom.news_type');
        // if(!empty($news_types)){
        //     foreach ($news_types as $key => $value) {

        //     }
        // }
        return $news_types;
    }

    public static function getNewsTypeName($key)
    {
        $news_types = config('custom.news_type');
        $name = '';
        if (!empty($news_types)) {
            foreach ($news_types as $key1 => $value) {
                if ($key == $key1) {
                    $name = $value;
                }
            }
        }
        return $name;
    }


    public static function getLoginUserDetails()
    {
        $user = Auth::guard('admin')->user();
        return $user;
    }

    public static function getLoginCompanyId()
    {
        $user = Auth::guard('admin')->user();
        $company_id = 0;
        if ($user->role_id == 0) {
            $company_id = 0;
        } else {
            $company_id = $user->company_id;
        }
        return $company_id;
    }

    public static function getLoginRoleId()
    {
        $user = Auth::guard('admin')->user();
        return $user->role_id;
    }


    public static function checkIsAdmin()
    {
        $is_admin = false;
        $user_data = Auth::guard('admin')->user();
        if (!empty($user_data)) {
            $role = Roles::where('id', $user_data->role_id)->first();
            if (!empty($role)) {
                if ($role->is_admin == 1) {
                    $is_admin = true;
                } else {
                    $is_admin = false;
                }
            }
        }
        if ($user_data->role_id == 0) {
            $is_admin = true;
        }
        return $is_admin;
    }


    public static function getGST(): array
    {
        $gstArr = [
            '5' => '5 %',
            '12' => '12 %',
            '18' => '18 %',
            '28' => '28 %',
        ];
        return $gstArr;

    }


    public static function isAllowedSection($sectionName, $type = '')
    {
        $roleId = Auth::guard('admin')->user()->role_id;
        $company_id = Auth::guard('admin')->user()->company_id;
        $isAllowed = false;
        if ($roleId == 0) {
            $isAllowed = true;
        } else {
            $sectionpermission = Permission::where('role_id', $roleId)->where('company_id', $company_id)->where('section', $sectionName)->where($type, 1)->first();
            if (!empty($sectionpermission)) {
                $isAllowed = true;
            } else {
                $isAllowed = false;
            }
        }
        return $isAllowed;
    }


    public static function saveActivityLogs($data)
    {
        $dbArray = [];
        $dbArray['company_id'] = $data->company_id ?? '';
        $dbArray['emp_id'] = $data->emp_id ?? '';
        $dbArray['remarks'] = $data->remarks ?? '';
        $dbArray['latitude'] = $data->latitude ?? '';
        $dbArray['longitude'] = $data->longitude ?? '';
        $dbArray['status'] = '1';
        ActivityLogs::insert($dbArray);
    }


    public static function isAllowedModule($moduleName)
    {

        $isAllowed = false;
        $allowedModulesArr = config('modules.allowed');
        $moduleNameArrAnd = [];
        $moduleNameArrOr = [];

        $isAnd = strpos($moduleName, "&");
        $isOr = strpos($moduleName, "|");

        if ($isAnd >= 0 && $isAnd !== false) {
            $moduleNameArrAnd = explode('&', $moduleName);
        } //elseif($isOr >= 0 && $isOr !== false){
        else {
            $moduleNameArrOr = explode('|', $moduleName);
        }

        //pr($moduleNameArr);
        //prd($moduleNameArr);

        if (!empty($moduleNameArrAnd) && count($moduleNameArrAnd) > 0) {
            $isAndAllowed = true;
            foreach ($moduleNameArrAnd as $module) {
                if (!in_array($module, $allowedModulesArr)) {
                    $isAndAllowed = false;
                }
            }

            $isAllowed = $isAndAllowed;
        } elseif (!empty($moduleNameArrOr) && count($moduleNameArrOr) > 0) {
            foreach ($moduleNameArrOr as $module) {
                if (in_array($module, $allowedModulesArr)) {
                    return true;
                }
            }
        }

        return $isAllowed;
    }

    public static function getCountries()
    {
        $countries = Country::where('status', 1)->where('is_delete', 0)->get();
        return $countries;

    }

    public static function getImageUrl($path, $filename): \Illuminate\Foundation\Application|string|\Illuminate\Contracts\Routing\UrlGenerator|\Illuminate\Contracts\Foundation\Application
    {
        $image = favicon();
        $is_s3 = env('IS_S3');
        if ($is_s3 == 1) {
            if (!empty($filename)) {
                $image = env('S3_URL') . $path . '/' . $filename;
            } else {
                $image = favicon();
            }
        }
        if ($is_s3 == 0) {
            if (!empty($filename)) {
                $image = env('IMAGE_URL') . $path . '/' . $filename;
            } else {
                $image = favicon();
            }
        }
        return $image;
    }

    public static function getRazorpayKeys()
    {
        return DB::table('payment_gateway')->where('status', 1)->first();

    }


    public static function numberToWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
            100 => 'Hundred',
            1000 => 'Thousand',
            1000000 => 'Million',
            1000000000 => 'Billion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 0) {
            $number = abs($number);
            $result = $negative . numberToWords($number);
        } else {
            $result = '';
        }

        $number = number_format($number, 2, '.', '');
        $parts = explode('.', $number);
        $integer = $parts[0];
        $decimal = $parts[1];

        if ($integer > 0) {
            $result .= self::convertIntegerToWords($integer, $dictionary);
        }

        $result .= ' Rupees';

        if ($decimal > 0) {
            $result .= $decimal > 0 ? $decimal . ' Paise' : '';
        }

        return $result;
    }

    public static function convertIntegerToWords($number, $dictionary)
    {
        $string = '';
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        if ($number >= 1000) {
            $string .= self::convertIntegerToWords(intval($number / 1000), $dictionary) . ' ' . $dictionary[1000] . $separator;
            $number = $number % 1000;
        }
        if ($number >= 100) {
            $string .= self::convertIntegerToWords(intval($number / 100), $dictionary) . ' ' . $dictionary[100] . $separator;
            $number = $number % 100;
        }
        if ($number >= 20) {
            $string .= $dictionary[intval($number / 10) * 10] . $hyphen;
            $number = $number % 10;
        }
        if ($number > 0) {
            $string .= $dictionary[$number];
        }
        return $string;
    }


    public static function getPastTime($timestamp)
    {
        $date1 = new DateTime($timestamp);
        $date2 = new DateTime(date('Y-m-d h:i:s'));

        $difference = $date1->diff($date2);

        if ($difference->s <= 60) {
            $data = self::convert_number_to_words($difference->s) . " Second Ago"; //23
        }

        if ($difference->i <= 60) {
            $data = self::convert_number_to_words($difference->i) . " Minute Ago"; //23
        }
        if ($difference->h >= 1 && $difference->h <= 24) {
            $data = self::convert_number_to_words($difference->h) . " Hour Ago"; //23
        }
        if ($difference->d >= 1 && $difference->h <= 30) {
            $data = self::convert_number_to_words($difference->d) . " Day Ago"; //23
        }
        if ($difference->m >= 1 && $difference->m <= 12) {
            $data = self::convert_number_to_words($difference->m) . " Month Ago"; //23
        }
        if ($difference->y >= 1) {
            $data = self::convert_number_to_words($difference->y) . " Year Ago"; //23
        }
        return $data;
        // $diffInDays    = $difference->d; //21
        // $diffInMonths  = $difference->m; //4
        // $diffInYears   = $difference->y; //1
    }

    public static function GetSlugBySelf($slug_array, $text)
    {

        $slug = '';

        // echo $text; die;
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);
        // echo $text; die;
        if (empty($text)) {
            // return 'n-a';
        }

        $slug = self::GetUniqueSlugBySelf($slug_array, $text);
        // echo $slug; die;

        return $slug;
    }

    public static function GetUniqueSlugBySelf($slug_array, $slug = '', &$num = '')
    {

        $new_slug = $slug . $num;

        //pr($new_slug);

        $slug = $new_slug;

        if (is_array($slug_array) && in_array($slug, $slug_array)) {
            $num = (int)$num + 1;
            $slug = self::GetUniqueSlugBySelf($slug_array, $new_slug, $num);
        }

        return $slug;
    }


    public static function GetSlug($tbl_name, $id_field, $row_id = '', $text = '')
    {
        // echo $text; die;
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);
        // echo $text; die;
        if (empty($text)) {
            // return 'n-a';
        }
        // echo $text; die;
        $slug = self::GetUniqueSlug($tbl_name, $id_field, $row_id, $text);
        // echo $slug; die;
        return $slug;
    }

    public static function GetUniqueSlug($tbl_name, $id_field, $row_id = '', $slug = '', &$num = '')
    {

        //prd($num);

        $new_slug = $slug . $num;

        $query = DB::table($tbl_name);
        $query->where('slug', $new_slug);
        $row = $query->first();

        if (empty($row)) {
            $slug = $new_slug;
        } else {
            // echo 'here'; die;
            if (!empty($row_id) && $row->$id_field == $row_id) {
                $slug = $new_slug;
            } else {
                $num = (int)$num + 1;
                $slug = self::GetUniqueSlug($tbl_name, $id_field, $row_id, $new_slug, $num);
            }
        }
        return $slug;
    }

    public static function getStatusStr($status)
    {

        if (is_numeric($status) && strlen($status) > 0) {
            if ($status == 1) {
                $status = 'Active';
            } else {
                $status = 'Inactive';
            }

        }
        return $status;
    }

    public static function getStates($country_id)
    {

        $states = State::where('country_id', $country_id)->where('is_delete', 0)->get();
        return $states;
    }

    public static function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->where('is_delete', 0)->get();
        return $cities;
    }

    public static function getCategoryName($category_id)
    {
        $category = Category::find($category_id);
        return $category->name ?? '';

    }

    public static function getQRUserDetails($qr_code)
    {
        $html = '';
        $user = User::where('id', $qr_code->user_id)->first();
        if (!empty($user)) {
            $html .= "Name : " . $user->name ?? '';
            $html .= '<br>';
            $html .= "Phone : " . $user->phone ?? '';
            $html .= '<br>';
            $html .= "Address : " . $user->address ?? '';
            $html .= '<br>';
        }


        return $html;

    }

    public static function getQRDetails($qr_data)
    {
        $html = '';
        $category = Category::where('id', $qr_data->product_id)->first();
        if (!empty($qr_data)) {
            if (!empty($category)) {
                if ($category->is_vehicle == 1) {

                    $image = self::getImageUrl('qr_codes', $qr_data->image);
                    $html .= "Vehicle Name : " . $qr_data->vehicle_name ?? '';
                    $html .= '<br>';
                    $html .= "Vehicle Type : " . $qr_data->vehicle_type ?? '';
                    $html .= '<br>';
                    $html .= "Vehicle No : " . $qr_data->vehicle_no ?? '';
                    $html .= '<br>';
                    $html .= "Image : " . '<a href=' . $image . ' target="_blank"><img src=' . $image . ' width="50px" height="50px"></a>';
                    $html .= '<br>';
                    $html .= "Activated On : " . $qr_data->activated_at ?? '';
                    $html .= '<br>';
                } else {
                    $html .= "Name : " . $qr_data->name ?? '';
                    $html .= '<br>';
                    $html .= "Phone : " . $qr_data->phone ?? '';
                    $html .= '<br>';
                    $html .= "Address : " . $qr_data->address ?? '';
                    $html .= '<br>';
                    $html .= "Address : " . $qr_data->address ?? '';
                    $html .= '<br>';
                    $html .= "Image : " . '<a href=' . $image . ' target="_blank"><img src=' . $image . ' width="50px" height="50px"></a>';
                    $html .= '<br>';
                    $html .= "Activated On : " . $qr_data->activated_at ?? '';
                    $html .= '<br>';
                }
            }
        }

        return $html;

    }

    public static function getVendors()
    {
        $vendors = Vendors::where('is_delete', 0)->get();
        return $vendors;
    }


    public static function getAttributes()
    {
        $attributes = Attributes::where('is_delete', 0)->get();
        return $attributes;
    }

    public static function checkVendorPrice($vendor_id, $products_id, $varient_id)
    {
        if (!empty($vendor_id) && !empty($products_id) && !empty($varient_id)) {
            return VendorProductPriceAlias::where('vendor_id', $vendor_id)->where('product_id', $products_id)->where('varient_id', $varient_id)->first();
        } else {
            return null;
        }
    }


    public static function checkVendorUpdatedPrice($vendor_id, $products_id, $varient_id)
    {

        if (!empty($vendor_id) && !empty($products_id) && !empty($varient_id)) {
            return VendorProductPriceAlias::where('vendor_id', $vendor_id)->where('product_id', $products_id)->where('id', $varient_id)->first();
        } else {
            return null;
        }
    }


    public static function getProductIds($vendor_id)
    {
        return VendorProductPriceAlias::where('vendor_id', $vendor_id)->groupBy('product_id')->pluck('product_id')->toArray();
    }

    public static function getBrands()
    {
        $brands = Brand::where('is_delete', 0)->get();
        return $brands;

    }

    public static function getProductName($product_id)
    {
        $product = Products::find($product_id);
        return $product->name ?? '';

    }

    public static function getAttributeName($attributes_id)
    {
        $product = Attributes::find($attributes_id);
        return $product->name ?? '';

    }

    public static function updateOrderStatus($order_id, $status)
    {
        $dbArray = [];
        $dbArray['order_id'] = $order_id;
        $dbArray['status'] = $status;
        $dbArray['updated_by'] = 'admin';
        OrderStatus::insert($dbArray);
        return true;
    }

    public static function getProductVarients($product_id)
    {
        $varients = [];
        $varients = ProductVarient::where('product_id', $product_id)->where('is_delete', 0)->where('status', 1)->get();
        return $varients;
    }

    public static function getVendorProductVarients($vendor_id, $product_id)
    {
        $varients = VendorProductPrice::where('vendor_id', $vendor_id)->where('product_id', $product_id)->where('is_delete', 0)->get();
        return $varients;
    }

    public static function getAddressDetails($addressID)
    {
        $user_address = UserAddress::find($addressID);
        return $user_address;

    }

    public static function getVendorProductVarientsSingle($vendor_id, $product_id, $varient_id)
    {
        $varients = VendorProductPrice::where('vendor_id', $vendor_id)->where('product_id', $product_id)->where('varient_id', $varient_id)->where('is_delete', 0)->first();
        return $varients;
    }

    public static function getVendorProductSingleVarients($vendor_id, $product_id, $varient_id)
    {
        $varients = VendorProductPrice::where('vendor_id', $vendor_id)->where('product_id', $product_id)->where('varient_id', $varient_id)->where('is_delete', 0)->first();
        return $varients;
    }

    public static function getAdminProductVarients($product_id)
    {
        $varients = ProductVarient::where('product_id', $product_id)->where('is_delete', 0)->get();
        return $varients;
    }


    public static function getManufacturer()
    {
        $manufacture = Manufacturer::where('is_delete', 0)->get();
        return $manufacture;

    }

    public static function getDates($no): array
    {
        $dateArr = [];
        $startDate = new DateTime("-{$no} days");
        $endDate = new DateTime();
        for ($date = $startDate; $date <= $endDate; $date->modify('+1 day')) {
            $dateArr[] = $date->format("Y-m-d");
        }
        return $dateArr;
    }

    public static function getCategories()
    {
        $categories = Category::where('parent_id', 0)->where('status', 1)->where('is_delete', 0)->get();
        return $categories;

    }

    public static function getOrderStatus($orderID)
    {
        $status = '';
        $order = Order::where('id', $orderID)->first();
        if (!empty($order)) {
            if ($order->status == 'PLACED') {
                $status = '<span class="badge bg-secondary">Pending</span>';
            }
            if ($order->status == 'PROCESSING') {
                $status = '<span class="badge bg-info">Processing</span>';
            }
            if ($order->status == 'CONFIRM') {
                $status = '<span class="badge bg-success">Confirmed</span>';
            }
            if ($order->status == 'CANCEL') {
                $status = '<span class="badge bg-danger">Canceled</span>';
            }
            if ($order->status == 'OUT_FOR_DELIVERY') {
                $status = '<span class="badge bg-warning">Out For Delivery</span>';
            }
            if ($order->status == 'DELIVERED') {
                $status = '<span class="badge bg-success">DELIVERED</span>';
            }
            if ($order->status == 'ALLOCATED') {
                $status = '<span class="badge bg-secondary">Allocated</span>';
            }
        }

        return $status;
    }

    public static function getOrderItemsWithProduct($orderID)
    {
        $order_items = OrderItems::where('order_id', $orderID)->get();
//        $order_items = OrderItems::select('products.*', 'order_items.id as order_items_id', 'order_items.variant_id', 'order_items.qty', 'order_items.price', 'order_items.net_price', 'order_items.status')->leftJoin('products', 'products.id', '=', 'order_items.product_id')
//            ->where('order_items.order_id', $orderID)->where('order_items.is_delete', 0)->get();
        return $order_items;
    }


    public static function getVendorName($vendor_id)
    {
        $vendors = Vendors::where('id', $vendor_id)->first();
        return $vendors->name ?? '';
    }

    public static function getVendorDetails($vendor_id)
    {
        $vendors = Vendors::where('id', $vendor_id)->first();
        return $vendors ?? '';
    }

    public static function getBrandName($brand_id)
    {
        $vendors = Brand::where('id', $brand_id)->first();
        return $vendors->brand_name ?? '';
    }

    public static function getManufactureName($man_id)
    {
        $vendors = Manufacturer::where('id', $man_id)->first();
        return $vendors->name ?? '';
    }

    public static function getQuotePorter($orders)
    {
        $quoteData = [];
        $store = CustomHelper::getVendorDetails($orders->vendor_id ?? '');
        $source_lat = $store->latitude ?? '';
        $source_lon = $store->longitude ?? '';

        $dest_lat = $orders->latitude ?? '';
        $dest_lon = $orders->longitude ?? '';
        $data = [
            'pickup_details' => [
                'lat' => $source_lat,
                'lng' => $source_lon
            ],
            'drop_details' => [
                'lat' => $dest_lat,
                'lng' => $dest_lon
            ],
            'customer' => [
                'name' => $orders->customer_name ?? "Guest",
                'mobile' => [
                    'country_code' => '+91',
                    'number' => $orders->contact_no ?? "9999999999",
                ]
            ]
        ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://pfe-apigw.porter.in/v1/get_quote',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: aa850081-1f6d-4786-8a8f-211ed6ed1be8',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);


        if (!empty($response->vehicles)) {
            foreach ($response->vehicles as $vehicle) {
                $type = $vehicle->type ?? null;
                if ($type == "2 Wheeler") {
                    $price = $vehicle->fare->minor_amount ?? null;
                    $eta = $vehicle->eta->value . ' ' . $vehicle->eta->unit;
                    $priceInRupees = $price / 100;
                    $quoteData['price'] = $priceInRupees;
                    $quoteData['eta'] = $eta;
                }

            }
        }
        return $quoteData;
    }


    public static function trackPorterOrder($exist = [])
    {
        if (!empty($exist->porter_order_id)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://pfe-apigw.porter.in/v1/orders/' . $exist->porter_order_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'x-api-key: aa850081-1f6d-4786-8a8f-211ed6ed1be8',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($response);
            if (!empty($response)) {
                $dbArray = [];
                $dbArray['order_status'] = $response->status??'';
                $dbArray['order_details_porter'] = json_encode($response);
                DB::table('order_courier')->where('id', $exist->id)->update($dbArray);
            }
        }

    return $exist;
    }

    public static function bookPorterShipment($orders)
    {
        $store = CustomHelper::getVendorDetails($orders->vendor_id ?? '');
        $source_lat = $store->latitude ?? '';
        $source_lon = $store->longitude ?? '';

        $dest_lat = $orders->latitude ?? '';
        $dest_lon = $orders->longitude ?? '';
        $data = [
            'request_id' => '2 Wheeler',
            'delivery_instructions' => [
                'instructions_list' => [
                    [
                        'type' => 'text',
                        'description' => 'handle with care'
                    ],
                    [
                        'type' => 'text',
                        'description' => 'Order ' . $orders->id
                    ]
                ]
            ],
            'pickup_details' => [
                'address' => [
                    'apartment_address' => '27',
                    'street_address1' => $store->address ?? '',
                    'street_address2' => $store->address ?? '',
                    'landmark' => $store->address ?? '',
                    'city' => '',
                    'state' => '',
                    'pincode' => $store->pincode ?? '',
                    'country' => 'India',
                    'lat' => $source_lat,
                    'lng' => $source_lon,
                    'contact_details' => [
                        'name' => $store->name ?? '',
                        'phone_number' => $store->user_phone ?? '',
                    ]
                ]
            ],
            'drop_details' => [
                'address' => [
                    'apartment_address' => $orders->house_no ?? '',
                    'street_address1' => $orders->landmark ?? '',
                    'street_address2' => $orders->location ?? '',
                    'landmark' => $orders->landmark ?? '',
                    'city' => '',
                    'state' => '',
                    'pincode' => "",
                    'country' => 'India',
                    'lat' => $dest_lat,
                    'lng' => $dest_lon,
                    'contact_details' => [
                        'name' => $orders->customer_name ?? "Guest",
                        'phone_number' => $orders->contact_no ?? "9999999999",
                    ]
                ]
            ]
        ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://pfe-apigw.porter.in/v1/orders/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: aa850081-1f6d-4786-8a8f-211ed6ed1be8',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        if (!empty($response)) {
            $dbArray = [];
            $dbArray['logistics'] = 'porter';
            $dbArray['order_id'] = $orders->id ?? '';
            $dbArray['request_id'] = $response->request_id ?? '';
            $dbArray['porter_order_id'] = $response->order_id ?? '';
            $dbArray['estimated_pickup_time'] = $response->estimated_pickup_time ?? '';
            $dbArray['tracking_url'] = $response->tracking_url ?? '';
            $dbArray['porter_data'] = json_encode($response);
            DB::table('order_courier')->insert($dbArray);
        }
    }


    public static function cancelPorterShipment($order)
    {
        $exist = DB::table('order_courier')->where("order_id", $order->id)->first();
        if (!empty($exist)) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://pfe-apigw.porter.in/v1/orders/' . $exist->porter_order_id . '/cancel',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'x-api-key: aa850081-1f6d-4786-8a8f-211ed6ed1be8'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        }
    }

    public static function getCategoryDetails($category_id)
    {
        $categories = Category::where('id', $category_id)->where('status', 1)->where('is_delete', 0)->first();
        return $categories;

    }

    public static function getStatusHTML($status, $tbl_id = 0, $class = '', $id = '', $type = 'status', $activeTxt = 'Active', $inActiveTxt = 'In-active')
    {

        $status_str = '';

        if (is_numeric($status) && strlen($status) > 0) {
            $status_name = '';
            $a_label = '';

            if ($status == 1) {
                $status_name = $activeTxt;
                $a_label = 'label-success';
            } else {
                $status_name = $inActiveTxt;
                $a_label = 'label-warning';
            }
            $status_str = '<a href="javascript:void(0)" class="label ' . $a_label . ' ' . $class . '" id="' . $id . '" data-id="' . $tbl_id . '" data-status="' . $status . '" data-type="' . $type . '" >' . $status_name . '</a>';
        }

        if (empty($status_str)) {
            $status_str = $status;
        }

        return $status_str;
    }

    public static function getBankDetails($bank_id)
    {
        $bank = Bank::find($bank_id);
        return $bank;
    }

    public static function getBranchesDetails($bank_id)
    {
        $bank = Branch::find($bank_id);
        return $bank;
    }

    public static function getCaseTransactions($case_id)
    {
        $transactions = Transaction::where('case_id', $case_id)->get();
        return $transactions;
    }

    public static function getCaseDetails($case_id)
    {
        $case = Cases::where('id', $case_id)->first();
        return $case;

    }

    public static function getRoles()
    {
        $roles = Roles::where('is_delete', 0)->get();
        return $roles;

    }

    public static function updateInvoiceAmount($case_id): true
    {
        $case = Cases::where('id', $case_id)->first();
        $bill_amount = CaseInvoices::where('case_id', $case_id)->sum('amount');
        $gst_amount = CaseInvoices::where('case_id', $case_id)->sum('gst');
        $total_amount = CaseInvoices::where('case_id', $case_id)->sum('total');
        if (!empty($case)) {
            $case->bill_amount = $bill_amount ?? 0;
            $case->gst_amount = $gst_amount ?? 0;
            $case->total_amount = $total_amount ?? 0;
            $case->save();
        }
        return true;
    }

    public static function getPropertyOwnersType()
    {
        $types = PropertyOwnerType::where('is_delete', 0)->where('status', 1)->get();
        return $types;

    }

    public static function getSubCategory($category_id)
    {
        $category = Category::where('parent_id', $category_id)->get();
        return $category;

    }

    public static function generateQrCode($qr_unique_id)
    {
        return env('QR_URL') . $qr_unique_id;
    }

    public static function calculateAgo($time)
    {

        $timestamp = strtotime($time);
        $time = Carbon::createFromTimestamp($timestamp);
        $timeAgo = $time->diffForHumans();
        return $timeAgo;

    }

    public static function generateUniqueIdForQr()
    {
        $length = 10;
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[self::crypto_rand_secure(0, $max - 1)];
        }

        return $token;

    }

    public static function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int)($log / 8) + 1; // length in bytes
        $bits = (int)$log + 1; // length in bits
        $filter = (int)(1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    public static function getAllOrderStatus(): array
    {
        $dbArray = [
            'PLACED', 'CONFIRM', 'CANCEL', 'OUT_FOR_DELIVERY', 'PARTIAL_DELIVERED', 'DELIVERED'
        ];

        return $dbArray;
    }

    public static function getSellerRoles($seller_id)
    {
        $roles = SellerRoles::where('vendor_id', $seller_id)->where('status', 1)->where('is_delete', 0)->get();
        return $roles;

    }

    public static function uploadImage($file, $path): string
    {
        $fileName = $path . time() . $file->getClientOriginalName();
        $fileName = str_replace(" ", "-", $fileName);
        $is_s3 = env('IS_S3') ?? 0;
        if ($is_s3 == 0) {
            $path = dirname(__DIR__, 3) . '/images/' . $path;
            $path = $file->move($path, $fileName);
        }
        if ($is_s3 == 1) {
            $path = $path . '/' . $fileName;
            Storage::disk('s3')->put($path, file_get_contents($file));
            $path = Storage::disk('s3')->url($path);

        }
        return $fileName;
    }

    public static function getAllPropertyDocuments($case_id)
    {
        $documents = PropertyDocuments::where('case_id', $case_id)->get();
        if (!empty($documents)) {
            foreach ($documents as $doc) {
                $doc_name = DocumentRequired::find($doc->doc_id);
                $doc->document_name = $doc_name->name ?? '';
            }
        }
        return $documents;

    }

    public static function getBanks()
    {
        $banks = Bank::where('status', 1)->where('is_delete', 0)->orderBy('name')->get();
        return $banks;

    }

    /**
     * @return array
     */
    public static function getPropertyTypeName($id)
    {
        $property_type = PropertyType::where('id', $id)->first();
        return $property_type->name ?? '';

    }

    public static function getAllPropertyOwners($case_id)
    {
        $property_owner = PropertyOwners::where('case_id', $case_id)->get();
        return $property_owner;
    }

    public static function getAllPropertyBorrower($case_id)
    {
        return PropertyBorrower::where('case_id', $case_id)->get();
    }

    public static function getPropertyTypes()
    {
        $property = PropertyType::where('status', 1)->where('is_delete', 0)->get();
        return $property;

    }

    public static function getDocumentRequired($property_type = '')
    {
        $document_required = [];
        if (!empty($property_type)) {
            $document_required = DocumentRequired::where('property_type', $property_type)->get();
        }
        return $document_required;

    }

    public static function getBranches($bank_id = '')
    {
        $branches = [];
        if (!empty($bank_id)) {
            $branches = Branch::where('bank_id', $bank_id)->where('status', 1)->where('is_delete', 0)->orderBy('name')->get();
        }

        return $branches;

    }

    public static function CheckAndFormatDate($date, $toFormat = 'Y-m-d H:i:s', $fromFormat = '')
    {
        $new_date = $date;

        $date = preg_replace(array('/\//', '/\./'), '-', $date);

        //echo $date; die;

        $new_date = self::DateFormat($date, $toFormat, $fromFormat = 'y-m-d');

        return $new_date;
    }

    public static function DateFormat($date, $toFormat = 'Y-m-d H:i:s', $fromFormat = '')
    {

        $new_date = $date;

        $formatArr = array('d-m-y', 'd-m-Y', 'd/m/Y', 'd/m/y', 'd/m/Y H:i:s', 'd/m/y H:i:s', 'd/m/Y H:i A', 'd/m/y H:i A',);

        if (empty($toFormat)) {
            $toFormat = 'Y-m-d H:i:s';
        }

        if ($date != '0000-00-00 00:00:00' && $date != '0000-00-00' && $date != '') {
            if (empty($fromFormat) || $fromFormat == '' || !in_array($fromFormat, $formatArr)) {
                $new_date = date($toFormat, strtotime($date));
            } elseif ($fromFormat == 'd-m-y' || $fromFormat == 'd-m-Y') {
                $date_arr = explode('-', $date);
                $date_str = $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];
                $new_date = date($toFormat, strtotime($date_str));
            } elseif ($fromFormat == 'd/m/Y' || $fromFormat == 'd/m/y') {
                $datetime_arr = explode(' ', $date);

                $date_arr = explode('/', $datetime_arr[0]);
                $date_str = $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];

                $new_date = date($toFormat, strtotime($date_str));
            } elseif ($fromFormat == 'd/m/Y H:i:s' || $fromFormat == 'd/m/y H:i:s') {
                $datetime_arr = explode(' ', $date);

                $time = $datetime_arr[1];

                $date_arr = explode('/', $datetime_arr[0]);
                $date_str = $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];

                $new_date = date($toFormat, strtotime($date_str . ' ' . $time));
            } elseif ($fromFormat == 'd/m/Y H:i A' || $fromFormat == 'd/m/y H:i A') {
                $datetime_arr = explode(' ', $date);

                $time = $datetime_arr[1] . ' ' . $datetime_arr[2];

                $date_arr = explode('/', $datetime_arr[0]);
                $date_str = $date_arr[2] . '-' . $date_arr[1] . '-' . $date_arr[0];

                $new_date = date($toFormat, strtotime($date_str . ' ' . $time));
            }

        } else {
            $new_date = '';
        }

        return $new_date;
    }

    public static function DateDiff($date1, $date2)
    {

        $date_diff = '';

        $date1 = self::DateFormat($date1, 'Y-m-d');
        $date2 = self::DateFormat($date2, 'Y-m-d');

        if (!empty($date1) && !empty($date2)) {
            $date1 = date_create($date1);
            $date2 = date_create($date2);
            $diff = date_diff($date1, $date2);

            $date_diff = $diff->format("%a");
        }
        return $date_diff;
    }

    public static function getStartAndEndDateOfWeek($week, $year, $format = 'Y-m-d H:i:s')
    {
        $dateTime = new \DateTime();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format($format);
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format($format);
        return $result;
    }

    /* Note: this function requires laravel intervention/image package */
    public static function UploadImage1($file, $path, $ext = '', $width = 768, $height = 768, $is_thumb = false, $thumb_path, $thumb_width = 300, $thumb_height = 300)
    {

        if (empty($ext)) {
            $ext = 'jpg,jpeg,png,gif,pdf';
        }

        list($img_width, $img_height, $type, $attr) = getimagesize($file);
        //prd($image_info);

        if ($img_width < $width) {
            $width = $img_width;
        }

        if ($img_height < $height) {
            $height = $img_height;
        }

        //echo url('public/uploads'); die;

        $result['success'] = false;

        $result['org_name'] = '';
        $result['file_name'] = '';

        if ($file) {

            //$path = 'designs/';
            //$thumb_path = 'designs/thumb/';

            $validator = Validator::make(['file' => $file], ['file' => 'mimes:' . $ext]);

            if ($validator->passes()) {
                $handle = fopen($file, "r");
                $opening_bytes = fread($handle, filesize($file));

                fclose($handle);

                if (strlen(strpos($opening_bytes, '<?php')) > 0 && (strpos($opening_bytes, '<?php') >= 0 || strpos($opening_bytes, '<?PHP') >= 0)) {
                    $result['errors']['file'] = "Invalid image!";
                } else {

                    $extension = $file->getClientOriginalExtension();
                    $fileOriginalName = $file->getClientOriginalName();
                    $fileOriginalName = str_replace(' ', '', $fileOriginalName);
                    $fileName = date('dmyhis') . '-' . $fileOriginalName;

                    $is_uploaded = Image::make($file)->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(public_path('storage/' . $path . $fileName));

                    if ($is_uploaded) {

                        $result['success'] = true;

                        if ($is_thumb) {
                            $thumb = Image::make($file)->resize($thumb_width, $thumb_height, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save(public_path('storage/' . $thumb_path . $fileName));
                        }

                        $result['org_name'] = $fileOriginalName;
                        $result['file_name'] = $fileName;
                        $result['extension'] = $extension ?? '';
                    }
                }
            } else {
                $result['errors'] = $validator->errors();
            }

        }

        return $result;
    }

    public static function UploadFile($file, $path, $ext = '')
    {

        if (empty($ext)) {
            $ext = 'jpg,jpeg,png,gif,doc,docx,txt,pdf,xls,xlsx,mp4';
        }

        $path = 'public/' . $path;

        $result['success'] = false;

        $result['org_name'] = '';
        $result['file_name'] = '';
        $result['file_path'] = '';

        if ($file) {

            $validator = Validator::make(['file' => $file], ['file' => 'mimes:' . $ext]);

            if ($validator->passes()) {
                $handle = fopen($file, "r");
                $opening_bytes = fread($handle, filesize($file));

                fclose($handle);

                if (strlen(strpos($opening_bytes, '<?php')) > 0 && (strpos($opening_bytes, '<?php') >= 0 || strpos($opening_bytes, '<?PHP') >= 0)) {
                    $result['errors']['file'] = "Invalid file!";
                } else {
                    $extension = $file->getClientOriginalExtension();
                    $fileOriginalName = $file->getClientOriginalName();
                    $fileName = date('dmyhis') . '-' . trim($fileOriginalName);

                    $path = $file->storeAs($path, $fileName);

                    if ($path) {
                        $result['success'] = true;

                        $result['org_name'] = $fileOriginalName;
                        $result['file_name'] = $fileName;
                        $result['file_path'] = $path;
                        $result['extension'] = $extension;
                    }
                }
            } else {
                $result['errors'] = $validator->errors();
            }

        }
        return $result;

    }

    public static function get2FASecret()
    {
        $settings = DB::table('settings')->where('id', 1)->first();
        return $settings->secret_key ?? '';
    }

    public static function get2FAQRImage(): string
    {
        $g = new GoogleAuthenticator();
        $secret = self::get2FASecret();
        return $g->getURL('SurakshaCode', 'SurakshaCode@gmail.com', $secret);
    }

    public static function getRoleName($role_id): string
    {
        $name = '';
        if ($role_id == 0) {
            $name = 'Super Admin';
        } else {
            $role = Roles::find($role_id);
            $name = $role->name ?? '';
        }

        return $name;

    }

    public static function getSellerRoleName($role_id): string
    {
        $name = '';
        $role = SellerRoles::find($role_id);
        $name = $role->name ?? '';


        return $name;

    }

    public static function getCategorySale($category_id)
    {
        $saleArr = [];
        $percent = 0;
        $total_sale = OrderItems::where('product_id', $category_id)->where('status', 'DELIVERED')->where('is_delete', 0)->sum('net_price');
        $total_qty = OrderItems::where('product_id', $category_id)->where('status', 'DELIVERED')->where('is_delete', 0)->sum('qty');
        $total_activated = QRCodes::where('product_id', $category_id)->where('is_delete', 0)->orderBy('id', 'desc')->where('is_activated', 1)->count();;
        if ($total_sale > 0) {
            $percent = ($total_activated / $total_qty) * 100;
        }


        $saleArr['total_sale'] = $total_sale;
        $saleArr['percent'] = number_format($percent, 2, ".", ".");
        return $saleArr;
    }

    public static function getMonthBack($num): array
    {
        $currentDate = new DateTime();
        $last12Months = [];
        for ($i = 0; $i < 12; $i++) {
            $monthDate = clone $currentDate;
            $monthDate->modify("-$i months");
            $formattedDate = $monthDate->format('M Y');
            $month = $monthDate->format('m');
            $year = $monthDate->format('Y');
            $date = $monthDate->format('Y-m-d');
            $dbArray = [];

            $total_qr_code = QRCodes::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('is_delete', 0)->count();
            $activated_qr_code = QRCodes::whereMonth('activated_at', $month)->whereYear('activated_at', $year)->where('is_activated', 1)->where('is_delete', 0)->count();
            $dbArray['formattedDate'] = $formattedDate;
            $dbArray['total_qr'] = $total_qr_code;
            $dbArray['activated_qr'] = $activated_qr_code;
            $dbArray['date'] = $date;
            $last12Months[] = $dbArray;
        }
        $last12Months = array_reverse($last12Months);
        return $last12Months;
    }

    public static function getEventImages($event)
    {
        $images = EventImages::where('event_id', $event->id)->get();
        if (!empty($images)) {
            foreach ($images as $image) {
                $image->file = self::getImageUrl('events', $image->file);
            }
        }
        return $images;

    }


    public static function UploadFileNew($file, $path, $ext = '')
    {

        if (empty($ext)) {
            $ext = 'jpg,jpeg,png,gif,doc,docx,txt,pdf,xls,xlsx,mp4';
        }

        $path = 'public/' . $path;

        $result['success'] = false;

        $result['org_name'] = '';
        $result['file_name'] = '';
        $result['file_path'] = '';

        if ($file) {

            $validator = Validator::make(['file' => $file], ['file' => 'required']);

            if ($validator->passes()) {
                $handle = fopen($file, "r");
                $opening_bytes = fread($handle, filesize($file));

                fclose($handle);

                if (strlen(strpos($opening_bytes, '<?php')) > 0 && (strpos($opening_bytes, '<?php') >= 0 || strpos($opening_bytes, '<?PHP') >= 0)) {
                    $result['errors']['file'] = "Invalid file!";
                } else {
                    $extension = $file->getClientOriginalExtension();
                    $fileOriginalName = $file->getClientOriginalName();
                    // $fileName = date('dmyhis').'-'.$fileOriginalName;
                    $fileName = date('dmyhis') . '-' . str_replace(' ', '', $fileOriginalName);;

                    $path = $file->storeAs($path, $fileName);

                    if ($path) {
                        $result['success'] = true;

                        $result['org_name'] = $fileOriginalName;
                        $result['file_name'] = $fileName;
                        $result['file_path'] = $path;
                        $result['extension'] = $extension;
                    }
                }
            } else {
                $result['errors'] = $validator->errors();
            }

        }
        return $result;
        // print_r($result);
        // die;
    }


    public static function WebsiteSettings($name)
    {

        $value = '';
        $settings = DB::table('website_settings')->where('name', $name)->first();

        if (!empty($settings) && isset($settings->value)) {
            $value = $settings->value;
        }
        return $value;
    }


    public static function websiteSettingsArray($nameArr)
    {

        $settings = '';

        if (is_array($nameArr) && !empty($nameArr) && count($nameArr) > 0) {
            $settings = DB::table('website_settings')->whereIn('name', $nameArr)->get()->keyBy('name');
            //prd($settings);
        }
        return $settings;
    }


    public static function formatUserAddress($userAddr)
    {

        $addressArr = [];

        if (!empty($userAddr) && count($userAddr) > 0) {

            $address = $userAddr->address;
            $locality = $userAddr->locality;
            $pincode = $userAddr->pincode;

            if (!empty($address)) {
                $addressArr[] = $address;
            }
            if (!empty($locality)) {
                $addressArr[] = $locality;
            }

            $addressState = '';
            $addressCity = '';

            if (isset($userAddr->userState)) {
                $addressState = $userAddr->userState;
            } elseif ($userAddr->addressState) {
                $addressState = $userAddr->addressState;
            }

            if (isset($userAddr->userCity)) {
                $addressCity = $userAddr->userCity;
            } elseif ($userAddr->addressCity) {
                $addressCity = $userAddr->addressCity;
            }

            /*$addressState = ($userAddr->addressState)?$userAddr->addressState:'';
            $addressCity = ($userAddr->addressCity)?$userAddr->addressCity:'';*/

            if (!empty($addressState) && count($addressState) > 0) {
                if (!empty($addressState->name)) {
                    $addressArr[] = $addressState->name;
                }
            }

            if (!empty($addressCity) && count($addressCity) > 0) {
                if (!empty($addressCity->name)) {
                    $addressArr[] = $addressCity->name;
                }
            }

            if (!empty($pincode)) {
                $addressArr[] = 'Pincode:' . $pincode;
            }

        }
        return $addressArr;
    }


    public static function formatOrderAddress($order, $isBilling = true, $isPhone = true, $isEmail = true)
    {

        $orderAddrArr = [];

        if (!empty($order) && count($order) > 0) {

            $name = '';
            $address = '';
            $locality = '';
            $pincode = '';
            $cityName = '';
            $stateName = '';
            $countryName = '';
            $phone = '';
            $email = '';

            if ($isBilling) {

                $name = $order->billing_name;
                $address = $order->billing_address;
                $locality = $order->billing_locality;
                $pincode = $order->billing_pincode;

                $billingCity = $order->billingCity;
                $billingState = $order->billingState;
                $billingCountry = $order->billingCountry;

                if (isset($billingCity->name) && !empty($billingCity->name)) {
                    $cityName = $billingCity->name;
                }
                if (isset($billingState->name) && !empty($billingState->name)) {
                    $stateName = $billingState->name;
                }
                if (isset($billingCountry->name) && !empty($billingCountry->name)) {
                    $countryName = $billingCountry->name;
                }

                $phone = $order->billing_phone;
                $email = $order->billing_email;

            } else {
                $name = $order->shipping_name;
                $address = $order->shipping_address;
                $locality = $order->shipping_locality;
                $pincode = $order->shipping_pincode;

                $shippingCity = $order->shippingCity;
                $shippingState = $order->shippingState;
                $shippingCountry = $order->shippingCountry;


                if (isset($shippingCity->name) && !empty($shippingCity->name)) {
                    $cityName = $shippingCity->name;
                }
                if (isset($shippingState->name) && !empty($shippingState->name)) {
                    $stateName = $shippingState->name;
                }
                if (isset($shippingCountry->name) && !empty($shippingCountry->name)) {
                    $countryName = $shippingCountry->name;
                }

                $phone = $order->shipping_phone;
                $email = $order->shipping_email;
            }

            if (!empty($name)) {
                $orderAddrArr[] = $name;
            }

            if (!empty($address)) {
                $orderAddrArr[] = $address;
            }

            if (!empty($cityName) && !empty($pincode)) {
                $cityName = $cityName . '-' . $pincode;
            }

            $cityArr = [];

            if (!empty($locality)) {
                $cityArr[] = $locality;
            }
            if (!empty($cityName)) {
                $cityArr[] = $cityName;
            }

            $orderAddrArr[] = implode(', ', $cityArr);

            $countryArr = [];

            if (!empty($stateName)) {
                $countryArr[] = $stateName;
            }
            if (!empty($countryName)) {
                $countryArr[] = $countryName;
            }

            $orderAddrArr[] = implode(', ', $countryArr);

            if ($isPhone && !empty($phone)) {
                $orderAddrArr[] = '<span class="addr_label">Phone: </span>' . $phone;
            }

            if ($isEmail && !empty($email)) {
                $orderAddrArr[] = '<span class="addr_label">Email: </span>' . $email;
            }

        }
        return $orderAddrArr;
    }


    public static function GetCountry($id = 0, $col_name = '')
    {

        $value = '';

        if (is_numeric($id) && $id > 0) {
            $country = DB::table('countries')->where('id', $id)->first();

            if (!empty($col_name) && isset($country->{$col_name})) {
                $value = $country->{$col_name};
            } else {
                $value = $country;
            }
        }

        return $value;
    }

    public static function getCategory()
    {
        $category = Category::where('parent_id', 0)->get();
        return $category;

    }

    public static function getParentCategory($parent_id)
    {
        $category = Category::where('id', $parent_id)->first();
        return $category;
    }

    public static function GetParentCategoryold($category)
    {
        $parent = '';
        if (isset($category->parent) && count($category->parent) > 0) {
            $parent = $category->parent;
        }

        //prd($parents_arr);
        return $parent;
    }

    public static function GetParentCategories()
    {
        $categories = Category::where('parent_id', 0)->where('status', 1)->where('is_delete', 0)->get();
        return $categories;

    }

    public static function getCategoryCommission($vendor_id, $category_id)
    {
        return CategoryWiseCommission::where('vendor_id', $vendor_id)->where('category_id', $category_id)->first();

    }

    public static function CategoriesMenuChild($childCategories, $className = '', $idName = '')
    {
        $menu_list_child = '';

        if (!empty($childCategories) && count($childCategories) > 0) {
            $menu_list_child .= '<ul class="' . $className . '" id="' . $idName . '">';

            foreach ($childCategories as $childCat) {

                $childrenCat = $childCat->children;

                $cat_url = url('designs?cat=' . $childCat->slug);

                if (isset($childrenCat) && count($childrenCat) > 0) {
                    $cat_url = 'javascript:void(0)';
                }

                $menu_list_child .= '<li><a href="' . $cat_url . '">' . $childCat->name . '</a>';

                if (isset($childrenCat) && count($childrenCat) > 0) {

                    $childrenCat = $childrenCat->sortBy('sort_order');

                    $menu_list_child .= self::CategoriesMenuChild($childrenCat, $className, $idName);
                }
                $menu_list_child .= '</li>';

            }

            $menu_list_child .= '</ul>';
        }

        return $menu_list_child;
    }


    private static $parentCatArr = [];

    public static function categoryParentForBreadcrumb($category)
    {

        if (isset($category->parent) && count($category->parent) > 0) {
            $parent_category = $category->parent;

            self::$parentCatArr[] = $parent_category->toArray();

            if (isset($parent_category->parent) && count($parent_category->parent) > 0) {
                self::categoryParentForBreadcrumb($parent_category);
            }

        }
    }


    public static function CategoryBreadcrumb($category, $first_uri, $first_uri_name, $is_last_link = false)
    {

        self::$parentCatArr = [];

        $BackUrl = self::BackUrl();

        //prd($category->toArray());
        $breadcrumb = '';

        if (!empty($first_uri_name)) {
            $breadcrumb .= '<a href="' . url($first_uri) . '" class="btn-link" >' . $first_uri_name . '</a>';
        }

        $hierarchy_arr = [];

        if (!empty($category) && count($category) > 0) {

            self::categoryParentForBreadcrumb($category);

            $hierarchy_arr = self::$parentCatArr;

            $hierarchy_arr_rev = array_reverse($hierarchy_arr);

            //prd($hierarchy_arr_rev);

            if (!empty($hierarchy_arr_rev) && count($hierarchy_arr_rev) > 0) {
                foreach ($hierarchy_arr_rev as $cat) {

                    $cat = (object)$cat;

                    if (isset($cat->name)) {
                        if (!empty($first_uri_name)) {
                            $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
                        }

                        $breadcrumb .= '<a href="' . url($first_uri . '&parent_id=' . $cat->id) . '" class="btn-link" >' . $cat->name . '</a>';
                        $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
                    }
                }
                //$breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
            } elseif (!empty($first_uri_name)) {
                $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
            }
            if ($is_last_link) {
                $breadcrumb .= '<a href="' . url('admin/categories?parent_id=' . $category->id . '&back_url=' . $BackUrl) . '">' . $category->name . '</a>';
            } else {
                $breadcrumb .= '<a href="javascript:void(0)">' . $category->name . '</a>';
            }

        }

        return $breadcrumb;
    }


    public static function CategoryBreadcrumbFrontend($category, $first_uri, $first_uri_name, $is_last_link = false)
    {

        self::$parentCatArr = [];

        //prd($category->toArray());
        $breadcrumb = '';

        if (!empty($first_uri_name)) {
            $breadcrumb .= '<a href="' . url($first_uri) . '" >' . $first_uri_name . '</a>';
        }

        $hierarchy_arr = [];

        if (!empty($category) && count($category) > 0) {

            $category_id = (isset($category->pivot->id)) ? $category->pivot->id : 0;
            $p1_cat = (isset($category->pivot->p1_cat)) ? $category->pivot->p1_cat : 0;
            $p2_cat = (isset($category->pivot->p2_cat)) ? $category->pivot->p2_cat : 0;

            self::categoryParentForBreadcrumb($category);

            $hierarchy_arr = self::$parentCatArr;

            $hierarchy_arr_rev = array_reverse($hierarchy_arr);

            //prd($hierarchy_arr_rev);

            $pcat = '';

            if (!empty($hierarchy_arr_rev) && count($hierarchy_arr_rev) > 0) {

                foreach ($hierarchy_arr_rev as $cat) {

                    $cat = (object)$cat;

                    if (isset($cat->name)) {
                        if (!empty($first_uri_name)) {
                            $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
                        }

                        $pCatUrl = route('products.list', ['pcat' => $cat->slug]);

                        if ($cat->id == $p1_cat) {
                            $pcat = $cat->slug;
                            $pCatUrl = route('products.list', ['pcat' => $cat->slug]);
                        } elseif ($cat->id == $p2_cat) {
                            //$pCatUrl = 'javascript:void(0)';
                            $pCatUrl = route('products.list', ['pcat' => $pcat, 'p2cat' => $cat->slug]);
                        }

                        $breadcrumb .= '<a href="' . $pCatUrl . '" >' . $cat->name . '</a>';
                        $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
                    }
                }
                //$breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
            } elseif (!empty($first_uri_name)) {
                $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
            }

            if ($is_last_link) {

                $catUrl = route('products.list', ['pcat' => $pcat, 'cat[]' => $category->slug]);

                $breadcrumb .= '<a href="' . $catUrl . '">' . $category->name . '</a>';
            } else {
                //$breadcrumb .= '<a href="javascript:void(0)">'.$category->name.'</a>';
                $breadcrumb .= $category->name;
            }

        }

        return $breadcrumb;
    }


    public static function makeCategoryDropDown($category, $selected_value = '')
    {

        $selected = '';


        if (is_array($selected_value)) {
            if (in_array($category->id, $selected_value)) {
                $selected = 'selected';
            }

        } else {
            if ($category->id == $selected_value) {
                $selected = 'selected';
            }

        }


        $category_name = $category->name;

        if (isset($category->parent) && count($category->parent) > 0) {
            $mark = self::markCategoryParent($category);
            $category_name = $mark . $category_name;
        }

        $options = '<option value="' . $category->id . '" ' . $selected . ' >' . $category_name . '</option>';

        if (isset($category->children) && count($category->children) > 0) {

            foreach ($category->children as $child_cat) {
                $options .= self::makeCategoryDropDown($child_cat, $selected_value);
            }

        }
        return $options;
    }

    public static function markCategoryParent($category)
    {
        $mark = '';

        if (isset($category->parent) && count($category->parent) > 0) {
            $mark .= ' - ';
            $category_parent = $category->parent;
            $mark .= self::markCategoryParent($category_parent);
        }

        return $mark;
    }


    public static function getMenuItemsList($menuItems, $menu_id, $is_parent = true, $parent_class = '', $child_class = '')
    {

        $routeName = self::getAdminRouteName();

        $list = '';
        if ($is_parent) {
            $list .= '<ol class="' . $parent_class . '">';
        }

        if (!empty($menuItems) && count($menuItems) > 0) {

            foreach ($menuItems as $mi) {

                $list .= '<li class="' . $child_class . '" id="item_id_' . $mi->id . '">';

                $list .= $mi->title;

                $item_url = route($routeName . '.menus.items', $menu_id . '/' . $mi->id);

                $list .= '&nbsp;&nbsp;<a href="' . $item_url . '" title="Edit"><i class="fas fa-edit"></i></a>';
                $list .= '&nbsp;&nbsp;<a href="javascript:void(0)" data-id="' . $mi->id . '" class="delItem" title="Delete"><i class="fas fa-trash-alt"></i></a>';

                if (isset($mi->children) && count($mi->children) > 0) {
                    $list .= '<ol class="">';
                    $list .= self::getMenuItemsList($mi->children, $menu_id, false, $parent_class, $child_class);
                    $list .= '</ol>';
                }
                $list .= '</li>';
            }
        }

        if ($is_parent) {
            $list .= '</ol>';
        }


        return $list;
    }


    public static function getMenuForFront($menuItems, $is_parent = true, $parent_class = '', $child_class = '', $child_parent_class = '')
    {

        $routeName = self::getAdminRouteName();

        $list = '';
        if ($is_parent) {
            $list .= '<ul class="' . $parent_class . '">';
        }

        if (!empty($menuItems) && count($menuItems) > 0) {

            foreach ($menuItems as $mi) {

                $menuUrl = url($mi->url);

                $target = $mi->target;

                if ($mi->link_type == 'external' && !empty($mi->url)) {
                    $menuUrl = $mi->url;
                }

                $list .= '<li class="' . $child_class . '" id="item_id_' . $mi->id . '">';

                $list .= '<a href="' . $menuUrl . '" target="' . $target . '">';
                $list .= $mi->title;
                $list .= '</a>';

                if (isset($mi->children) && count($mi->children) > 0) {
                    $list .= '<ul class="' . $child_parent_class . '">';
                    $list .= self::getMenuForFront($mi->children, false, $parent_class, $child_class);
                    $list .= '</ul>';
                }

                $list .= '</li>';
            }
        }

        if ($is_parent) {
            $list .= '</ul>';
        }


        return $list;
    }


    public static function getNameFromNumber($num)
    {

        $index = 0;
        $index = abs($index * 1);
        $numeric = ($num - $index) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - $index) / 26);
        if ($num2 > 0) {
            return self::getNameFromNumber(
                    $num2 - 1 + $index
                ) . $letter;
        } else {
            return $letter;
        }
    }

    public static function BackUrl()
    {
        $uri = request()->path();
        if (count(request()->input()) > 0) {
            $request_input = request()->input();
            if (isset($request_input['back_url'])) {
                unset($request_input['back_url']);
            }
            $uri .= '?' . http_build_query($request_input, '', "&");
        }
        //rawurlencode(str)
        //return rawurlencode($uri);
        return $uri;
    }

    public static function sendEmail($viewPath, $viewData, $to, $from, $replyTo, $subject, $params = array())
    {

        try {

            Mail::send(
                $viewPath,
                $viewData,
                function ($message) use ($to, $from, $replyTo, $subject, $params) {
                    $attachment = (isset($params['attachment'])) ? $params['attachment'] : '';

                    if (!empty($replyTo)) {
                        $message->replyTo($replyTo);
                    }

                    if (!empty($from)) {
                        $message->from($from, "Reptile Finance");
                    }

                    if (!empty($attachment)) {
                        $message->attach($attachment);
                    }

                    $message->to($to);
                    $message->subject($subject);

                }
            );
        } catch (\Exception $e) {
            // Never reached
        }
        return true;
        if (count(Mail::failures()) > 0) {
            return false;
        } else {
            return true;
        }

    }

    public static function sendEmailRaw($html, $plainText, $to, $from, $replyTo, $subject, $params = array())
    {

        try {

            Mail::raw(
                [],
                function ($message) use ($html, $plainText, $to, $from, $replyTo, $subject, $params) {
                    $attachment = (isset($params['attachment'])) ? $params['attachment'] : '';

                    if (!empty($replyTo)) {
                        $message->replyTo($replyTo);
                    }

                    if (!empty($from)) {
                        $message->from($from);
                    }

                    if (!empty($attachment)) {
                        $message->attach($attachment);
                    }

                    $message->setBody($html, 'text/html');
                    $message->addPart($plainText, 'text/plain');

                    $message->to($to);
                    $message->subject($subject);

                }
            );
        } catch (\Exception $e) {
            // Never reached
        }

        if (count(Mail::failures()) > 0) {
            return false;
        } else {
            return true;
        }
    }


    public static function time_elapsed_string($datetime, $full = false)
    {
        date_default_timezone_set("Asia/Kolkata");

        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function getAccessToken()
    {
        $path = storage_path('app/public/configold.json');
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => '420109173074-mtb818f62aldi9b9snolls81dm5fcrcq.apps.googleusercontent.com',
            'clientSecret' => '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',
            'redirectUri' => ["https://reptilefinance.reptiledevclub.com/superadmin/googlecallback/"],
            'urlAuthorize' => 'https://accounts.google.com/o/oauth2/auth',
            'urlAccessToken' => 'https://oauth2.googleapis.com/token',
            'urlResourceOwnerDetails' => $path,
        ]);
        $google_data = DB::table('google_data')->where('id', 1)->first();
        $refreshToken = $google_data->refresh_token ?? '';
        try {
            $accessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $refreshToken
            ]);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to retrieve access token
            // Handle the exception
            //print_r($e);
        }

        // The new access token
        $newAccessToken = $accessToken->getToken();
        return $newAccessToken;
    }


    public static function saveLogs($type, $company_id = '', $shop_id = '')
    {
        date_default_timezone_set("Asia/Kolkata");
        $dbArray = [];
        $text = '';
        $dbArray['company_id'] = $company_id;
        $dbArray['type'] = $type;
        $dbArray['shop_id'] = $shop_id;
        $dbArray['created_at'] = date('Y-m-d H:i:s');
        if ($type == 'login') {
            $text = 'Admin Login Successfully';
        }
        if ($type == 'logout') {
            $text = 'Admin logout Successfully';
        }
        $dbArray['text'] = $text;

        DB::table('activity_logs')->insert($dbArray);
    }

    public static function updateData($tbl, $id_col, $id, $data)
    {

        $is_updated = 0;

        if (!empty($tbl) && !empty($id_col) && is_numeric($id) && $id > 0 && is_array($data) && count($data) > 0) {
            $is_updated = DB::table($tbl)->where($id_col, $id)->update($data);
        }

        return $is_updated;
    }


    public static function isSerialized($value, &$result = null)
    {

        if (empty($value)) {
            return false;
        }

        // Bit of a give away this one
        if (!is_string($value)) {
            return false;
        }
        // Serialized false, return true. unserialize() returns false on an
        // invalid string or it could return false if the string is serialized
        // false, eliminate that possibility.
        if ($value === 'b:0;') {
            $result = false;
            return true;
        }
        $length = strlen($value);
        $end = '';
        switch ($value[0]) {
            case 's':
                if ($value[$length - 2] !== '"') {
                    return false;
                }
            case 'b':
            case 'i':
            case 'd':
                // This looks odd but it is quicker than isset()ing
                $end .= ';';
            case 'a':
            case 'O':
                $end .= '}';
                if ($value[1] !== ':') {
                    return false;
                }
                switch ($value[2]) {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        break;
                    default:
                        return false;
                }
            case 'N':
                $end .= ';';
                if ($value[$length - 1] !== $end[0]) {
                    return false;
                }
                break;
            default:
                return false;
        }
        if (($result = @unserialize($value)) === false) {
            $result = null;
            return false;
        }
        return true;
    }


    public static function makeStarRatingArr($rating = 5)
    {

        $ratingArr = explode('.', $rating);

        $count = $ratingArr[0];

        $revCount = 5 - $count;

        $starColorArr = [];

        for ($i = 1; $i <= $count; $i++) {
            $starColorArr[] = '<span class="fa fa-star color"></span>';
        }

        $starArr = [];

        if ($revCount > 0) {
            for ($r = 1; $r <= $revCount; $r++) {
                $starArr[] = '<span class="fa fa-star"></span>';
            }
        }

        $starArray = array_merge($starColorArr, $starArr);

        return $starArray;

    }


    public static function wordsLimit($str, $limit = 150, $isStripTags = false, $allowTags = '')
    {
        $newStr = '';
        if (strlen($str) <= $limit) {
            $newStr = $str;
        } else {
            $newStr = substr($str, 0, $limit) . ' ...';
        }

        if ($isStripTags) {
            if (!empty($allowTags)) {
                $newStr = strip_tags($newStr, $allowTags);
            } else {
                $newStr = strip_tags($newStr);
            }
        }

        return $newStr;
    }


    private static $categoryAttributes = [];

    public static function getParentCategoryAttributes($category)
    {

        if (!empty($category) && count($category) > 0) {

            if (isset($category->parent) && count($category->parent) > 0) {
                self::getParentCategoryAttributes($category->parent);
            }

            $attributes = (isset($category->categoryAttributes)) ? $category->categoryAttributes : '';
            if (!empty($attributes) && count($attributes) > 0) {
                self::$categoryAttributes[] = $attributes;
            }
        }

        return self::$categoryAttributes;
    }


    public static function getData($tbl, $id = 0, $where = '', $selectArr = ['*'], $params = [])
    {

        $result = '';

        $orderByArr = (isset($params['orderBy'])) ? $params['orderBy'] : '';
        $featured = (isset($params['featured'])) ? $params['featured'] : '0';

        $query = DB::table($tbl);

        $query->select($selectArr);

        if (!empty($where) && count($where) > 0) {
            $query->where($where);
        }

        if (!empty($orderByArr) && count($orderByArr) > 0) {
            foreach ($orderByArr as $orderKey => $orderVal) {
                $query->orderBy($orderKey, $orderVal);
            }
        }

        if (isset($featured) && !empty($featured)) {
            $query->where('featured', $featured);
        }

        if (isset($params['limit']) && is_numeric($params['limit']) && $params['limit'] > 0) {
            $query->limit($params['limit']);
        }

        if (is_numeric($id) && $id > 0) {
            $query->where('id', $id);
            $result = $query->first();
        } else {
            $result = $query->get();
        }

        return $result;
    }

    public static function calculateProductDiscount($mainPrice, $salePrice)
    {

        $discount = 0;

        if (!empty($mainPrice) && !empty($salePrice)) {
            $discount = (($mainPrice - $salePrice) / $mainPrice) * 100;
        }

        return $discount;
    }

    public static function calculateProductShipping($weight, $qty)
    {

        $shippingCharge = 0;

        if (is_numeric($weight) && $weight > 0 && is_numeric($qty) && $qty > 0) {

        }

        return $shippingCharge;
    }


    // Common Function for GetEvents

    public static function convert_number_to_words($number)
    {

        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . self::convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }


    /* End Common Function */
    public static function getPaymentStatusStr($status)
    {

        if (is_numeric($status) && strlen($status) > 0) {
            if ($status == 1) {
                $status = 'Paid';
            } else if ($status == 2) {
                $status = 'Reject';
            } else {
                $status = 'Pending';
            }

        }
        return $status;
    }

    // For Menus

    public static function getImagePath($filePath)
    {
        $filePath = str_replace('\\', '/', $filePath);
        $basePath = dirname(__DIR__, 3) . '/images/';// Example: D:/xampp/htdocs/BuyBuyCart/images
        $basePath = str_replace('\\', '/', $basePath);
        return str_replace($basePath, '', $filePath);
    }

    function randomNumberOrder($qtd)
    {
        $Caracteres = '0123456789';
        $QuantidadeCaracteres = strlen($Caracteres);
        $QuantidadeCaracteres--;

        $ransom_num = NULL;
        for ($x = 1; $x <= $qtd; $x++) {
            $Posicao = rand(0, $QuantidadeCaracteres);
            $ransom_num .= substr($Caracteres, $Posicao, 1);
        }

        return $ransom_num;
    }


    public static function getNotifyData($type)
    {

        return DB::table('notification_table')->where('type', $type)->first();
    }

    public static function getVendorProductIds($vendor_id)
    {
        $vendor_product_ids = VendorProductPrice::where('vendor_id', $vendor_id)->where('is_delete', 0)->groupBy('product_id')->pluck('product_id')->toArray();
        return $vendor_product_ids;

    }

    public static function getProductImages($product_id)
    {
        $product_images = DB::table('product_images')->where('product_id', $product_id)->where('is_delete', 0)->get();
        if (!empty($product_images)) {
            foreach ($product_images as $img) {
                $img->image = self::getImageUrl('products', $img->image);
            }
        }

        return $product_images;

    }

    public static function getCategoryBrandImages($id, $type)
    {
        $product_images = DB::table('category_brand_images')->where('type_id', $id)->where('type', $type)->where('is_delete', 0)->get();
        if (!empty($product_images)) {
            foreach ($product_images as $img) {
                $img->image = self::getImageUrl('banners', $img->image);
            }
        }

        return $product_images;

    }


    public static function getProductDeatils($product_id)
    {
        $product = Products::find($product_id);
        return $product;

    }

    public static function getDaysLeft($start_date, $end_date)
    {
        $daysleft = '';
        if (!empty($start_date) && !empty($end_date)) {
            $year = date('Y', strtotime($end_date));
            $month = date('m', strtotime($end_date));
            $date = date('d', strtotime($end_date));
            $startDate = Carbon::parse(date('Y-m-d')); // Example: December 31, 2024

            // Get the current date
            $endDate = Carbon::parse(date('Y-m-d', strtotime($end_date)));


            // Calculate the difference in days
            $daysleft = $startDate->diffInDays($endDate);
            if ($daysleft >= 0) {
                $daysleft = abs($daysleft);
            } else {
                $daysleft = 0;
            }

        }

        return $daysleft;

    }

    public static function createAccessToken()
    {
        $path = storage_path('app/public') . '/config.json';
        $provider = new GenericProvider([
            'clientId' => '985890722792-03rcmtvga7l07k67dqbbpe6lt7bphoe5.apps.googleusercontent.com',
            'clientSecret' => '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',
            'redirectUri' => ["https://adminbuycart.reptileantitheft.com/googlecallback", "https://localhost/BuyBuyCart/buy_buy_cart_admin/googlecallback"],
            'urlAuthorize' => 'https://accounts.google.com/o/oauth2/auth',
            'urlAccessToken' => 'https://oauth2.googleapis.com/token',
            'urlResourceOwnerDetails' => $path,
        ]);

        $google_data = DB::table('google_data')->where('id', 1)->first();
        // Assuming you have a refresh token
        $refreshToken = $google_data->refresh_token ?? '';
        try {
            $accessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $refreshToken
            ]);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to retrieve access token
            // Handle the exception
        }

        // The new access token
        $newAccessToken = $accessToken->getToken();
        if (!empty($newAccessToken)) {
            DB::table('google_data')->where('id', 1)->update(['access_token' => $newAccessToken]);
        }
        return $newAccessToken;
    }


    public static function getAccessTokenNew()
    {
        $serviceAccountFile = storage_path('app/buybuycart.json');
//        $scopes = ['https://www.googleapis.com/auth/firebaseiid'];
//        $credentials = new ServiceAccountCredentials($scopes,$serviceAccountFile);
//        try {
//            $accessToken = $credentials->fetchAuthToken();
//        } catch (\Exception $e) {
//            return null; // Or handle the error as needed
//        }
//        print_r($accessToken);
//        die;
//        if (isset($accessToken['access_token'])) {
//            $token = $accessToken['access_token'];
//            return $token; // Return the token; don't echo it here
//        } else {
//            return null; // Or handle the error as needed
//        }
//
//
//
//
        $serviceAccountFile = storage_path('app/buybuycart.json'); // Correct path
        $scopes = ['https://www.googleapis.com/auth/firebaseiid'];

        $jsonKey = json_decode(file_get_contents($serviceAccountFile), true);
        $privateKey = str_replace('\\n', "\n", $jsonKey['private_key']); // Correct newline handling

        $data = [
            'client_id' => $jsonKey['client_id'],
            'client_secret' => $privateKey,
            'scope' => 'https://www.googleapis.com/auth/firebaseiid',
            'grant_type' => 'client_credentials',
        ];

        $ch = curl_init('https://oauth2.googleapis.com/token');  // URL only!
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded', // Absolutely essential!
            ],
            CURLOPT_POSTFIELDS => http_build_query($data), // Correct URL encoding
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            $result = json_decode($response, true);
            print_r($result); // Crucial: Print the full response!
        }

        curl_close($ch);


        print_r($response);
        die;

        return $response['access_token'] ?? null;
    }


    public static function subscribeToTopic($tokens, $topic, $accessToken)
    {
        $data = [
            'to' => '/topics/' . $topic,
            'registration_tokens' => $tokens
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://iid.googleapis.com/iid/v1:batchAdd',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function sendNotificationToTopic($topic, $title, $body, $accessToken)
    {
        $postFields = [
            'message' => [
                'topic' => $topic,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'android' => [
                    'priority' => 'high'
                ],
                'data' => [
                    'title' => $title,
                    'body' => $body
                ]
            ]
        ];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/v1/projects/buybuycart-317d4/messages:send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postFields),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    public static function fcmNotification($token, $data)
    {
        $accessToken = self::createAccessToken();

        $title = $data['title'] ?? '';
        $body = $data['body'] ?? '';
        $image = $data['image'] ?? '';
        $latitude = $data['latitude'] ?? '';
        $longitude = $data['longitude'] ?? '';
        $address = $data['address'] ?? '';
        $file_type = $data['file_type'] ?? '';
        $type = $data['type'] ?? '';
        $total_item = $data['total_item'] ?? '0';
        $total_amount = $data['total_amount'] ?? '';
        $order_status = $data['order_status'] ?? '';
        $priority = $data['priority'] ?? 'high';

        $postFields = [
            'message' => [
                'token' => $token,
                'data' => [
                    'body' => $body,
                    'title' => $title,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'address' => $address,
                    'file_type' => $file_type,
                    'type' => $type,
                    'total_item' => (string)$total_item,
                    'address' => $address,
                    'priority' => $priority,
                    'order_status' => $order_status,
                    'total_amount' => $total_amount
                ],
                'notification' => [
                    'body' => $body,
                    'title' => $title,
                    'image' => $image,
                ],
                'android' => [
                    'priority' => $priority,
                ],
            ],
        ];


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/v1/projects/buybuycart-317d4/messages:send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postFields),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function saveTransaction($data): void
    {

        $dbArray = [];
        $dbArray['userID'] = $data['userID'] ?? '';
        $dbArray['txn_no'] = $data['txn_no'] ?? '';
        $dbArray['amount'] = $data['amount'] ?? '';
        $dbArray['type'] = $data['type'] ?? '';
        $dbArray['note'] = $data['note'] ?? '';
        $dbArray['wallet_type'] = $data['wallet_type'] ?? '';
        $dbArray['against_for'] = $data['against_for'] ?? '';
        $dbArray['paid_by'] = $data['paid_by'] ?? '';
        $dbArray['orderID'] = $data['orderID'] ?? '';
        $dbArray['expired_at'] = $data['expired_at'] ?? '';
        Transaction::insert($dbArray);

    }

    public static function send_notification($title, $body, $deviceToken, $image)
    {
        $sendData = array(
            'body' => !empty($body) ? $body : '',
            'title' => !empty($title) ? $title : '',
            'image' => !empty($image) ? $image : '',
            'sound' => 'Default'
        );

        return self::fcmNotification($deviceToken, $sendData);
    }

    public static function fcmNotificationold($device_id, $sendData)
    {
        #API access key from Google API's Console
        if (!defined('API_ACCESS_KEY')) {
            define('API_ACCESS_KEY', 'AAAAvWysmTI:APA91bErvKHn3cCH4vmzzdyG_GNPui2T9ub5rIyn0QcPTQwvOZoMIyVQkPael9Ep9SN1dwBwgpOblq6U0ad5dpp-4ADqPOkDuiWhxZ9TxVLIlISmc0xRwM9d3hllK9Qp4C7QyGf2AYh7');
        }


        $fields = array
        (
            'to' => $device_id,
            'data' => $sendData,
            'notification' => $sendData,
            // "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === false) {
            die('Curl failed ' . curl_error($ch));
        }
        // prd($result);
        curl_close($ch);
        return true;
    }


    public static function sendBirthDayEmail()
    {
        $users = User::select('id', 'name', 'email', 'phone', 'dob')->where('dob', date('Y-m-d'))->where('is_delete', 0)->get();
        if (!empty($users)) {
            foreach ($users as $user) {
                $to_email = $user->email;
                $subject = 'BirthDay Wish - ' . env('APP_NAME');
                $ADMIN_EMAIL = config('custom.admin_email');
                $from_email = $ADMIN_EMAIL;
                $email_data = [];
                $email_data['name'] = $user->name;
                $email_data['phone'] = $user->phone;
                $email_data['dob'] = $user->dob;
                $is_send = self::sendEmail('emails.birthday_email', $email_data, $to = $to_email, $from_email, $replyTo = $from_email, $subject);

            }
        }

    }


    /* End of helper class */
}
