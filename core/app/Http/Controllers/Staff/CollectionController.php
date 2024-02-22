<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Lib\SavingsProcess;
use App\Lib\LoanProcess;
use App\Models\TimeInterval;
use App\Models\GeneralSetting;
use App\Models\Loan;
use App\Models\LoanPlan;
use App\Models\PaidLog;
use App\Models\Savings;
use App\Models\SavingsPlan;
use App\Models\User;
use Illuminate\Http\Request;

class CollectionController extends Controller
{

    public function loanPlan()
    {
        $pageTitle      = 'Loan Plans';
        $emptyMessage   = 'No loan plan found';
        $loanPlans      = LoanPlan::where('status', 1)->latest()->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('staff.collection.loan_plan', compact('pageTitle', 'emptyMessage', 'loanPlans', 'days'));
    }

    public function loanApplyForm($planId)
    {
        $pageTitle  = 'Apply For Loan';
        $plan       = LoanPlan::where('status', 1)->findOrFail($planId);
        $days       = TimeInterval::select('name', 'day')->get()->toArray();

        return view('staff.collection.loan_apply', compact('pageTitle', 'plan', 'days'));
    }

    public function loanApply(Request $request, $planId)
    {
        $plan = LoanPlan::where('status',1)->findOrFail($planId);
        $user = User::where('username', $request->user)->orWhere('email', $request->user)->firstOrFail();
        $loanProcess = new LoanProcess(['plan'=>$plan,'user'=>$user, 'staff_id'=>auth('staff')->id()]);
        $request->validate($loanProcess->applyValidation());
        $apply = $loanProcess->apply();
        return back()->withNotify($apply);
    }


    public function loans()
    {
        $pageTitle = 'All loans';
        $emptyMessage = 'No loan found';
        $segments = request()->segments();
        $lastSegment = end($segments);
        $searchKey = request()->search;

        if($lastSegment == 'pending'){
            $pageTitle = 'Pending Loans';
            $loans = Loan::pending()->where('staff_id', auth('staff')->id());;
        }elseif($lastSegment == 'active'){
            $pageTitle = 'Active Loans';
            $loans = Loan::active();
        }elseif($lastSegment == 'paid'){
            $pageTitle = 'Paid Loans';
            $loans = Loan::paid();
        }elseif($lastSegment == 'closed'){
            $pageTitle = 'Closed Loans';
            $loans = Loan::closed();
        }else{
            $loans = Loan::query();
        }

        if($searchKey){
            $loans = $loans->where(function($query) use($searchKey){
                $query->whereHas('user', function($user) use($searchKey){
                    $user->where('username', 'LIKE', "%$searchKey%");
                })->orWhereHas('loanPlan', function($loanPlan) use($searchKey){
                    $loanPlan->where('name', 'LIKE', "%$searchKey%");
                });
            });
        }

        $loans = $loans->with(['user', 'loanPlan', 'paidLogs'=>function($paidLog){
            $paidLog->where('status', 1);
        }])->latest()->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('staff.collection.loan', compact('pageTitle', 'emptyMessage', 'loans', 'days'));
    }

    public function loanInstallment(Request $request)
    {
        $loan = Loan::with(['user', 'loanPlan', 'paidLogs'=>function($paidLog){
            $paidLog->where('status', 1);
        }])->findOrFail($request->loan_id);

        $user = $loan->user;
        $loanProcess = new LoanProcess([
            'user'=>$user,
        ]);

        $response = $loanProcess->installment($loan);
        if ($response['error'] == true) {
            return back()->withNotify($response['message']);
        }

        $trx               = getTrx();

        $paidLog                = new PaidLog();
        $paidLog->loan_id       = $request->loan_id;
        $paidLog->user_id       = $loan->user->id;
        $paidLog->staff_id      = auth('staff')->id();
        $paidLog->admin_receive = 0;
        $paidLog->amount        = $loan->installment;
        $paidLog->late_fee      = $loanProcess->lateFee;
        $paidLog->final_amount  = $loanProcess->amountWithLateFee;
        $paidLog->trx           = $trx;
        $paidLog->status        = 1;
        $paidLog->save();

        $loanProcess->updateLoan($loan);

        $general = GeneralSetting::first();

        notify($loan->user, 'LOAN_PAID', [
            'amount' => showAmount($loanProcess->amountWithLateFee),
            'currency' => $general->cur_text,
            'paid_by' => 'Staff',
            'loan_plan' => $loan->loanPlan->name
        ]);

        $notify[] = ['success', "Installment taken successfully"];
        return back()->withNotify($notify);
    }

    public function savingsPlan(){
        $pageTitle    = 'Savings Plans';
        $emptyMessage = 'No savings plan found';
        $savingsPlans = SavingsPlan::where('status', 1)->latest()->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('staff.collection.savings_plan', compact('pageTitle', 'emptyMessage', 'savingsPlans', 'days'));
    }

