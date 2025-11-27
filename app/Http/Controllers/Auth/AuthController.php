<?php

namespace App\Http\Controllers\Auth;


use Str;
use url;
use File;
use Hash;
use Mail;
use Cookie;
use Session;
use Socialite;
use App\Models\User;
use App\Models\Seller;
use Redirect,Response;
use App\Models\General;
use App\Models\ActivityLog;
use App\Mail\VerifyCodeMail;
use Illuminate\Http\Request;
use App\Mail\RegistrationMail;
use App\Models\SocialIdentity;
use App\Mail\passwordResetVerify;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct()
    {
    	$this->middleware('authCheck');
    }

    public function login(Request $r){
        //session(['url.intended' => url()->current()]);

        // if(route('login')!=url()->previous()){
        //     session(['url.intended' => url()->previous()]);
        // }

       // return Session::get('url.intended');

        //return $redirect;

        if ($r->isMethod('post'))
        {

            //Login Post Action

            $check = $r->validate([
                'user'    => 'required|max:100',
                'password' => 'required|max:50'
            ]);

            if(!$check){
                Session::flash('error','Need To validation');
                return back();
            }

            $remember_me  = ( !empty( $r->remember ) )? TRUE : FALSE;

            $user =User::where('mobile',$r->user)->orWhere('email',$r->user)->first();

            if($user){
                if(Hash::check($r->password, $user->password)){
                    Auth::login($user, $remember_me);

                    ActivityLog::create([
                        'uuid' => Str::uuid()->toString(),
                        'event' => 'login',
                        'title' => "User Logged In: {$user->name}",
                        'user_type' => get_class($user),
                        'user_id' => $user->id,
                        'loggable_type' => get_class($user),
                        'loggable_id' => $user->id,
                        'data' => json_encode([
                            'ip' => $r->ip(),
                            'user_agent' => $r->userAgent(),
                            'url' => $r->fullUrl(),
                            'last_active_at' => now()->toDateTimeString(),
                        ]),
                    ]);
                    ActivityLog::create([
                        'uuid' => Str::uuid()->toString(),
                        'event' => 'user_active',
                        'title' => "User Active: {$user->name}",
                        'user_type' => get_class($user),
                        'user_id' => $user->id,
                        'loggable_type' => get_class($user),
                        'loggable_id' => $user->id,
                        'data' => json_encode([
                            'last_active_at' => now()->toDateTimeString(),
                            'ip' => $r->ip(),
                            'user_agent' => $r->userAgent(),
                            'url' => $r->fullUrl(),
                        ]),
                    ]);

                    $redirect =Session::get('url.intended');
                    //Session::forget('url.intended');
                    if($redirect){
                        return Redirect::to($redirect);
                    }

                    if ($user->admin == 1) {
                        return redirect()->route('admin.dashboard');
                    } elseif ($user->staff == 1) {
                        return redirect()->route('staff.dashboard');
                    }

                }else{
                    Session::flash('error','Your account password is incorrect');
                    return back();
                }
            }else{
                Session::flash('error','Your do not have any accounts with us.');
                return back();
            }

            //Login Post Action End

        }
        // if(auth::check()){
        //     Redirect()->route('admin.dashboard');
        // }
        return view('Auth.adminLogin');

    }

    public function logout(Request $request)
    {

         $user = Auth::user();

        if ($user) {
            ActivityLog::create([
                'uuid' => Str::uuid()->toString(),
                'event' => 'logout',
                'title' => "User Logged Out: {$user->name}",
                'user_type' => get_class($user),
                'user_id' => $user->id,
                'loggable_type' => get_class($user),
                'loggable_id' => $user->id,
                'data' => json_encode([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'logged_out_at' => now()->toDateTimeString(),
                ]),
            ]);
        }

        // Laravel logout
        Auth::logout();

        // Clear all session data
        session()->flush();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect to login page with message
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }


}
