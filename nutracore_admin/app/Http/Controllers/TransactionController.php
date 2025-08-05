<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Banner;
use App\Models\Blocks;
use App\Models\Company;
use App\Models\Transaction;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;
use Yajra\DataTables\DataTables;


class TransactionController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $transaction = Transaction::where('is_delete', 0)->latest();

        $transaction = $transaction->paginate(50);
        $data['transactions'] = $transaction;
        return view('transaction.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;

        $banner = '';
        if (is_numeric($id) && $id > 0) {
            $banner = Banner::find($id);
            if (empty($banner)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/banners');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/banners';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Banner has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Banner has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Banner';

        if (!empty($banner)) {
            $page_heading = 'Update Banner';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['banners'] = $banner;

        return view('banners.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';

        $admin = new Banner();

        if (is_numeric($id) && $id > 0) {
            $exist = Banner::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $admin = $exist;
                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $admin->$key = $val;
        }

        $isSaved = $admin->save();

        if ($isSaved) {
            $this->saveImage($request, $admin, $oldImg);
        }

        return $isSaved;
    }


    private function saveImage($request, $banner, $oldImg = '')
    {

        $file = $request->file('image');

        if ($file) {
            $path = 'banners';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            if ($uploaded_data) {
                $banner->banner_img = $uploaded_data;
                $banner->save();
            }
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Banner::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Banner has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
