<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CountDownTimer;
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
use App\Models\Blocks;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class CountDownTimerController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $id = $request->id;
            // Validation
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'start_time' => 'required',
                'end_time' => 'required',
                'product_ids' => 'required|array',
                'product_ids.*' => 'exists:products,id',
                'status' => 'required|in:0,1'
            ]);

            // If ID exists → Update, else → Create new
            $timer = CountdownTimer::find(1);

            // Save core data
            $timer->title = $request->title;
            $timer->description = $request->description;
            $timer->start_date = $request->start_date;
            $timer->end_date = $request->end_date;
            $timer->product_ids = implode(',',$request->product_ids);
            $timer->start_time = $request->start_time;
            $timer->end_time = $request->end_time;
            $timer->status = $request->status;
            $timer->save();
            return redirect()->back()->with('success', 'Countdown Timer saved successfully!');


        }
        $countdown_timer = CountDownTimer::where('id', 1)->first();
        $data['countdown_timer'] = $countdown_timer;
        return view('countdown_timer.index', $data);
    }


    private function saveImage($request, $banner, $oldImg = '')
    {

        $image_text = $request->image_text ?? '';
        if (!empty($image_text)) {
            $image_val = $image_text[0] ?? "";
            if (!empty($image_val)) {
                $banner->banner_img = $image_val;
                $banner->save();
            }
        }
        $file = $request->file('image');
        if ($file) {
            $path = 'banners';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            if ($uploaded_data) {
                $banner->banner_img = $uploaded_data;
                $banner->save();
            }
        }
    }


}
