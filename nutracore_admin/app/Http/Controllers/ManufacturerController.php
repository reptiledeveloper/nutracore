<?php

namespace App\Http\Controllers;

use Attribute;
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
use App\Models\Attributes;
use App\Models\Manufacturer;

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class ManufacturerController extends Controller
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
        $manufacturer = Manufacturer::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            $manufacturer->where('name', 'like', '%' . $search . '%');
        }
        $manufacturer = $manufacturer->paginate(10);
        $data['manufacturer'] = $manufacturer;
        return view('manufacturer.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $manufacturer = '';
        if (is_numeric($id) && $id > 0) {

            $manufacturer = Manufacturer::find($id);
            if (empty($manufacturer)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/manufacturer');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/manufacturer';
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
                $alert_msg = 'Manufacturer has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Manufacturer has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Manufacturer';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Manufacturer ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['manufacturer'] = $manufacturer;

        return view('manufacturer.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title']);


        $oldImg = '';

        $manufacturer = new Manufacturer;

        if (is_numeric($id) && $id > 0) {
            $exist = Manufacturer::find($id);

            if (isset($exist->id) && $exist->id == $id) {
                $manufacturer = $exist;

                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $manufacturer->$key = $val;
        }

        $isSaved = $manufacturer->save();

        if ($isSaved) {
            $this->saveImage($request, $manufacturer, $oldImg);
        }

        return $isSaved;
    }

    private function saveImage($request, $manufacturer, $oldImg = '')
    {
        $file = $request->file('image');
        if ($file) {
            $path = 'manufacturer';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $manufacturer->image = $uploaded_data;
            $manufacturer->save();
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Manufacturer::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Manufacturer has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
