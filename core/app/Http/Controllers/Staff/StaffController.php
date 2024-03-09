<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Lib\GoogleAuthenticator;
use App\Models\GeneralSetting;
use App\Models\PaidLog;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function dashboard()
    {
        $pageTitle = 'Dashboard';
        $widget['today_loan_collection_count'] = PaidLog::where('staff_id', auth('staff')->id())->whereDate('created_at', now()->format('Y-m-d'))->where('loan_id', '!=', 0)->count();
        $widget['today_loan_collection_amount'] = PaidLog::where('staff_id', auth('staff')->id())->whereDate('created_at', now()->format('Y-m-d'))->where('loan_id', '!=', 0)->sum('final_amount'); 
        $widget['today_savings_collection_count'] = PaidLog::where('staff_id', auth('staff')->id())->whereDate('created_at', now()->format('Y-m-d'))->where('savings_id', '!=', 0)->count();
        $widget['today_savings_collection_amount'] = PaidLog::where('staff_id', auth('staff')->id())->whereDate('created_at', now()->format('Y-m-d'))->where('savings_id', '!=', 0)->sum('final_amount');
        $payments = PaidLog::where('staff_id', auth('staff')->id())->where('status', 1)->with('user', 'loan.loanPlan', 'savings.savingsPlan')->latest()->limit(10)->get();
        $emptyMessage = 'No payment history found';
        
        return view('staff.dashboard', compact('pageTitle', 'widget', 'payments', 'emptyMessage'));
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $staff = Auth::guard('staff')->user();
        return view('staff.profile', compact('pageTitle', 'staff'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|integer|min:1',
            'city' => 'sometimes|required|max:50',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])],
        ],[
            'firstname.required'=>'First name field is required',
            'lastname.required'=>'Last name field is required'
        ]);
        $staff = Auth::guard('staff')->user();

        $staff->firstname= $request->firstname;
        $staff->lastname= $request->lastname;

        $user = Auth::guard('staff')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = uploadImage($request->image, imagePath()['profile']['staff']['path'], imagePath()['profile']['staff']['size'], $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$staff->address->country,
            'city' => $request->city,
        ];

        $staff->fill($in)->save();

        $notify[] = ['success', 'Profile updated successfully.'];
        return back()->withNotify($notify);

    }

    public function changePassword()
    {
        $pageTitle = 'Change password';
        $staff = Auth::guard('staff')->user();
        return view('staff.password', compact('pageTitle', 'staff'));
    }

    public function submitPassword(Request $request)
    {
        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required','confirmed',$password_validation]
        ]);

        try {
            $staff = auth()->guard('staff')->user();
            if (Hash::check($request->current_password, $staff->password)) {
                $password = Hash::make($request->password);
                $staff->password = $password;
                $staff->save();
                $notify[] = ['success', 'Password changes successfully.'];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', 'The password doesn\'t match!'];
                return back()->withNotify($notify);
            }
        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function show2faForm()
    {
        $general = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $staff = auth()->guard('staff')->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($staff->username . '@' . $general->sitename, $secret);
        $pageTitle = 'Two Factor';
        return view('staff.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $staff = auth()->guard('staff')->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($staff,$request->code,$request->key);
        if ($response) {
            $staff->tsc = $request->key;
            $staff->ts = 1;
            $staff->save();
            $staffAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($staff, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$staffAgent['ip'],
                'time' => @$staffAgent['time']
            ], 'staff');
            $notify[] = ['success', 'Google authenticator enabled successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $staff = auth()->guard('staff')->user();
        $response = verifyG2fa($staff,$request->code);
        if ($response) {
            $staff->tsc = null;
            $staff->ts = 0;
            $staff->save();
            $staffAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($staff, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$staffAgent['ip'],
                'time' => @$staffAgent['time']
            ], 'staff');
            $notify[] = ['success', 'Two factor authenticator disable successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function paymentHistory($filter = null, $adminReceive = null){
        $pageTitle = 'Payment History';
        $emptyMessage = 'No history found';
        $segment = request()->segment(3);

        $payments = PaidLog::where('staff_id', auth('staff')->id())->where('status', 1);

        if($filter == 'today'){
            $payments = $payments->whereDate('created_at', now()->format('Y-m-d'));
        }elseif($filter != null){
            $payments = $payments->whereDate('created_at', $filter);
        }

        if($adminReceive != null){
            $payments = $payments->where('admin_receive', $adminReceive);
        }

        if($segment == 'loan'){
            $payments = $payments->where('loan_id', '!=', 0)->with('loan.loanPlan');
        }elseif($segment == 'savings'){
            $payments = $payments->where('savings_id', '!=', 0)->with('savings.savingsPlan');
        }else{
            $payments = $payments->with('loan.loanPlan', 'savings.savingsPlan');
        }
        $payments = $payments->with('user')->latest()->paginate(getPaginate());

        return view('staff.payment_history', compact('pageTitle', 'emptyMessage', 'payments'));
    }
}
