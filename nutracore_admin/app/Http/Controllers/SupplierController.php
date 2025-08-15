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
use App\Models\Supplier;

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;



class SupplierController extends Controller
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
        $suppliers = Supplier::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            $suppliers->where('name', 'like', '%' . $search . '%');
        }
        $suppliers = $suppliers->paginate(10);
        $data['suppliers'] = $suppliers;
        return view('suppliers.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $suppliers = '';
        if (is_numeric($id) && $id > 0) {

            $suppliers = Supplier::find($id);
            if (empty($suppliers)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/suppliers');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/suppliers';
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
                $alert_msg = 'suppliers has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'suppliers has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Suppliers';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Suppliers ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['suppliers'] = $suppliers;

        return view('suppliers.form', $data);

    }






    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title']);
        $oldImg = '';
        $suppliers = new Supplier();
        if (is_numeric($id) && $id > 0) {
            $exist = Supplier::find($id);

            if (isset($exist->id) && $exist->id == $id) {
                $suppliers = $exist;

                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $suppliers->$key = $val;
        }

        $isSaved = $suppliers->save();

        if ($isSaved) {
            //$this->saveImage($request, $suppliers, $oldImg);
        }

        return $isSaved;
    }



    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Supplier::where('id', $id)->update(['is_delete' => 1]);
        }
        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Supplier has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
