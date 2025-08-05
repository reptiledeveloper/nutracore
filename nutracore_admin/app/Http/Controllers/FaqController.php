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
use App\Models\FAQ;
use App\Models\Admin;
use App\Models\Blocks;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class FaqController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $faqs = FAQ::where('is_delete', 0)->latest()->paginate(10);
        $data['faqs'] = $faqs;
        return view('faqs.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;

        $faqs = '';
        if (is_numeric($id) && $id > 0) {
            $faqs = FAQ::find($id);
            if (empty($faqs)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/faqs');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/faqs';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'FAQ has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'FAQ has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add FAQ';

        if (!empty($faqs)) {
            $page_heading = 'Update FAQ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['faqs'] = $faqs;

        return view('faqs.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';

        $faqs = new FAQ();

        if (is_numeric($id) && $id > 0) {
            $exist = FAQ::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $faqs = $exist;
                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $faqs->$key = $val;
        }

        $isSaved = $faqs->save();

        if ($isSaved) {

        }

        return $isSaved;
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = FAQ::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'FAQ has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
