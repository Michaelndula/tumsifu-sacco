<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\LoanProcess;
use App\Models\TimeInterval;
use App\Models\GeneralSetting;
use App\Models\Loan;
use App\Models\LoanPlan;
use App\Models\PaidLog;
use App\Models\User;
use Illuminate\Http\Request;

class ManageLoanController extends Controller
{
    protected $pageTitle    = 'All Loans';
    protected $emptyMessage = 'No loan found';
    protected $view         = 'admin.loan.index';
    protected $loans        = null;

    public function allLoans()
    {
        $data  = $this->filterLoans();
        return view($this->view, $data);
    }

    public function paidLoans()
    {
        $data  = $this->filterLoans('paid');
        return view($this->view, $data);
    }

    public function activeLoans()
    {
        $data  = $this->filterLoans('active');
        return view($this->view, $data);
    }

    public function pendingLoans()
    {
        $data  = $this->filterLoans('pending');
        return view($this->view, $data);
    }

    protected function filterLoans($scope = null)
    {
        $loans = Loan::query();

        if($scope){
            $loans              = Loan::$scope();
            $this->pageTitle    = ucfirst($scope) . ' Loans';
            $this->emptyMessage = "No $scope loan found";
        }

        $searchKey = request()->search;

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
        }]);

        $loans = $loans->latest()->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        $data['loans'] = $loans;
        $data['days']  = $days;
        $data['pageTitle'] = $this->pageTitle;
        $data['emptyMessage'] = $this->emptyMessage;

        return $data;

    }

    public function showLoanForm(Request $request)
    {
        $pageTitle = 'Register New Loan';
        $plans = LoanPlan::where('status', 1)->get();
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('admin.loan.register', compact('pageTitle', 'plans', 'days'));
    }

    public function saveLoan(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'plan_id'  => 'required'
        ],[
            'user.required' => 'Username or email field required'
        ]);

        $user = User::where('email', $request->user)->orWhere('username', $request->user)->firstOrFail();
        $plan = LoanPlan::findOrFail($request->plan_id);

        $loan                       = new Loan();
        $loan->user_id              = $user->id;
        $loan->loan_plan_id         = $plan->id;
        $loan->loan_amount          = $plan->loan_amount;
        $loan->payable_amount       = $plan->payable_amount;
        $loan->installment          = $plan->installment;
        $loan->installment_interval = $plan->installment_interval;
        $loan->total_installment    = $plan->total_installment;
        $loan->late_fee             = $plan->fixed_late_fee + ($plan->installment * $plan->percent_late_fee / 100);
        $loan->status               = 1;
        $loan->save();
        
        $general = GeneralSetting::first();
        notify($loan->user, 'LOAN_REGISTER', [
            'loan_plan'            => $loan->loanPlan->name,
            'amount'               => showAmount($loan->loan_amount),
            'currency'             => $general->cur_text,
            'installment'          => showAmount($loan->installment),
            'total_installment'    => $loan->total_installment,
            'installment_interval' => $loan->installment_interval.' days'
        ]);

        $notify[] = ['success', 'Loan given successfully'];
        return back()->withNotify($notify);
    }

    public function pendingDetails($id)
    {
        $pageTitle = 'Pending Loan Details';
        $loan = Loan::with('loanPlan')->findOrFail($id);
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('admin.loan.pending_details', compact('pageTitle', 'loan', 'days'));
    }

    public function approveLoan(Request $request)
    {
        $loan = Loan::with('user', 'loanPlan')->findOrFail($request->loan_id);
        $loan->next_installment = now()->addDays($loan->installment_interval);
        $loan->status = 1;
        $loan->save();

        $general = GeneralSetting::first();
        notify($loan->user, 'LOAN_REGISTER', [
            'loan_plan'            => $loan->loanPlan->name,
            'amount'               => showAmount($loan->loan_amount),
            'currency'             => $general->cur_text,
            'installment'          => showAmount($loan->installment),
            'total_installment'    => $loan->total_installment,
            'installment_interval' => $loan->installment_interval.' days'
        ]);

        $notify[] = ['success', 'Loan approved successfully'];
        return back()->withNotify($notify);
    }

    public function installment(Request $request)
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

        $paidLog               = new PaidLog();
        $paidLog->loan_id      = $request->loan_id;
        $paidLog->user_id      = $loan->user->id;
        $paidLog->amount       = $loan->installment;
        $paidLog->late_fee     = $loanProcess->lateFee;
        $paidLog->final_amount = $loanProcess->amountWithLateFee;
        $paidLog->status       = 1;
        $paidLog->save();

        $loan->total_paid += $loan->installment;
        $loan->installment_given += 1;
        $loan->total_late_fee_paid += $loanProcess->lateFee;
        $loan->last_installment = now();
        $loan->next_installment = $loan->next_installment->addDays($loan->installment_interval);

        if($loan->total_installment == $loan->installment_given){
            $loan->status = 2;
        }
        $loan->save();

        $general = GeneralSetting::first();
        notify($loan->user, 'LOAN_PAID', [
            'amount'    => showAmount($loanProcess->amountWithLateFee),
            'currency'  => $general->cur_text,
            'paid_by'   => 'Admin',
            'loan_plan' => $loan->loanPlan->name
        ]);

        $notify[] = ['success', "Installment taken successfully"];
        return back()->withNotify($notify);
    }

    public function close(Request $request)
    {
        $loan = Loan::where('status', 1)->findOrFail($request->loan_id);
        $loan->status = 3;
        $loan->save();

        $notify[] = ['success', 'Loan closed successfully'];
        return back()->withNotify($notify);
    }

    public function userLoans($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = $user->fullname.' active loans';
        $emptyMessage = 'No active loans found';
        $loans = Loan::active()->where('user_id', $id)->with(['user', 'loanPlan', 'paidLogs'=>function($paidLogs){
            $paidLogs->where('status', 1);
        }])->paginate(getPaginate(20));
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('admin.loan.index', compact('pageTitle', 'emptyMessage', 'loans', 'days'));
    }

}
