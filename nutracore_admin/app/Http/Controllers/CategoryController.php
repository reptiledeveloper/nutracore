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

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class CategoryController extends Controller
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
        $categories = Category::where('parent_id', 0)->where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            $categories->where('name', 'like', '%' . $search . '%');
        }
        $categories = $categories->paginate(50);
        $data['categories'] = $categories;
        return view('categories.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $categories = '';
        if (is_numeric($id) && $id > 0) {

            $categories = Category::find($id);
            if (empty($categories)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/categories');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/categories';
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
                $alert_msg = 'Category has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Category has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Category';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Category ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['categories'] = $categories;

        return view('categories.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title','banners']);


        $oldImg = '';
        if(!empty($request->product_ids)){
            $data['product_ids'] = implode(",",$request->product_ids);
        }

        $categories = new Category;
        $data['parent_id'] = 0;
        if(!empty($request->tags)){
            $data['tags'] = implode(",",$request->tags);
        }
        if (is_numeric($id) && $id > 0) {
            $exist = Category::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $categories = $exist;
                $oldImg = $exist->image;
                if (empty($exist->slug)) {
                    $data['slug'] = CustomHelper::GetSlug('categories', 'id', $id, $request->name);
                }
            }
        } else {
            $data['slug'] = CustomHelper::GetSlug('categories', 'id', $id, $request->name);
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
            $path = 'categories';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $categories->image = $uploaded_data;
            $categories->save();
        }

        $files = $request->file('banners');
        if (!empty($files)) {
            foreach ($files as $file) {
                $path = 'banners';
                $uploaded_data = CustomHelper::UploadImage($file, $path);
                $dbArray = [];
                $dbArray['type'] = 'category';
                $dbArray['type_id'] = $categories->id??'';
                $dbArray['image'] = $uploaded_data;
                DB::table('category_brand_images')->insert($dbArray);
            }
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Category::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Category has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
