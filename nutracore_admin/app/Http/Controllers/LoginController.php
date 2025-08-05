<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;


class LoginController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $method = $request->method();
        if ($method == 'POST' || $method == 'post') {
            $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
            $otp = $request->otp ?? '';
            $credentials = $request->only('email', 'password');
            $users = Admin::where('email', $request->email)->first();
            if (!empty($users)) {
                if ($users->status == 0) {
                    return back()->with('alert-danger', 'Your Account id Inactive, contact the administrator.');
                }
                if ($users->status == 1) {
                    if ($users->is_approve == 0) {
                        return back()->with('alert-danger', 'Your Account is Not Approved');
                    } else {
                        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
                            $users->device_token = $request->token ?? '';
                            $users->login_time = date('Y-m-d h:i:s');
                            $users->is_logout = 0;
                            $users->save();
                            $request->session()->regenerate();
                            return redirect('admin');
                        } else {
                            return back()->with('alert-danger', 'Invalid Username and Password');
                        }

                    }
                }
            }
        }
        return view('login.index');
    }

    public function logout(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
//        CustomHelper::saveLogs('logout');

        Auth::logout();

        $request->session()->invalidate();

        return redirect('/');
    }
}
