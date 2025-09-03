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
use App\Models\Banner;
use App\Models\Admin;
use App\Models\Suppliments;
use App\Models\Blocks;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class SupplimentsController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $suppliments = Suppliments::where('is_delete', 0)->latest()->paginate(10);
        $data['suppliments'] = $suppliments;
        return view('suppliments.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;
        $suppliments = '';
        if (is_numeric($id) && $id > 0) {
            $suppliments = Suppliments::find($id);
            if (empty($suppliments)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/suppliments');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/suppliments';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Suppliments has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Suppliments has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Supplements';

        if (!empty($suppliments)) {
            $page_heading = 'Update Supplements';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['suppliments'] = $suppliments;

        return view('suppliments.form', $data);

    }


    public function save(Request $request, $id = 0)
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'activity' => 'required|string|max:255',
            'status' => 'required|boolean',
        ];

        for ($i = 1; $i <= 5; $i++) {
            $rules["supliment_$i"] = 'nullable|exists:categories,id';
            $rules["supliment_{$i}_products"] = 'nullable|array';
            $rules["supliment_{$i}_products.*"] = 'exists:products,id';
        }

        $validated = $request->validate($rules);

        $data = $request->except(['_token', 'back_url', 'image', 'image_text']);

        // 🔥 Convert product arrays to comma-separated strings
        for ($i = 1; $i <= 5; $i++) {
            $field = "supliment_{$i}_products";
            if (!empty($request->$field)) {
                $data[$field] = implode(',', $request->$field);
            } else {
                $data[$field] = null;
            }
        }

        // 🔥 Combine all product IDs into `product_id` column
        $allProducts = [];
        for ($i = 1; $i <= 5; $i++) {
            $field = "supliment_{$i}_products";
            if (!empty($request->$field)) {
                $allProducts = array_merge($allProducts, explode(',', $data[$field]));
            }
        }
        $data['product_id'] = !empty($allProducts) ? implode(',', $allProducts) : null;

        // 🔥 Save or Update
        $suppliment = Suppliments::find($id) ?? new Suppliments;
        foreach ($data as $key => $val) {
            $suppliment->$key = $val;
        }
        $suppliment->save();

        return redirect()->route('suppliments.index')
            ->with('success', $id ? 'Updated successfully' : 'Created successfully');
    }





    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Suppliments::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Suppliments has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
