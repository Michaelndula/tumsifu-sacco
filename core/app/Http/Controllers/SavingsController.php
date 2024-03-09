<?php

namespace App\Http\Controllers;

use App\Lib\SavingsProcess;
use App\Models\GeneralSetting;
use App\Models\TimeInterval;
use App\Models\PaidLog;
use App\Models\Savings;
use App\Models\SavingsPlan;
use Illuminate\Http\Request;

class SavingsController extends Controller
{
    public function plan(){
        $pageTitle = 'Savings Plans';
        $emptyMessage = 'No savings plan found';
        $savingsPlans = SavingsPlan::where('status', 1)->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('user.savings.plan', compact('pageTitle', 'emptyMessage', 'savingsPlans', 'days'));
    }

    public function applyForm($planId){
        $pageTitle = 'Apply For Savings';
        $plan = SavingsPlan::where('status', 1)->findOrFail($planId);
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('user.savings.apply', compact('pageTitle', 'plan', 'days'));
    }

    public function apply(Request $request, $planId){
        $plan = SavingsPlan::where('status',1)->findOrFail($planId);
        $savingsProcess = new SavingsProcess(['plan'=>$plan,'user'=>auth()->user()]);
        $request->validate($savingsProcess->applyValidation());
        $apply = $savingsProcess->apply();
        return back()->withNotify($apply);
    }

    public function savings(){
        $emptyMessage = 'No savings history found';
        $segments = request()->segments();
        $lastSegment = end($segments);

        if($lastSegment == 'pending'){
            $pageTitle = 'Pending savings';
            $savingsList = Savings::pending();
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
            $pageTitle = 'All Savings';
            $savingsList = Savings::query();
        }

        $savingsList = $savingsList->where('user_id', auth()->id())->with('savingsPlan')->latest()->paginate(getPaginate());

        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('user.savings.index', compact('pageTitle', 'emptyMessage', 'savingsList', 'days'));
    }

    public function payment(Request $request){
        $request->validate([
            'savings_id' => 'required',
        ]);

        $savings = Savings::where('id', $request->savings_id)->where('user_id', auth()->id())->with('savingsPlan')->firstOrFail();

        $trx = getTrx();
        $user =  auth()->user();
        $savingsProcess = new SavingsProcess([
            'user'=>$user,
        ]);
    
        $response = $savingsProcess->installment($savings);
        if ($response['error'] == true) {
            return back()->withNotify($response['notify']);
        }

        $paidLog               = new PaidLog();
        $paidLog->savings_id   = $savings->id;
        $paidLog->user_id      = auth()->id();
        $paidLog->amount       = $savings->installment;
        $paidLog->late_fee     = $savingsProcess->lateFee;
        $paidLog->final_amount = $savingsProcess->amountWithLateFee;
        $paidLog->trx          = $trx;

        $paidLog->save();
        session()->put('payment_data',[
            'paid_log_id' => $paidLog->id,
            'amount'  => $savingsProcess->amountWithLateFee,
            'trx'         => $paidLog->trx
        ]);
        return redirect()->route('user.deposit');
    }
}
