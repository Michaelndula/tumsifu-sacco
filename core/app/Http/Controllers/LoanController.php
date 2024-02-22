<?php

namespace App\Http\Controllers;

use App\Lib\LoanProcess;
use App\Models\GeneralSetting;
use App\Models\TimeInterval;
use App\Models\Loan;
use App\Models\LoanPlan;
use App\Models\PaidLog;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function plan(){
        $pageTitle = 'Loan Plans';
        $emptyMessage = 'No loan plan found';
        $loanPlans = LoanPlan::where('status', 1)->latest()->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('user.loan.plan', compact('pageTitle', 'emptyMessage', 'loanPlans', 'days'));
    }

    public function applyForm($planId){
        $pageTitle = 'Apply For Loan';
        $plan = LoanPlan::where('status', 1)->findOrFail($planId);
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('user.loan.apply', compact('pageTitle', 'plan', 'days'));
    }

    public function apply(Request $request, $planId){
        $plan = LoanPlan::where('status', 1)->findOrFail($planId);
        $loanProcess = new LoanProcess(['plan'=>$plan,'user'=>auth()->user()]);
        $request->validate($loanProcess->applyValidation());
        $apply = $loanProcess->apply();
        return back()->withNotify($apply);
    }

    public function loans(){
        $emptyMessage = 'No loans history found';
        $segments = request()->segments();
        $lastSegment = end($segments);

        if($lastSegment == 'pending'){
            $pageTitle = 'Pending Loans';
            $loans = Loan::pending();
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
            $pageTitle = 'All Loans';
            $loans = Loan::query();
        }

        $loans = $loans->where('user_id', auth()->id())->with('loanPlan')->latest()->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('user.loan.index', compact('pageTitle', 'emptyMessage', 'loans', 'days'));
    }

    public function payment(Request $request){

        $request->validate([
            'loan_id' => 'required',
        ]);

        $loan = Loan::where('id', $request->loan_id)->where('user_id', auth()->id())->with('loanPlan')->firstOrFail();

        $trx = getTrx();
        $user =  auth()->user();
        $loanProcess = new LoanProcess(['user'=>$user]);
        
        $response = $loanProcess->installment($loan);
        if ($response['error'] == true) {
            return back()->withNotify($response['notify']);
        }

        $paidLog               = new PaidLog();
        $paidLog->loan_id      = $loan->id;
        $paidLog->user_id      = $user->id;
        $paidLog->amount       = $loan->installment;
        $paidLog->late_fee     = $loanProcess->lateFee;
        $paidLog->final_amount = $loanProcess->amountWithLateFee;
        $paidLog->trx          = $trx;
        $paidLog->save();

        session()->put('payment_data',[
            'paid_log_id' => $paidLog->id,
            'amount'      => $loanProcess->amountWithLateFee,
            'trx'         => $paidLog->trx
        ]);

        return redirect()->route('user.deposit');
    }
}
