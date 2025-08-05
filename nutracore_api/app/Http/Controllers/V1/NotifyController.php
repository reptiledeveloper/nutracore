<?php

namespace App\Http\Controllers\V1;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\Notification;


class NotifyController extends Controller
{

    public function __construct()
    {

    }

    public function sendFCM(){
        $token = 'fjiL9eteSu6POqPzlBE2XM:APA91bGMiMBIT6T6p1sWv1d5tgTrZbkyhejwK3ZDckEMKU6TM4cypQKxYMQJ1FuLenga5uGFu3DjycQP45YAn7H7WjiQUo7xq0KrhKu9faX5glRDYTuB5zidcI09qUc2kK6HhHn0OALv';
        $path = storage_path('app/public/reptile-bahikhata-55a981a4f40d.json');

        // $client = new \Google\Client();
        // $client->setAuthConfig($path);
        $firebase = (new Factory)
        ->withServiceAccount($path)
        ->createMessaging();
        // $notification = Notification::create()->setTitle('Notification Title')->setBody('Notification Body');
        
        $notification = [
            'title' => 'Notification Title',
            'body' => 'Notification Body',
        ];
        $customData = [
             'titlee' => 'Notification Title',
            'bodye' => 'Notification Body',
        ];
        $message = CloudMessage::new()
        ->withNotification($notification)
        // ->withData($customData)
        ->withTarget('token', $token); 

        
        $response = $firebase->send($message);
        echo "<pre>";
        print_r($message);

    }

}