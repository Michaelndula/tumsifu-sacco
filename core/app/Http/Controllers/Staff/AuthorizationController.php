<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizationController extends Controller
{
    public function checkValidCode($staff, $code, $add_min = 10000)
    {
        if (!$code) return false;
        if (!$staff->ver_code_send_at) return false;
        if ($staff->ver_code_send_at->addMinutes($add_min) < Carbon::now()) return false;
        if ($staff->ver_code !== $code) return false;
        return true;
    }


    public function authorizeForm()
    {
        if (auth()->guard('staff')->check()) {
            $staff = auth()->guard('staff')->user();
            if (!$staff->status) {
                Auth::guard('staff')->logout();
            }elseif (!$staff->ev) {
                if (!$this->checkValidCode($staff, $staff->ver_code)) {
                    $staff->ver_code = verificationCode(6);
                    $staff->ver_code_send_at = Carbon::now();
                    $staff->save();
                    sendEmail($staff, 'EVER_CODE', [
                        'code' => $staff->ver_code
                    ]);
                }
                $pageTitle = 'Email verification form';
                return view('staff.auth.authorization.email', compact('staff', 'pageTitle'));
            }elseif (!$staff->sv) {
                if (!$this->checkValidCode($staff, $staff->ver_code)) {
                    $staff->ver_code = verificationCode(6);
                    $staff->ver_code_send_at = Carbon::now();
                    $staff->save();
                    sendSms($staff, 'SVER_CODE', [
                        'code' => $staff->ver_code
                    ]);
                }
                $pageTitle = 'SMS verification form';
                return view('staff.auth.authorization.sms', compact('staff', 'pageTitle'));
            }elseif (!$staff->tv) {
                $pageTitle = 'Google Authenticator';
                return view('staff.auth.authorization.2fa', compact('staff', 'pageTitle'));
            }else{
                return redirect()->route('staff.dashboard');
            }

        }

        return redirect()->route('staff.login');
    }

    public function sendVerifyCode(Request $request)
    {
        $staff = Auth::guard('staff')->user();


        if ($this->checkValidCode($staff, $staff->ver_code, 2)) {
            $target_time = $staff->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $target_time - time();
            throw ValidationException::withMessages(['resend' => 'Please Try after ' . $delay . ' Seconds']);
        }
        if (!$this->checkValidCode($staff, $staff->ver_code)) {
            $staff->ver_code = verificationCode(6);
            $staff->ver_code_send_at = Carbon::now();
            $staff->save();
        } else {
            $staff->ver_code = $staff->ver_code;
            $staff->ver_code_send_at = Carbon::now();
            $staff->save();
        }



        if ($request->type === 'email') {
            sendEmail($staff, 'EVER_CODE',[
                'code' => $staff->ver_code
            ]);

            $notify[] = ['success', 'Email verification code sent successfully'];
            return back()->withNotify($notify);
        } elseif ($request->type === 'phone') {
            sendSms($staff, 'SVER_CODE', [
                'code' => $staff->ver_code
            ]);
            $notify[] = ['success', 'SMS verification code sent successfully'];
            return back()->withNotify($notify);
        } else {
            throw ValidationException::withMessages(['resend' => 'Sending Failed']);
        }
    }

    public function emailVerification(Request $request)
    {
        $request->validate([
            'email_verified_code'=>'required'
        ]);


        $email_verified_code = str_replace(' ','',$request->email_verified_code);
        $staff = Auth::guard('staff')->user();

        if ($this->checkValidCode($staff, $email_verified_code)) {
            $staff->ev = 1;
            $staff->ver_code = null;
            $staff->ver_code_send_at = null;
            $staff->save();
            return redirect()->route('staff.dashboard');
        }
        throw ValidationException::withMessages(['email_verified_code' => 'Verification code didn\'t match!']);
    }

    public function smsVerification(Request $request)
    {
        $request->validate([
            'sms_verified_code' => 'required',
        ]);


        $sms_verified_code =  str_replace(' ','',$request->sms_verified_code);

        $staff = Auth::guard('staff')->user();
        if ($this->checkValidCode($staff, $sms_verified_code)) {
            $staff->sv = 1;
            $staff->ver_code = null;
            $staff->ver_code_send_at = null;
            $staff->save();
            return redirect()->route('staff.dashboard');
        }
        throw ValidationException::withMessages(['sms_verified_code' => 'Verification code didn\'t match!']);
    }
    public function g2faVerification(Request $request)
    {
        $staff = auth()->guard('staff')->user();
        $request->validate([
            'code' => 'required',
        ]);
        $code = str_replace(' ','',$request->code);
        $response = verifyG2fa($staff,$code);
        if ($response) {
            $notify[] = ['success','Verification successful'];
        }else{
            $notify[] = ['error','Wrong verification code'];
        }
        return back()->withNotify($notify);
    }
}
