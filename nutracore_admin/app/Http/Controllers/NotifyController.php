<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Illuminate\Support\Facades\App;
use Validator;
use App\Models\User;
use App\Models\Banner;
use App\Models\Admin;
use App\Models\Blocks;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class NotifyController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public static function send_admin_subscription($user_id, $data)
    {
        $success = [];
        $user = User::find($user_id);
        if (!empty($user)) {
            $token = $user->device_token ?? '';
            if (!empty($token)) {
                $success = CustomHelper::fcmNotification($token, $data);
            }
        }
        return $success;
    }

}
