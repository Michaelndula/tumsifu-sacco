<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\GeneralSetting;
use App\Models\PaidLog;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ManageStaffsController extends Controller
{
    protected $pageTitle;

    protected function filterStaffs($scope = null)
    {
        $request    = request();
        $staffs     = Staff::query();

        if($scope){
            $staffs = $staffs->$scope();
        }
        $pageTitle  = $this->pageTitle;

        $search     = $request->search;

        if($search){
            $staffs = $staffs->where(function ($staff) use ($search) {
                $staff->where('username', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $staffs         = $staffs->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage   = 'No staff found';

        $info = json_decode(json_encode(getIpInfo()), true);
        $mobile_code = @implode(',', $info['code']);
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('admin.staffs.list', compact('pageTitle', 'emptyMessage', 'staffs', 'scope', 'mobile_code', 'countries'));
    }

    public function allStaffs()
    {
        $this->pageTitle = 'All Staffs';
        return $this->filterStaffs();
    }

    public function activeStaffs()
    {
        $this->pageTitle = 'Active Staffs';
        return $this->filterStaffs('active');
    }

    public function bannedStaffs()
    {
        $this->pageTitle = 'Banned Staffs';
        return $this->filterStaffs('banned');
    }

    public function emailUnverifiedStaffs()
    {
        $this->pageTitle = 'Email Unverified Staffs';
        return $this->filterStaffs('emailUnverified');
    }

    public function emailVerifiedStaffs()
    {
        $this->pageTitle = 'Email Verified Staffs';
        return $this->filterStaffs('emailVerified');
    }

    public function smsUnverifiedStaffs()
    {
        $this->pageTitle = 'SMS Unverified Staffs';
        return $this->filterStaffs('smsUnverified');
    }


    public function smsVerifiedStaffs()
    {
        $this->pageTitle = 'SMS Verified Staffs';
        return $this->filterStaffs('smsVerified');
    }

    public function detail($id)
    {
        $pageTitle = 'Staff Detail';
        $staff = Staff::findOrFail($id);

        $widget['today_loan_collection_count'] = PaidLog::where('staff_id', $id)->whereDate('created_at', now()->format('Y-m-d'))->where('loan_id', '!=', 0)->count();
        $widget['today_loan_collection_amount'] = PaidLog::where('staff_id', $id)->whereDate('created_at', now()->format('Y-m-d'))->where('loan_id', '!=', 0)->sum('amount');
        $widget['today_savings_collection_count'] = PaidLog::where('staff_id', $id)->whereDate('created_at', now()->format('Y-m-d'))->where('savings_id', '!=', 0)->count();
        $widget['today_savings_collection_amount'] = PaidLog::where('staff_id', $id)->whereDate('created_at', now()->format('Y-m-d'))->where('savings_id', '!=', 0)->sum('amount');

        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.staffs.detail', compact('pageTitle', 'staff', 'widget', 'countries'));
    }


    public function update(Request $request, $id)
    {
        $staff       = Staff::findOrFail($id);
        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        $request->validate([
            'firstname' => 'required|max:50',
            'lastname'  => 'required|max:50',
            'email'     => 'required|email|max:90|unique:staffs,email,' . $staff->id,
            'mobile'    => 'required|unique:staffs,mobile,' . $staff->id,
            'country'   => 'required',
        ]);
        $countryCode         = $request->country;
        $staff->mobile       = $request->mobile;
        $staff->country_code = $countryCode;
        $staff->firstname    = $request->firstname;
        $staff->lastname     = $request->lastname;
        $staff->email        = $request->email;
        $staff->address      = [
                            'address' => $request->address,
                            'city'    => $request->city,
                            'state'   => $request->state,
                            'zip'     => $request->zip,
                            'country' => @$countryData->$countryCode->country,
                        ];
        $staff->status       = $request->status ? 1 : 0;
        $staff->ev           = $request->ev ? 1 : 0;
        $staff->sv           = $request->sv ? 1 : 0;
        $staff->ts           = $request->ts ? 1 : 0;
        $staff->tv           = $request->tv ? 1 : 0;
        $staff->save();

        $notify[]                     = ['success', 'Staff detail has been updated'];
        return redirect()->back()->withNotify($notify);
    }

    public function staffLoginHistory($id)
    {
        $staff = Staff::findOrFail($id);
        $pageTitle = 'Staff Login History - ' . $staff->username;
        $emptyMessage = 'No staffs login found.';
        $login_logs = $staff->login_logs()->orderBy('id','desc')->with('staff')->paginate(getPaginate());
        return view('admin.staffs.logins', compact('pageTitle', 'emptyMessage', 'login_logs'));
    }

    public function showEmailSingleForm($id)
    {
        $staff = Staff::findOrFail($id);
        $pageTitle = 'Send Email To: ' . $staff->username;
        return view('admin.staffs.email_single', compact('pageTitle', 'staff'));
    }

    public function sendEmailSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        $staff = Staff::findOrFail($id);
        sendGeneralEmail($staff->email, $request->subject, $request->message, $staff->username);
        $notify[] = ['success', $staff->username . ' will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function showEmailAllForm()
    {
        $pageTitle = 'Send Email To All Staffs';
        return view('admin.staffs.email_all', compact('pageTitle'));
    }

    public function sendEmailAll(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        foreach (Staff::where('status', 1)->cursor() as $staff) {
            sendGeneralEmail($staff->email, $request->subject, $request->message, $staff->username);
        }

        $notify[] = ['success', 'All staffs will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function login($id)
    {
        $staff = Staff::findOrFail($id);
        Auth::guard('staff')->login($staff);
        return redirect()->route('staff.dashboard');
    }

    public function emailLog($id)
    {
        $staff = Staff::findOrFail($id);
        $pageTitle = 'Email log of '.$staff->username;
        $logs = EmailLog::where('staff_id',$id)->with('staff')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.staffs.email_log', compact('pageTitle','logs','emptyMessage','staff'));
    }

    public function emailDetails($id){
        $email = EmailLog::findOrFail($id);
        $pageTitle = 'Email details';
        return view('admin.staffs.email_details', compact('pageTitle','email'));
    }

    public function showRegistrationForm(){
        $pageTitle = 'Staff Register';
       
        return view('admin.staffs.register', compact('pageTitle', 'mobile_code', 'countries'));
    }

    public function register(Request $request){
        $general = GeneralSetting::first();
        $password_validation = Password::min(6);
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',',array_column($countryData, 'dial_code'));
        $countries = implode(',',array_column($countryData, 'country'));

        $request->validate([
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'required|string|email|max:90|unique:staffs',
            'mobile' => 'required|string|max:50|unique:staffs',
            'password' => ['required','confirmed',$password_validation],
            'username' => 'required|alpha_num|unique:staffs|min:6',
            'captcha' => 'sometimes|required',
            'mobile_code' => 'required|in:'.$mobileCodes,
            'country_code' => 'required|in:'.$countryCodes,
            'country' => 'required|in:'.$countries,
        ]);

        $exist = Staff::where('mobile',$request->mobile_code.$request->mobile)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }

        //Staff Create
        $staff = new Staff();
        $staff->firstname = $request->firstname;
        $staff->lastname = $request->lastname;
        $staff->email = strtolower(trim($request->email));
        $staff->password = Hash::make($request->password);
        $staff->username = trim($request->username);
        $staff->country_code = $request->country_code;
        $staff->mobile = $request->mobile_code.$request->mobile;
        $staff->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => $request->country,
            'city' => ''
        ];
        $staff->status = 1;
        $staff->ev = $general->ev ? 0 : 1;
        $staff->sv = $general->sv ? 0 : 1;
        $staff->ts = 0;
        $staff->tv = 1;
        $staff->save();

        $notify[] = ['success', 'New staff added successfully']; 
        return back()->withNotify($notify);
    }

    public function checkUser(Request $request){
        $exist['data'] = null;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = Staff::where('email',$request->email)->first();
            $exist['type'] = 'email';
        }
        if ($request->mobile) {
            $exist['data'] = Staff::where('mobile',$request->mobile)->first();
            $exist['type'] = 'mobile';
        }
        if ($request->username) {
            $exist['data'] = Staff::where('username',$request->username)->first();
            $exist['type'] = 'username';
        }
        return response($exist);
    }
}
