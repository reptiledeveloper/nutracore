<?php

namespace App\Http\Controllers\V1;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Mail;
use App\Models\Order;

class EmailController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function out_for_delivery($order_id)
    {
        $order = Order::find($order_id);
        $user = User::find($order->userID);
        $to_email = $user->email ?? '';
        if (!empty($to_email)) {
            $subject = 'Your BuyBuyCart Order is on the Way! 🚚✨';
            $from_email = env('MAIL_FROM_ADDRESS');
            $email_data = [];
            $email_data['order'] = $order;
            $email_data['user'] = $user;
            return $this->sendEmail('emails.out_for_delivery', $email_data, $to = $to_email, $from_email, $replyTo = $from_email, $subject);
        } else {
            return;
        }
    }

    public function delivered($order_id)
    {
        $order = Order::find($order_id);
        $user = User::find($order->userID);
        $to_email = $user->email ?? '';
        if (!empty($to_email)) {
            $subject = 'Your BuyBuyCart Order Has Been Delivered! 🎉';
            $from_email = env('MAIL_FROM_ADDRESS');
            $email_data = [];
            $email_data['order'] = $order;
            $email_data['user'] = $user;
            return $this->sendEmail('emails.delivered', $email_data, $to = $to_email, $from_email, $replyTo = $from_email, $subject);
        } else {
            return;
        }
    }
    public function send_otp($user, $otp)
    {
        $to_email = $user->email ?? '';
        if (!empty($to_email)) {
            $subject = 'Your BuyBuyCart Login OTP';
            $from_email = env('MAIL_FROM_ADDRESS');
            $email_data = [];
            $email_data['otp'] = $otp;
            $email_data['user'] = $user;
            return $this->sendEmail('emails.send_otp', $email_data, $to_email, $from_email, $from_email, $subject);
        } else {
            return;
        }
    }

    public function order_cancelled_by_admin($order_id)
    {
        $order = Order::find($order_id);
        $user = User::find($order->userID);
        $to_email = $user->email ?? '';
        if (!empty($to_email)) {
            $subject = 'Your BuyBuyCart Order Has Been Cancelled';
            $from_email = env('MAIL_FROM_ADDRESS');
            $email_data = [];
            $email_data['user'] = $user;
            $email_data['order'] = $order;
            return $this->sendEmail('emails.order_cancelled_by_admin', $email_data, $to_email, $from_email, $from_email, $subject);
        } else {
            return;
        }
    }
    public function order_cancelled_by_user($order_id)
    {
        $order = Order::find($order_id);
        $user = User::find($order->userID);
        $to_email = $user->email ?? '';
        if (!empty($to_email)) {
            $subject = 'Order Cancellation Successfully Processed – Thank You!';
            $from_email = env('MAIL_FROM_ADDRESS');
            $email_data = [];
            $email_data['user'] = $user;
            $email_data['order'] = $order;
            return $this->sendEmail('emails.order_cancelled_by_user', $email_data, $to_email, $from_email, $from_email, $subject);
        } else {
            return;
        }
    }





    public static function sendEmail($viewPath, $viewData, $to, $from, $replyTo, $subject, $params = array())
    {

        try {

            Mail::send(
                $viewPath,
                $viewData,
                function ($message) use ($to, $from, $replyTo, $subject, $params) {
                    $attachment = (isset($params['attachment'])) ? $params['attachment'] : '';

                    if (!empty($replyTo)) {
                        $message->replyTo($replyTo);
                    }

                    if (!empty($from)) {
                        $message->from($from, "BuyBuyCart");
                    }

                    if (!empty($attachment)) {
                        $message->attach($attachment);
                    }

                    $message->to($to);
                    $message->subject($subject);

                }
            );
        } catch (\Exception $e) {
            // Never reached
            print_r($e);
            die;
        }
    }
}