    public function savingsApplyForm($planId){
        $pageTitle = 'Apply For Savings';
        $plan = SavingsPlan::where('status', 1)->findOrFail($planId);
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('staff.collection.savings_apply', compact('pageTitle', 'plan', 'days'));
    }

    public function savingsApply(Request $request, $planId){
        $plan = SavingsPlan::where('status',1)->findOrFail($planId);
        $user = User::where('username', $request->user)->orWhere('email', $request->user)->firstOrFail();
        $savingsProcess = new SavingsProcess(['plan'=>$plan,'user'=>$user, 'staff_id'=>auth('staff')->id()]);
        $request->validate($savingsProcess->applyValidation());
        $apply = $savingsProcess->apply();
        return back()->withNotify($apply);
    }

    public function savings(){
        $pageTitle = 'All savings';
        $emptyMessage = 'No savings found';
        $segments = request()->segments();
        $lastSegment = end($segments);
        $searchKey = request()->search;

        if($lastSegment == 'pending'){
            $pageTitle = 'Pending Loans';
            $savingsList = Savings::pending()->where('staff_id', auth('staff')->id());
        }elseif($lastSegment == 'active'){
            $pageTitle = 'Active Savings';
            $savingsList = Savings::active();
        }elseif($lastSegment == 'paid'){
            $pageTitle = 'Paid Savings';
            $savingsList = Savings::paid();
        }elseif($lastSegment == 'closed'){
            $pageTitle = 'Closed Savings';
            $savingsList = Savings::closed();
        }else{
            $savingsList = Savings::query();
        }

        if($searchKey){
            $savingsList = $savingsList->where(function($query) use($searchKey){
                $query->whereHas('user', function($user) use($searchKey){
                    $user->where('username', 'LIKE', "%$searchKey%");
                })->orWhereHas('savingsPlan', function($savingsPlan) use($searchKey){
                    $savingsPlan->where('name', 'LIKE', "%$searchKey%");
                });
            });
        }

        $savingsList = $savingsList->with(['user', 'savingsPlan', 'paidLogs'=>function($paidLog){
            $paidLog->where('status', 1);
        }])->latest()->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('staff.collection.savings', compact('pageTitle', 'emptyMessage', 'savingsList', 'days'));
    }

    public function savingsInstallment(Request $request){


        $savings = Savings::with(['user', 'savingsPlan', 'paidLogs'=>function($paidLog){
            $paidLog->where('status', 1);
        }])->findOrFail($request->savings_id);

        $user = $savings->user;
        $savingsProcess = new SavingsProcess([
            'user'=>$user,
        ]);

        $response = $savingsProcess->installment($savings);
        if ($response['error'] == true) {
            return back()->withNotify($response['message']);
        }

        $trx               = getTrx();

        $paidLog                = new PaidLog();
        $paidLog->savings_id    = $request->savings_id;
        $paidLog->user_id       = $savings->user->id;
        $paidLog->staff_id      = auth()->guard('staff')->id();
        $paidLog->admin_receive = 0;
        $paidLog->amount        = $savings->installment;
        $paidLog->late_fee      = $savingsProcess->lateFee;
        $paidLog->final_amount  = $savingsProcess->amountWithLateFee;
        $paidLog->trx           = $trx;
        $paidLog->status        = 1;
        $paidLog->save();

        $savingsProcess->updateSavings($savings);

        $general = GeneralSetting::first();


        notify($savings->user, 'SAVINGS_PAID', [
            'amount' => showAmount($savingsProcess->amountWithLateFee),
            'currency' => $general->cur_text,
            'paid_by' => 'Staff',
            'savings_plan' => $savings->savingsPlan->name
        ]);

        $notify[] = ['success', "Installment taken successfully"];
        return back()->withNotify($notify);
    }

    public function checkUser(Request $request)
    {
        $user = User::where('username', $request->user)->orWhere('email', $request->user)->first();
        if(!$user){
            return false;
        }
        return $user;
    }

    public function dailyCollection(){
        $emptyMessage = 'No collection found';

        $segmentThree = request()->segment(2);
        $segmentFour = request()->segment(3);

        $collections = PaidLog::where('staff_id', auth('staff')->id());

        if($segmentThree == 'loan-collections'){
            $pageTitle = 'Loan Collections';
            $collections = $collections->where('loan_id', '!=', 0);
        }elseif($segmentThree == 'savings-collections'){
            $pageTitle = 'Savings Collections';
            $collections = $collections ->where('savings_id', '!=', 0);
        }
        $collections = $collections
        ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m-%d')")
        ->selectRaw('*, sum(final_amount) as total_amount, count(*) as count');
        if($segmentFour == 'pending'){
            $pageTitle = 'Pending '.$pageTitle;
            $collections = $collections->where('admin_receive', 0);
        }elseif($segmentFour == 'paid'){
            $pageTitle = 'Paid '.$pageTitle;
            $collections = $collections->where('admin_receive', 1);
        }

        $collections = $collections->with('staff')->latest()->paginate(getPaginate());

        return view('staff.collection.daily', compact('pageTitle', 'emptyMessage', 'collections'));
    }
}
