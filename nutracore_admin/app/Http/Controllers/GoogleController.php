<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Http;
use Google_Client;
use League\OAuth2\Client\Provider\GenericProvider;
use DB;

class GoogleController extends Controller
{

    public function __construct()
    {

    }

    public function sendFCM()
    {
        $token = 'cT7NsJdy6Hs3Qm1pnDKyHQ:APA91bEdfQ8auhVYscFrYgeopekn2wgSTwofrKmwE4lf7tOHVEC4he4OylvFK82OFa9V4V_wohOSHWv4Rf3qketRkU6eHOzvoR31IwIgZDHyQXzXdNDcwcw';
        $accessToken = self::createAccessToken();
        $image = 'https://localhost/ReptileBahikhataLite/admin/public/assets/images/logo.svg';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/v1/projects/buybuycart-317d4/messages:send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "message": {
                "token": "' . $token . '",
                "data": {
                    "body": "Test",
                    "title": "Buy Buy Cart Live",
                    },
                "notification": {
                    "body": "Test",
                    "title": "Buy Buy Cart Live",
                    "image": "' . $image . '"
                }
              }
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function googlecallback(Request $request)
    {
        $authCode = $request->code ?? '';
        if (!empty($authCode)) {
            $path = storage_path('app/public') . '/nutracore.json';
            $scope = ['https://www.googleapis.com/auth/firebase.messaging', 'https://www.googleapis.com/auth/cloud-platform'];
            $applicationName = 'buybuycart-441506';
            $client = new \Google_Client();
            $client->setApplicationName($applicationName);
            $client->setAuthConfig($path);
            $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging', 'https://www.googleapis.com/auth/cloud-platform']);
            $client->setAccessType('online');
            $client->setApprovalPrompt('force');
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $dbArray = [];
            $dbArray['code'] = $authCode;
            if (!empty($accessToken['refresh_token'])) {
                $dbArray['refresh_token'] = $accessToken['refresh_token'] ?? '';
            }
            if (!empty($accessToken['access_token'])) {
                $dbArray['access_token'] = $accessToken['access_token'] ?? '';
            }
            $exist = DB::table('google_data')->where('id', 1)->first();
            if (!empty($exist)) {
                DB::table('google_data')->where('id', 1)->update($dbArray);
            } else {
                DB::table('google_data')->insert($dbArray);
            }
        }
        return redirect(route('home'));
        return back();

    }


    public function createAccessToken()
    {
        $path = storage_path('app/public') . '/nutracore.json';
        $provider = new GenericProvider([
            'clientId' => '154429853000-cv6i13uuujc2hr8cs9lgc2ojc72ckffd.apps.googleusercontent.com',
            'clientSecret' => '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',
            'redirectUri' => ["https://admin.nutracore.in/googlecallback",
                "http://localhost/Nutracore/nutracore_admin/googlecallback"],
            'urlAuthorize' => 'https://accounts.google.com/o/oauth2/auth',
            'urlAccessToken' => 'https://oauth2.googleapis.com/token',
            'urlResourceOwnerDetails' => $path,
        ]);

        $google_data = DB::table('google_data')->where('id', 1)->first();
        // Assuming you have a refresh token
        $refreshToken = $google_data->refresh_token ?? '';
        $accessToken = '';
        try {
            $accessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $refreshToken
            ]);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to retrieve access token
            // Handle the exception

        }
        // The new access token
        $newAccessToken = $accessToken->getToken();
        if (!empty($newAccessToken)) {
            DB::table('google_data')->where('id', 1)->update(['access_token' => $newAccessToken]);
        }
        return $newAccessToken;
    }

    public function google_auth()
    {
        $path = storage_path('app/public') . '/nutracore.json';
        $scope = ['https://www.googleapis.com/auth/firebase.messaging', 'https://www.googleapis.com/auth/cloud-platform'];
        $applicationName = 'nutracore';
        $client = new \Google_Client();
        $client->setApplicationName($applicationName);
        $client->setAuthConfig($path);
        $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging', 'https://www.googleapis.com/auth/cloud-platform']);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }


}
