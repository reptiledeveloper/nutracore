<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Validator;
use App\Models\User;
use App\Models\Offers;
use Storage;
use DB;
use Hash;


class OfferController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $offers = Offers::where('is_delete', 0)->latest();

        $offers = $offers->paginate(10);
        $data['offers'] = $offers;
        return view('offers.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;

        $offers = '';
        if (is_numeric($id) && $id > 0) {
            $offers = Offers::find($id);
            if (empty($offers)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/offers');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/offers';
            }
            $rules = [];
            $rules['offer_code'] = 'required';
            $rules['start_date'] = 'required';
            $rules['end_date'] = 'required';
            $rules['offer_type'] = 'required';
            $rules['offer_value'] = 'required';

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Offers has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Offers has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Offer / PromoCode';

        if (!empty($offers)) {
            $page_heading = 'Update Offer / PromoCode';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['offers'] = $offers;

        return view('offers.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';
        if (!empty($request->category_ids)) {
            $data['category_ids'] = implode(",", $request->category_ids);
        }
        if (!empty($request->product_ids)) {
            $data['product_ids'] = implode(",", $request->product_ids);
        }
        if (!empty($request->brand_ids)) {
            $data['brand_ids'] = implode(",", $request->brand_ids);
        }

        if(!empty($request->user_id)){
            $phone = $request->user_id??'';
            $user = User::where('phone',$phone)->first();
            $data['user_id'] = $user->id??'';
        }

        $offers = new Offers();
        if (is_numeric($id) && $id > 0) {
            $exist = Offers::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $offers = $exist;
                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $offers->$key = $val;
        }

        $isSaved = $offers->save();

        if ($isSaved) {
            $this->saveImage($request, $offers, $oldImg);
        }

        return $isSaved;
    }


    private function saveImage($request, $offers, $oldImg = '')
    {

        $file = $request->file('image');
        if ($file) {
            $path = 'offers';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            if ($uploaded_data) {
                $offers->image = $uploaded_data;
                $offers->save();
            }
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Offers::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Offers has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


    public function fetch_user(Request $request)
    {
        $phone = $request->phone??'';
        $user = User::where('phone',$phone)->first();
        echo $user->name??'';
    }
}
