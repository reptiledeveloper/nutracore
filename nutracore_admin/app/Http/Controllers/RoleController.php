<?php

namespace App\Http\Controllers;

use Couchbase\Role;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Models\Roles;

class RoleController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $roles = Roles::where('is_delete', 0)->latest()->paginate(10);
        $data['roles'] = $roles;
        return view('roles.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;
        $roles = '';
        if (is_numeric($id) && $id > 0) {
            $roles = Roles::find($id);
            if (empty($roles)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/roles');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/roles';
            }
            $request->validate([
                'name' => 'required',
                'status' => 'required',
            ]);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Role has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Role has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }
        $page_heading = 'Add Role';
        if (!empty($roles)) {
            $page_heading = 'Update Role';
        }
        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['roles'] = $roles;

        return view('roles.form', $data);

    }


    public function save(Request $request, $id = 0)
    {
        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';
        $roles = new Roles();
        if (is_numeric($id) && $id > 0) {
            $exist = Roles::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $roles = $exist;
                $oldImg = $exist->image;
            }
        }
        foreach ($data as $key => $val) {
            $roles->$key = $val;
        }
        $isSaved = $roles->save();

        if ($isSaved) {

        }

        return $isSaved;
    }


    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $id = (isset($request->id)) ? $request->id : 0;
        $is_delete = '';
        if (is_numeric($id) && $id > 0) {
            $is_delete = Roles::where('id', $id)->update(['is_delete' => 1]);
        }
        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Roles has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
