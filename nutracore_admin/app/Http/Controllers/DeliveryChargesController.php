<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;

use App\Models\FAQ;
use App\Models\DeliveryCharges;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;


class DeliveryChargesController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $delivery_charges = DeliveryCharges::where('is_delete', 0)->latest()->paginate(10);
        $data['delivery_charges'] = $delivery_charges;
        return view('delivery_charges.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;

        $delivery_charges = '';
        if (is_numeric($id) && $id > 0) {
            $delivery_charges = DeliveryCharges::find($id);
            if (empty($delivery_charges)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/delivery_charges');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/delivery_charges';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Delivery Charges has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Delivery Charges has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Delivery Charges';

        if (!empty($delivery_charges)) {
            $page_heading = 'Update Delivery Charges';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['delivery_charges'] = $delivery_charges;

        return view('delivery_charges.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';

        $faqs = new DeliveryCharges();

        if (is_numeric($id) && $id > 0) {
            $exist = DeliveryCharges::find($id);
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
            $is_delete = DeliveryCharges::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'DeliveryCharges has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
