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


class LoyalitySystemController extends Controller
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
        $loyality_system = LoyalitySystem::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            //$loyality_system->where('name', 'like', '%' . $search . '%');
        }
        $loyality_system = $loyality_system->paginate(10);
        $data['loyality_system'] = $loyality_system;
        return view('loyality_system.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $loyality_system = '';
        if (is_numeric($id) && $id > 0) {

            $loyality_system = LoyalitySystem::find($id);
            if (empty($loyality_system)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/loyality_system');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/loyality_system';
            }

            $name = (isset($request->name)) ? $request->name : '';


            $rules = [];
            if (is_numeric($id) && $id > 0) {
               

            } else {
               
            }
            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'LoyalitySystem has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'LoyalitySystem has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add LoyalitySystem';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update LoyalitySystem ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['loyality_system'] = $loyality_system;

        return view('loyality_system.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title']);


        $oldImg = '';
        
        $categories = new LoyalitySystem;

        if (is_numeric($id) && $id > 0) {
            $exist = LoyalitySystem::find($id);
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
           // $this->saveImage($request, $categories, $oldImg);
        }

        return $isSaved;
    }

    private function saveImage($request, $categories, $oldImg = '')
    {

        // $file = $request->file('logo');
        // if ($file) {
        //     $fileName = "logo" . time() . $file->getClientOriginalName();
        //     $filePath = 'company/' . $fileName;
        //     $path = Storage::disk('s3')->put($filePath, file_get_contents($file));
        //     $users->logo = $fileName;
        //     $users->save();
        // }

        $file = $request->file('image');
        if ($file) {
            $path = 'categories';
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
            $is_delete = LoyalitySystem::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'LoyalitySystem has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
