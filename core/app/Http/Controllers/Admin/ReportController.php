<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\UserLogin;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function userLoginHistory(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'User Login History Search - ' . $search;
            $emptyMessage = 'No search result found.';
            $login_logs = UserLogin::where('user_id', '!=', 0)->whereHas('user', function ($query) use ($search) {
                $query->where('username', $search);
            })->orderBy('id','desc')->with('user')->paginate(getPaginate());
            return view('admin.reports.user_logins', compact('pageTitle', 'emptyMessage', 'search', 'login_logs'));
        }
        $pageTitle = 'User Login History';
        $emptyMessage = 'No users login found.';
        $login_logs = UserLogin::where('user_id', '!=', 0)->orderBy('id','desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.user_logins', compact('pageTitle', 'emptyMessage', 'login_logs'));
    }

    public function userLoginIpHistory($ip)
    {
        $pageTitle = 'Login By - ' . $ip;
        $login_logs = UserLogin::where('user_ip',$ip)->where('user_id', '!=', 0)->orderBy('id','desc')->with('user')->paginate(getPaginate());
        $emptyMessage = 'No users login found.';
        return view('admin.reports.user_logins', compact('pageTitle', 'emptyMessage', 'login_logs','ip'));

    }

    public function staffLoginHistory(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Staff Login History Search - ' . $search;
            $emptyMessage = 'No search result found.';
            $login_logs = UserLogin::where('staff_id', '!=', 0)->whereHas('staff', function ($query) use ($search) {
                $query->where('username', $search);
            })->orderBy('id','desc')->with('staff')->paginate(getPaginate());
            return view('admin.reports.staff_logins', compact('pageTitle', 'emptyMessage', 'search', 'login_logs'));
        }
        $pageTitle = 'Staff Login History';
        $emptyMessage = 'No staffs login found.';
        $login_logs = UserLogin::where('staff_id', '!=', 0)->orderBy('id','desc')->with('staff')->paginate(getPaginate());
        return view('admin.reports.staff_logins', compact('pageTitle', 'emptyMessage', 'login_logs'));
    }

    public function staffLoginIpHistory($ip)
    {
        $pageTitle = 'Login By - ' . $ip;
        $login_logs = UserLogin::where('user_ip',$ip)->where('staff_id', '!=', 0)->orderBy('id','desc')->with('staff')->paginate(getPaginate());
        $emptyMessage = 'No staffs login found.';
        return view('admin.reports.staff_logins', compact('pageTitle', 'emptyMessage', 'login_logs','ip'));

    }

    public function userEmailHistory(){
        $pageTitle = 'User Email history';
        $logs = EmailLog::where('user_id', '!=', 0)->with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.reports.user_email_history', compact('pageTitle', 'emptyMessage','logs'));
    }

    public function staffEmailHistory(){
        $pageTitle = 'Staff Email history';
        $logs = EmailLog::where('staff_id', '!=', 0)->with('staff')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.reports.staff_email_history', compact('pageTitle', 'emptyMessage','logs'));
    }
}
