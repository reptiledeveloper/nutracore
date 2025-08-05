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

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;



class AttributesController extends Controller
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
        $attributes = Attributes::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            $attributes->where('name', 'like', '%' . $search . '%');
        }
        $attributes = $attributes->paginate(10);
        $data['attributes'] = $attributes;
        return view('attributes.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $attributes = '';
        if (is_numeric($id) && $id > 0) {

            $attributes = Attributes::find($id);
            if (empty($attributes)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/attributes');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/attributes';
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
                $alert_msg = 'Attributes has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Attributes has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Attributes';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Attributes ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['attributes'] = $attributes;

        return view('attributes.form', $data);

    }






    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title']);


        $oldImg = '';

        $attributes = new Attributes;

        if (is_numeric($id) && $id > 0) {
            $exist = Attributes::find($id);

            if (isset($exist->id) && $exist->id == $id) {
                $attributes = $exist;

                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $attributes->$key = $val;
        }

        $isSaved = $attributes->save();

        if ($isSaved) {
            $this->saveImage($request, $attributes, $oldImg);
        }

        return $isSaved;
    }
    private function saveImage($request, $attributes, $oldImg = '')
    {
        $file = $request->file('image');
        if ($file) {
            $path = 'attributes';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $attributes->brand_img = $uploaded_data;
            $attributes->save();
        }
    }



    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Attributes::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Attributes has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
