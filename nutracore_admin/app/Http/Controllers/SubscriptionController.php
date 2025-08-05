<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlans;
use App\Models\Subscriptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Validator;
use App\Models\Category;

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class SubscriptionController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }


    public function index(Request $request)
    {
        $data = [];
        $search = $request->search ?? '';


        $subscriptions = Subscriptions::select('users.name', 'subscriptions.*', 'users.email', 'users.phone', 'users.image')
            ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
//            ->where('subscriptions.is_delete', 0)->where('subscriptions.paid_status', 1)->whereDate('subscriptions.end_date', '>=', date('Y-m-d'));
            ->where('subscriptions.is_delete', 0)->where('subscriptions.paid_status', 1);


        $subscriptions = $subscriptions->paginate(50);

        $data['subscriptions'] = $subscriptions;
        return view('subscriptions.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $categories = '';
        if (is_numeric($id) && $id > 0) {

            $categories = Category::find($id);
            if (empty($categories)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/categories');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/categories';
            }

            $name = (isset($request->name)) ? $request->name : '';


            $rules = [];
            if (is_numeric($id) && $id > 0) {
                $rules['name'] = 'required';

            } else {
                $rules['name'] = 'required';
            }
            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Category has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Category has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Category';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Category ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['categories'] = $categories;

        return view('categories.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title']);


        $oldImg = '';

        $categories = new Category;
        $data['parent_id'] = 0;
        if (is_numeric($id) && $id > 0) {
            $exist = Category::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $categories = $exist;
                $oldImg = $exist->image;
                if (empty($exist->slug)) {
                    $data['slug'] = CustomHelper::GetSlug('categories', 'id', $id, $request->name);
                }
            }
        } else {
            $data['slug'] = CustomHelper::GetSlug('categories', 'id', $id, $request->name);
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $categories->$key = $val;
        }

        $isSaved = $categories->save();

        if ($isSaved) {
            $this->saveImage($request, $categories, $oldImg);
        }

        return $isSaved;
    }

    private function saveImage($request, $categories, $oldImg = '')
    {

        // $file = $request->file('logo');
        // if ($file) {
        //     $fileName = "logo" . time() . $file->getClientOriginalName();
        //     $filePath = 'company/' . $fileName;
        //     $path = Storage::disk('s3')->put($filePath, file_get_contents($file));
        //     $users->logo = $fileName;
        //     $users->save();
        // }

        $file = $request->file('image');
        if ($file) {
            $path = 'categories';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $categories->image = $uploaded_data;
            $categories->save();
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Category::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Category has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }

    public function add_subscription(Request $request)
    {
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $rules = [];
            $rules['subscription_id'] = "required";
            $rules['user_id'] = "required";
            $request->validate($rules);
            $subscription = SubscriptionPlans::find($request->subscription_id);
            $start_date = date('Y-m-d');
            $exist = Subscriptions::where('user_id', $request->user_id)->latest()->first();
            $dbArray = [];
            if (!empty($exist)) {
                if (strtotime($exist->end_date) >= strtotime($start_date)) {

                } else {

                }
            }
            $dbArray['start_date'] = $start_date;
            $dbArray['end_date'] = date('Y-m-d', strtotime("+" . $subscription->duration . " months", strtotime($start_date)));
            $dbArray['user_id'] = $request->user_id ?? '';
            $dbArray['subscription_id'] = $request->subscription_id ?? '';
            $dbArray['txn_id'] = "NC" . rand(000000, 9999999);
            $dbArray['paid_status'] = 1;
            $dbArray['taken_by'] = 'Admin';
            Subscriptions::insert($dbArray);
            $data = [];
            $data['title'] = "You Got A Subscription From BuyBuy Cart";
            $data['description'] = "You Got A Subscription From BuyBuy Cart";
            $notify = NotifyController::send_admin_subscription($request->user_id, $data);
            // return back('alert-success', 'Subscription Added Successfully');
             return back();
        }
        return back();
    }


}
