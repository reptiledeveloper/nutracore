<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Chats;
use App\Models\SupportTicket;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;


class SupportTicketController extends Controller
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
        $tickets = SupportTicket::where('is_delete', 0)->orderBy('id', 'desc');

        $tickets = $tickets->paginate(10);
        $data['tickets'] = $tickets;
        return view('tickets.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = $request->id ?? '';
        $tickets = SupportTicket::where('id', $id)->first();
        $data['tickets'] = $tickets;
        return view('tickets.chat', $data);

    }

    public function get_chats(Request $request)
    {
        $ticket_id = $request->ticket_id ?? '';
        $chats = Chats::where('ticket_id', $ticket_id)->get();
        $html = view('tickets.messages', ['chats' => $chats]);
        echo $html;

    }

    public function submit_chat(Request $request)
    {
        $user_id = Auth::guard('admin')->user()->id ?? '';
        $ticket = SupportTicket::where('id', $request->ticket_id)->first();
        $dbArray = [];
        $dbArray['ticket_id'] = $request->ticket_id ?? '';
        $dbArray['sender_id'] = $user_id;
        $dbArray['sender_type'] = 'admin';
        $dbArray['reciever_id'] = $ticket->user_id ?? '';
        $dbArray['reciever_type'] = "user";
        $dbArray['message'] = $request->message ?? '';
        Chats::insert($dbArray);
        echo 1;

    }

    public function update_status(Request $request)
    {
        SupportTicket::where('id', $request->ticket_id)->update(['status'=>$request->status]);
        echo 1;
    }

    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = SupportTicket::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'SupportTicket has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }



}
