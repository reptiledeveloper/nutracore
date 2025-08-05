<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\State;
use App\Models\Company;
use App\Models\DeliveryAgents;
use App\Models\DeliveryAgentTransaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class DeliveryAgentsController extends Controller
{


    private string $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }


    public function index(Request $request)
    {
        $data = [];
        $agents = DeliveryAgents::where('is_delete', 0)->orderBy('id', 'desc');

        $agents = $agents->paginate(10);
        $data['agents'] = $agents;
        return view('agents.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;
        $agents = '';
        if (is_numeric($id) && $id > 0) {

            $agents = DeliveryAgents::find($id);
            if (empty($agents)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/delivery_agents');
            }
        }
        if ($request->method() == 'POST' || $request->method() == 'post') {
            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/delivery_agents';
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
                $alert_msg = 'Delivery Agents has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Delivery Agents has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Delivery Agents';
        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Delivery Agents ';
        }
        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['agents'] = $agents;
        return view('agents.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'adhar_card', 'vehicle_document', 'driving_licence']);
        $oldImg = '';
        if (!empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        }
        $agents = new DeliveryAgents();
        if (is_numeric($id) && $id > 0) {
            $exist = DeliveryAgents::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $agents = $exist;
                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $agents->$key = $val;
        }

        $isSaved = $agents->save();

        if ($isSaved) {
            $this->saveImage($request, $agents, $oldImg);
        }

        return $isSaved;
    }

    private function saveImage($request, $agents, $oldImg = '')
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
            $path = 'agents';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $agents->image = $uploaded_data;
            $agents->save();
        }
        $adhar_card = $request->file('adhar_card');
        if ($adhar_card) {
            $path = 'agents';
            $uploaded_data = CustomHelper::UploadImage($adhar_card, $path);
            $agents->adhar_card = $uploaded_data;
            $agents->save();
        }
        $vehicle_document = $request->file('vehicle_document');
        if ($vehicle_document) {
            $path = 'agents';
            $uploaded_data = CustomHelper::UploadImage($vehicle_document, $path);
            $agents->vehicle_document = $uploaded_data;
            $agents->save();
        }
        $driving_licence = $request->file('driving_licence');
        if ($driving_licence) {
            $path = 'agents';
            $uploaded_data = CustomHelper::UploadImage($driving_licence, $path);
            $agents->driving_licence = $uploaded_data;
            $agents->save();
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = DeliveryAgents::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'DeliveryAgents has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }

    public function view(Request $request)
    {
        $id = $request->id ?? '';
        $agents = DeliveryAgents::find($id);
        $data = [];
        $data['agents'] = $agents;
        $orders = Order::where('agent_id', $id)->where('is_delete', 0)->latest();

        $orders = $orders->paginate(10);
        $data['orders'] = $orders;
        return view('agents.view', $data);
    }

    public function transactions(Request $request)
    {
        $id = $request->id ?? '';
        $agents = DeliveryAgents::find($id);
        $data = [];
        $data['agents'] = $agents;

        $transactions = DeliveryAgentTransaction::where('agent_id',$id)->paginate(20);
        $data['transactions'] = $transactions;
        return view('agents.transactions', $data);
    }


}
