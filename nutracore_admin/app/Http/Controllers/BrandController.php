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

use App\Models\Brand;
use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class BrandController extends Controller
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
        $brands = Brand::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            $brands->where('brand_name', 'like', '%' . $search . '%');
        }
        $brands = $brands->paginate(10);
        $data['brands'] = $brands;
        return view('brands.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $brands = '';
        if (is_numeric($id) && $id > 0) {

            $brands = Brand::find($id);
            if (empty($brands)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/brands');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/brands';
            }

            $name = (isset($request->name)) ? $request->name : '';


            $rules = [];
            if (is_numeric($id) && $id > 0) {
                $rules['brand_name'] = 'required';

            } else {
                $rules['brand_name'] = 'required';
            }
            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Brand has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Brand has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Brand';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Brand ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['brands'] = $brands;

        return view('brands.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image','certificate', 'password', 'holiday_date', 'holiday_title']);


        $oldImg = '';

        $brands = new Brand;

        if (is_numeric($id) && $id > 0) {
            $exist = Brand::find($id);

            if (isset($exist->id) && $exist->id == $id) {
                $brands = $exist;

                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $brands->$key = $val;
        }

        $isSaved = $brands->save();

        if ($isSaved) {
            $this->saveImage($request, $brands, $oldImg);
        }

        return $isSaved;
    }

    private function saveImage($request, $brands, $oldImg = '')
    {
        $file = $request->file('image');
        if ($file) {
            $path = 'brands';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $brands->brand_img = $uploaded_data;
            $brands->save();
        }
        $file = $request->file('certificate');
        if ($file) {
            $path = 'brands';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $brands->certificate = $uploaded_data;
            $brands->save();
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Brand::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Brand has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
