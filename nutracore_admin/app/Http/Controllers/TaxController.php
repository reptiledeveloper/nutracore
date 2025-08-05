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
use App\Models\Tax;

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;



class TaxController extends Controller
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
        $tax = Tax::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            $tax->where('title', 'like', '%' . $search . '%');
        }
        $tax = $tax->paginate(10);
        $data['tax'] = $tax;
        return view('tax.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $tax = '';
        if (is_numeric($id) && $id > 0) {

            $tax = Tax::find($id);
            if (empty($tax)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/tax');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/tax';
            }

            $name = (isset($request->name)) ? $request->name : '';


            $rules = [];
            if (is_numeric($id) && $id > 0) {

            } else {

            }
            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'TAX has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'TAX has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add TAX';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update TAX ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['tax'] = $tax;

        return view('tax.form', $data);

    }






    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title']);


        $oldImg = '';

        $tax = new Tax();

        if (is_numeric($id) && $id > 0) {
            $exist = Tax::find($id);

            if (isset($exist->id) && $exist->id == $id) {
                $tax = $exist;

                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $tax->$key = $val;
        }

        $isSaved = $tax->save();

        if ($isSaved) {
            //$this->saveImage($request, $tax, $oldImg);
        }

        return $isSaved;
    }
    private function saveImage($request, $tax, $oldImg = '')
    {
        $file = $request->file('image');
        if ($file) {
            $path = 'attributes';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $tax->brand_img = $uploaded_data;
            $tax->save();
        }
    }



    public function delete(Request $request)
    {

        $id = (isset($request->id)) ? $request->id : 0;
        $is_delete = '';
        if (is_numeric($id) && $id > 0) {
            $is_delete = Tax::where('id', $id)->update(['is_delete' => 1]);
        }
        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Tax has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
