<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Admin;
use App\Models\Category;
use App\Models\CategoryWiseCommission;
use App\Models\Order;
use App\Models\Products;
use App\Models\SellerPermission;
use App\Models\SellerRoles;
use App\Models\VendorProductPrice;
use App\Models\Vendors;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;

class SellerController extends Controller
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
        $sellers = Vendors::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            $sellers->where('name', 'like', '%' . $search . '%');
            $sellers->orWhere('user_name', 'like', '%' . $search . '%');
            $sellers->orWhere('user_phone', 'like', '%' . $search . '%');
            $sellers->orWhere('user_email', 'like', '%' . $search . '%');
        }
        $sellers = $sellers->paginate(10);
        $data['sellers'] = $sellers;
        return view('sellers.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $sellers = '';
        if (is_numeric($id) && $id > 0) {

            $sellers = Vendors::find($id);
            if (empty($sellers)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/sellers');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/sellers';
            }

            $name = (isset($request->name)) ? $request->name : '';


            $rules = [];
            if (is_numeric($id) && $id > 0) {

            } else {
                // $rules['pan_no'] = 'required | regex:/[A-Z]{5}[0-9]{4}[A-Z]{1}/';
                // $rules['bank_code'] = 'required | regex:/^[A-Z]{4}0[A-Z0-9]{6}$/';
                $rules['user_phone'] = 'required | regex:/^[6-9][0-9]{9}$/';

            }
            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Stores has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Stores has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Stores';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Stores ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['sellers'] = $sellers;

        return view('sellers.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title']);


        $oldImg = '';

        $sellers = new Vendors();

        if (is_numeric($id) && $id > 0) {
            $exist = Vendors::find($id);

            if (isset($exist->id) && $exist->id == $id) {
                $sellers = $exist;

                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $sellers->$key = $val;
        }

        $isSaved = $sellers->save();

        if ($isSaved) {
            $this->saveImage($request, $sellers, $oldImg);
        }

        return $isSaved;
    }


    private function saveImage($request, $sellers, $oldImg = '')
    {
        $file = $request->file('image');
        if ($file) {
            $path = 'sellers';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $sellers->image = $uploaded_data;
            $sellers->save();
        }
        $file = $request->file('gst_certificate');
        if ($file) {
            $path = 'sellers';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $sellers->gst_certificate = $uploaded_data;
            $sellers->save();
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Vendors::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Sellers has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }

    public function view(Request $request)
    {
        $data = [];
        $id = $request->id;
        $seller = Vendors::find($id);
        if (empty($seller)) {
            return back()->with('alert-danger', 'Seller Not Exist');
        }
        $data['seller'] = $seller;
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $createdCat = $this->save($request, $id);
            if ($createdCat) {
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Seller has been updated successfully.';
                }
                return back()->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }
        return view('sellers.view', $data);
    }

    public function saveCommission($request, $id)
    {
        $commisssion_type = $request->commisssion_type ?? '';
        $commisssion = $request->commisssion ?? '';
        $category_id = $request->category_id ?? '';
        if (!empty($commisssion_type)) {
            foreach ($commisssion_type as $key => $value) {
                $dbArray = [];
                $dbArray['category_id'] = $category_id[$key] ?? '';
                $dbArray['vendor_id'] = $id;
                $dbArray['commisssion'] = $commisssion[$key] ?? '';
                $dbArray['commisssion_type'] = $value ?? '';
                $exist = CustomHelper::getCategoryCommission($id, $category_id[$key]);
                if (!empty($exist)) {
                    CategoryWiseCommission::where('id', $exist->id)->update($dbArray);
                } else {
                    CategoryWiseCommission::insert($dbArray);
                }
            }
        }
        return true;
    }

    public function commission(Request $request)
    {
        $data = [];
        $id = $request->id;
        $seller = Vendors::find($id);
        if (empty($seller)) {
            return back()->with('alert-danger', 'Seller Not Exist');
        }
        $data['seller'] = $seller;
        $categories = Category::where('parent_id', 0)->where('is_delete', 0)->where('status',1)->get();
        $data['categories'] = $categories;
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $createdCat = $this->saveCommission($request, $id);
            if ($createdCat) {
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Commission has been updated successfully.';
                }
                return back()->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }
        return view('sellers.commission', $data);

    }

    public function updateVarient($request, $id)
    {

        $varient_id = $request->varient_id ?? '';
        $dbArray = [];
        $dbArray['unit'] = $request->unit ?? '';
        $dbArray['unit_value'] = $request->unit_value ?? '';
        $dbArray['mrp'] = $request->mrp ?? '';
        $dbArray['selling_price'] = $request->selling_price ?? '';
        $dbArray['subscription_price'] = $request->subscription_price ?? '';
        $dbArray['status'] = $request->status ?? 0;
        $products_id = $request->product_id ?? '';
        $exist = CustomHelper::checkVendorUpdatedPrice($id, $products_id, $varient_id);
        if (!empty($exist)) {
            VendorProductPrice::where('id', $exist->id)->update($dbArray);
        }
        return true;
    }


    public function products(Request $request)
    {

        $data = [];
        $id = $request->id;
        $search = $request->search ?? '';
        $category_id = $request->category_id ?? '';
        $subcategory_id = $request->subcategory_id ?? '';
        $seller = Vendors::find($id);
        if (empty($seller)) {
            return back()->with('alert-danger', 'Seller Not Exist');
        }
        $data['seller'] = $seller;
        $productIds = CustomHelper::getProductIds($id);
        $products = Products::where('is_delete', 0);
        if (!empty($category_id)) {
            $products->where('category_id', $category_id);
        }
        if (!empty($subcategory_id)) {
            $products->where('subcategory_id', $subcategory_id);
        }
        if (!empty($search)) {
            $products->where('name', 'like', '%' . $search . '%');
        }
        $products = $products->whereIn('id', $productIds)->paginate(20);
        $data['products'] = $products;
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $createdCat = $this->updateVarient($request, $id);
            if ($createdCat) {
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Varient has been updated successfully.';
                }
                return back()->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }
        return view('sellers.products', $data);

    }

    public function admins(Request $request)
    {

        $data = [];
        $id = $request->id;
        $seller = Vendors::find($id);
        if (empty($seller)) {
            return back()->with('alert-danger', 'Seller Not Exist');
        }
        $data['seller'] = $seller;
        $admins = Admin::where('vendor_id', $id)->where('is_delete', 0)->paginate(20);
        $data['admins'] = $admins;
        return view('sellers.admins', $data);

    }

    public function orders(Request $request)
    {

        $data = [];
        $id = $request->id;
        $seller = Vendors::find($id);
        if (empty($seller)) {
            return back()->with('alert-danger', 'Seller Not Exist');
        }
        $data['seller'] = $seller;
        $orders = Order::where('vendor_id', $id)->where('is_delete', 0)->paginate(20);
        $data['orders'] = $orders;
        return view('sellers.orders', $data);

    }



    public function roles(Request $request)
    {

        $data = [];
        $id = $request->id;
        $seller = Vendors::find($id);
        if (empty($seller)) {
            return back()->with('alert-danger', 'Seller Not Exist');
        }
        $data['seller'] = $seller;
        $roles = SellerRoles::where('vendor_id', $id)->where('is_delete', 0)->paginate(20);
        $data['roles'] = $roles;
        $method = $request->method();
//        if ($method == 'post' || $method == 'POST') {
//            $createdCat = $this->updateVarient($request, $id);
//            if ($createdCat) {
//                if (is_numeric($id) && $id > 0) {
//                    $alert_msg = 'Varient has been updated successfully.';
//                }
//                return back()->with('alert-success', $alert_msg);
//            } else {
//                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
//            }
//        }
        return view('sellers.roles', $data);

    }

    public function permission(Request $request)
    {

        $data = [];
        $id = $request->id;
        $seller = Vendors::find($id);
        if (empty($seller)) {
            return back()->with('alert-danger', 'Seller Not Exist');
        }
        $role_id = $request->role_id ?? '';
        $data['seller'] = $seller;
        return view('sellers.permission', $data);

    }


    public function update_permission(Request $request): void
    {
        $key = isset($request->key) ? $request->key : '';
        $section = isset($request->section) ? $request->section : '';
        $permission = isset($request->permission) ? $request->permission : '';
        $role_id = isset($request->role_id) ? $request->role_id : '';
        $seller_id = isset($request->seller_id) ? $request->seller_id : '';
        $dbArray = [];
        $exist = SellerPermission::where(['vendor_id' => $seller_id, 'role_id' => $role_id, 'section' => $key])->first();
        if (!empty($exist)) {
            $dbArray[$section] = $permission;
            SellerPermission::where('id', $exist->id)->update($dbArray);
        } else {
            $dbArray['role_id'] = $role_id;
            $dbArray['vendor_id'] = $seller_id;
            $dbArray['section'] = $key;
            $dbArray[$section] = $permission;
            SellerPermission::insert($dbArray);
        }
    }


    public function add_role(Request $request)
    {
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $rules = [];
            $rules['name'] = 'required';
            $rules['vendor_id'] = 'required';
            $request->validate($rules);

            $dbArray = [];
            if (!empty($request->name)) {
                $dbArray['name'] = $request->name;
            }
            if (isset($request->status)) {
                $dbArray['status'] = $request->status;
            }
            if (!empty($request->vendor_id)) {
                $dbArray['vendor_id'] = $request->vendor_id;
            }
            if (empty($request->id)) {
                SellerRoles::insert($dbArray);
            } else {
                SellerRoles::where('id', $request->id)->update($dbArray);
            }
            return back();
        }
        return back();
    }


}
