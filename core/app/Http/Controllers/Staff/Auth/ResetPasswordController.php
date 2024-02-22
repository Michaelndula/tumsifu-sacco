<?php

namespace App\Http\Controllers\Staff\Auth;

use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Models\StaffPasswordReset;
use App\Models\Staff;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
    

    public function __construct()
    {
        $this->middleware('staff.guest');
    }

    public function showResetForm(Request $request, $token = null)
    {

        $email = session('fpass_email');
        $token = session()->has('token') ? session('token') : $token;
        if (StaffPasswordReset::where('token', $token)->where('email', $email)->count() != 1) {
            $notify[] = ['error', 'Invalid token'];
            return redirect()->route('staff.password.request')->withNotify($notify);
        }
        return view('staff.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $email, 'pageTitle' => 'Reset Password']
        );
    }

    public function reset(Request $request)
    {

        session()->put('fpass_email', $request->email);
        $request->validate($this->rules(), $this->validationErrorMessages());
        $reset = StaffPasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        if (!$reset) {
            $notify[] = ['error', 'Invalid verification code'];
            return redirect()->route('staff.login')->withNotify($notify);
        }

        $staff = Staff::where('email', $reset->email)->first();
        $staff->password = bcrypt($request->password);
        $staff->save();



        $staffIpInfo = getIpInfo();
        $staffBrowser = osBrowser();
        sendEmail($staff, 'PASS_RESET_DONE', [
            'operating_system' => @$staffBrowser['os_platform'],
            'browser' => @$staffBrowser['browser'],
            'ip' => @$staffIpInfo['ip'],
            'time' => @$staffIpInfo['time']
        ]);

        $notify[] = ['success', 'Password changed successfully'];
        return redirect()->route('staff.login')->withNotify($notify);
    }



    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required','confirmed',$password_validation],
        ];
    }

}
