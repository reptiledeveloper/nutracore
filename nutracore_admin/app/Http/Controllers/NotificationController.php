<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Admin;
use App\Models\Blog;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\QRCodes;
use App\Models\User;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;


class NotificationController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }


    public function index(Request $request)
    {
        $data = [];
        $campaigns = Campaign::where('is_delete',0)->orderBy('id', 'desc');

        $campaigns = $campaigns->paginate(10);
        $data['campaigns'] = $campaigns;
        return view('notifications.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $campaigns = '';
        if (is_numeric($id) && $id > 0) {
            $campaigns = Campaign::find($id);
            if (empty($campaigns)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/notifications');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/notifications';
            }

            $name = (isset($request->name)) ? $request->name : '';


            $rules = [];
            if (is_numeric($id) && $id > 0) {
                $rules['title'] = 'required';
            } else {
                $rules['title'] = 'required';
            }
            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Campaign has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Campaign has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Campaign';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Campaign ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['campaign'] = $campaigns;

        return view('notifications.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';
        $categories = new Campaign();
        if (is_numeric($id) && $id > 0) {
            $exist = Campaign::find($id);
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
            $this->saveImage($request, $categories, $oldImg);
        }

        return $isSaved;
    }

    private function saveImage($request, $categories, $oldImg = '')
    {
        $file = $request->file('image');
        if ($file) {
            $path = 'notification';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            if ($uploaded_data) {
                $categories->image = $uploaded_data;
                $categories->save();
            }
        }
    }


    public function delete(Request $request)
    {
        $id = (isset($request->id)) ? $request->id : 0;
        $is_delete = '';
        if (is_numeric($id) && $id > 0) {
            $is_delete = Campaign::where('id', $id)->update(['is_delete' => 1]);
        }
        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Blogs has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


    public function send(Request $request)
    {
        $id = $request->id;
        $campaign = Campaign::find($id);
        $image = '';
        $userIds = [];
        $tokens = [];
        if($campaign->user_type == 'user'){
            $user_data = User::where('status', 1)->where('is_delete', 0)->where('device_token', '!=', null);
            $tokens = $user_data->pluck('device_token')->toArray();
            $userIds = $user_data->pluck('id')->toArray();

        }else{
            $user_data = Admin::where('status', 1)->where('is_delete', 0)->where('device_token', '!=', null)->where('vendor_id', '!=', null);
            $tokens = $user_data->pluck('device_token')->toArray();
            $userIds = $user_data->pluck('id')->toArray();
        }

        if (!empty($campaign->image)) {
            $image = CustomHelper::getImageUrl('notification', $campaign->image);
        }
        $data = [
            'title' => $campaign->title ?? '',
            'description' => $campaign->description ?? '',
            'type' => $campaign->type ?? '',
            'image' => $image,
        ];

        $tokens = array_filter($tokens); // Removes empty values
        $tokens = array_values($tokens);
        if(!empty($tokens)){
            $topic = 'news';
            $accessToken = CustomHelper::createAccessToken();
                $title =  $campaign->title ?? '';
                $body =  $campaign->description ?? '';
                CustomHelper::sendNotificationToTopic($topic, $title, $body,$accessToken);
        }
//        die;
//        if (!empty($tokens)) {
//            foreach ($tokens as $key => $token) {
//                if(!empty($token)){
//                    $dbArray = [];
//                    $dbArray['user_id'] = $userIds[$key] ?? '';
//                    $dbArray['title'] = $campaign->title ?? '';
//                    $dbArray['image'] = $campaign->image ?? '';
//                    $dbArray['description'] = $campaign->description ?? '';
//                    $dbArray['is_sent'] = 1;
//                    Notification::insert($dbArray);
//                    $success = CustomHelper::fcmNotification($token, $data);
//                }
//            }
//        }

        return back()->with('alert-success', 'Notification  has been Sent successfully.');
    }


}
