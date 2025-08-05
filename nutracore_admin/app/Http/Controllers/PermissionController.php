<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Models\Permission;

class PermissionController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data = [];
        $role_id = $request->role_id??'';
        $data['role_id'] = $role_id;
        return view('permission.index', $data);
    }


    public function update_permission(Request $request): void
    {
        $key = isset($request->key) ? $request->key :'';
        $section = isset($request->section) ? $request->section :'';
        $permission = isset($request->permission) ? $request->permission :'';
        $role_id = isset($request->role_id) ? $request->role_id :'';
        $dbArray = [];
        $exist = Permission::where(['role_id'=>$role_id,'section'=>$key])->first();
        if(!empty($exist)){
            $dbArray[$section] = $permission;
            Permission::where('id',$exist->id)->update($dbArray);
        }else{
            $dbArray['role_id'] = $role_id;
            $dbArray['section'] = $key;
            $dbArray[$section] = $permission;
            Permission::insert($dbArray);
        }
    }

}
