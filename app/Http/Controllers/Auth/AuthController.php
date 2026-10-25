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
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    // Wrong passwords for one account from one IP before that account is locked on that IP.
    private const MAX_ATTEMPTS = 3;
    private const LOCK_SECONDS = 15 * 60;

    // Wrong logins from one IP across any accounts before the whole IP is blocked. Kept well
    // above MAX_ATTEMPTS: every PC in the factory usually shares one public IP.
    private const IP_MAX_ATTEMPTS = 20;
    private const IP_BLOCK_SECONDS = 60 * 60;

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

        $lockMessage = $this->loginLockMessage($r);

        if ($r->isMethod('post'))
        {

            //Login Post Action

            if ($lockMessage) {
                Session::flash('error', $lockMessage);
                return back()->withInput($r->only('user'));
            }

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
                    if((int) $user->status !== 1){
                        Session::flash('error','Your account has been deactivated. Please contact the administrator.');
                        return back();
                    }

                    RateLimiter::clear($this->accountThrottleKey($r));
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
                            'public_ip' => clientPublicIp($r),
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
                            'public_ip' => clientPublicIp($r),
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

                    Auth::logout();
                    Session::flash('error', 'Your account has no panel access. Please contact the administrator.');
                    return back()->withInput($r->only('user'));

                }else{
                    event(new Failed(Auth::getDefaultDriver(), $user, ['user' => $r->user, 'password' => $r->password]));
                    return $this->failedLogin($r, 'Your account password is incorrect.');
                }
            }else{
                event(new Failed(Auth::getDefaultDriver(), null, ['user' => $r->user, 'password' => $r->password]));
                return $this->failedLogin($r, 'You do not have any account with us.');
            }

            //Login Post Action End

        }
        // if(auth::check()){
        //     Redirect()->route('admin.dashboard');
        // }
        if ($lockMessage && !Session::has('error')) {
            Session::now('error', $lockMessage);
        }

        return view('Auth.adminLogin');

    }

    private function accountThrottleKey(Request $r): string
    {
        return 'login:' . Str::lower(trim((string) $r->input('user'))) . '|' . $r->ip();
    }

    private function ipThrottleKey(Request $r): string
    {
        return 'login-ip:' . $r->ip();
    }

    /** Why this login can't be tried right now (IP blocked or account locked), or null. */
    private function loginLockMessage(Request $r): ?string
    {
        if (RateLimiter::tooManyAttempts($this->ipThrottleKey($r), self::IP_MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($this->ipThrottleKey($r)) / 60);
            return "Too many failed logins from your network. Login is blocked for {$minutes} minute(s).";
        }

        if ($r->filled('user') && RateLimiter::tooManyAttempts($this->accountThrottleKey($r), self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($this->accountThrottleKey($r)) / 60);
            return "Too many wrong attempts. This account is locked for {$minutes} minute(s).";
        }

        return null;
    }

    /** Counts a wrong login against the account+IP and the IP, and tells the user what is left. */
    private function failedLogin(Request $r, string $message)
    {
        $accountKey = $this->accountThrottleKey($r);
        $ipKey = $this->ipThrottleKey($r);
        RateLimiter::hit($accountKey, self::LOCK_SECONDS);
        RateLimiter::hit($ipKey, self::IP_BLOCK_SECONDS);

        $context = ['user' => (string) $r->input('user'), 'ip' => $r->ip(), 'user_agent' => $r->userAgent()];
        if (RateLimiter::attempts($ipKey) === self::IP_MAX_ATTEMPTS) {
            Log::warning('Login blocked for IP after repeated failed attempts', $context);
        } elseif (RateLimiter::attempts($accountKey) === self::MAX_ATTEMPTS) {
            Log::warning('Login locked for account after repeated failed attempts', $context);
        }

        $left = self::MAX_ATTEMPTS - RateLimiter::attempts($accountKey);
        Session::flash('error', $this->loginLockMessage($r)
            ?? $message . ' ' . $left . ' attempt' . ($left === 1 ? '' : 's') . ' left.');

        return back()->withInput($r->only('user'));
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
                    'public_ip' => clientPublicIp($request),
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
