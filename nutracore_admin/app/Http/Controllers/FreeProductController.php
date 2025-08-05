<?php

namespace App\Http\Controllers;

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
use App\Models\FreeProduct;
use App\Models\LoyalitySystem;

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class FreeProductController extends Controller
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
        $freebeesproduct = FreeProduct::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
           //$freebeesproduct->where('name', 'like', '%' . $search . '%');
        }
        $freebeesproduct = $freebeesproduct->paginate(10);
        $data['freebeesproducts'] = $freebeesproduct;
        return view('freebeesproduct.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $freebeesproduct = '';
        if (is_numeric($id) && $id > 0) {

            $freebeesproduct = FreeProduct::find($id);
            if (empty($freebeesproduct)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/free_product');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/free_product';
            }

            $name = (isset($request->name)) ? $request->name : '';


            $rules = [];
            if (is_numeric($id) && $id > 0) {
                $rules['product_name'] = 'required';

            } else {
                $rules['product_name'] = 'required';
            }
            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'FreeProduct has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'FreeProduct has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add FreeProduct';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update FreeProduct ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['freebeesproduct'] = $freebeesproduct;

        return view('freebeesproduct.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title']);


        $oldImg = '';
      

        $categories = new FreeProduct;
     
        if (is_numeric($id) && $id > 0) {
            $exist = FreeProduct::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $categories = $exist;
                $oldImg = $exist->image;
               
            }
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
        $file = $request->file('image');
        if ($file) {
            $path = 'freebeesproduct';
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
            $is_delete = FreeProduct::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'FreeProduct has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
