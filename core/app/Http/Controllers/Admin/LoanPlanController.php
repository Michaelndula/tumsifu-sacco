<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeInterval;
use App\Models\LoanPlan;
use Illuminate\Http\Request;

class LoanPlanController extends Controller
{
    public function index()
    {
        $pageTitle = 'Loan Plans';
        $emptyMessage = 'No loan plan found';
        $loanPlans = LoanPlan::query();
        $searchKey = request()->search;

        if($searchKey){
            $loanPlans = $loanPlans->where('name', 'LIKE', "%$searchKey%");
        }
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        $loanPlans = $loanPlans->latest()->paginate(getPaginate());
        return view('admin.plan.loan', compact('pageTitle', 'emptyMessage', 'loanPlans', 'days'));
    }

    public function create()
    {
        $pageTitle = 'Add New Loan Plan';
        $days = TimeInterval::all();
        return view('admin.plan.loan_form', compact('pageTitle', 'days'));
    }

    public function edit($id)
    {
        $plan = LoanPlan::findOrFail($id);
        $pageTitle = 'Edit Loan Plan';
        $days = TimeInterval::all();
        return view('admin.plan.loan_form', compact('pageTitle', 'plan', 'days'));
    }

    public function saveLoanPlan(Request $request, $id=0)
    {
        $request->validate([
            'name'                 => 'required',
            'installment_interval' => 'required|integer|exists:time_intervals,day',
            'loan_amount'          => 'required|numeric|gt:0',
            'payable_amount'       => 'required|numeric|gt:0',
            'total_installment'    => 'required|integer|min:1',
            'description'          => 'required',
            'fixed_late_fee'       => 'required|numeric|min:0',
            'percent_late_fee'     => 'required|numeric|min:0',
            'field_name.*'         => 'sometimes|required'
        ],[
            'field_name.*.required'=>'All field is required'
        ]);

        if($request->payable_amount <= $request->loan_amount){
            $notify[] = ['error', 'Payable amount must be greater than loan amount'];
            return back()->withNotify($notify);
        }

        $input_form = [];
        if ($request->has('field_name')) {
            for ($i = 0; $i < count($request->field_name); $i++) {

                $arr = [];
                $arr['field_name'] = titleToKey($request->field_name[$i]);
                $arr['field_level'] = $request->field_name[$i];
                $arr['type'] = $request->type[$i];
                $arr['validation'] = $request->validation[$i];
                $input_form[$arr['field_name']] = $arr;
            }
        }
        $loanPlan = new LoanPlan();
        $type = 'added';

        if($id){
            $loanPlan = LoanPlan::findOrFail($id);
            $type = 'updated';
        }

        $loanPlan->name                 = $request->name;
        $loanPlan->loan_amount          = $request->loan_amount;
        $loanPlan->payable_amount       = $request->payable_amount;
        $loanPlan->installment          = $request->payable_amount / $request->total_installment;
        $loanPlan->total_installment    = $request->total_installment;
        $loanPlan->installment_interval = $request->installment_interval;
        $loanPlan->fixed_late_fee       = $request->fixed_late_fee;
        $loanPlan->percent_late_fee     = $request->percent_late_fee;
        $loanPlan->user_data            = $input_form;
        $loanPlan->description          = $request->description;
        $loanPlan->save();

        $notify[] = ['success', "Loan plan $type successfully"];
        return back()->withNotify($notify);
    }

    public function status(Request $request)
    {
        $plan = LoanPlan::findOrFail($request->plan_id);
        $plan->status = !$plan->status;
        $plan->save();

        $notify[] = ['success', $plan->name.($plan->status ? ' activated' : ' deactivated').' successfully'];
        return back()->withNotify($notify);
    }
}
