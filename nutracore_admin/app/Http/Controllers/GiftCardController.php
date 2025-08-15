<?php

namespace App\Http\Controllers;

use Attribute;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Validator;

use App\Models\Brand;
use App\Models\Attributes;
use App\Models\GiftCard;

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class GiftCardController extends Controller
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
        $giftcards = GiftCard::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            $giftcards->where('name', 'like', '%' . $search . '%');
        }
        $giftcards = $giftcards->paginate(10);
        $data['giftcards'] = $giftcards;
        return view('gift_cards.index', $data);
    }

    public function add(Request $request)
    {
        $id = $request->id ?? '';
        if (empty($id)) {
            $no_of_giftcard = $request->no_of_giftcard ?? '';
            $amount = $request->amount ?? '';
            $duration = $request->duration ?? '';
            for ($i = 0; $i < $no_of_giftcard; $i++) {
                $db = new GiftCard;
                $db->amount = $amount;
                $db->duration = $duration;
                $db->code = CustomHelper::generateGiftCardCode();
                $db->save();
            }
        } else {
            $status = $request->status ?? '';
            GiftCard::where('id', $id)->update(['status' => $status]);
        }

        return back();

    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = GiftCard::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'GiftCard has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
