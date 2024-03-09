<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeInterval;
use App\Models\SavingsPlan;
use Illuminate\Http\Request;

class SavingsPlanController extends Controller
{
    public function index(){
        $pageTitle = 'Savings Plans';
        $emptyMessage = 'No savings plan found';
        $savingsPlans = SavingsPlan::query();
        $searchKey = request()->search;

        if( $searchKey){
            $savingsPlans = $savingsPlans->where('name', 'LIKE', "%$searchKey%");
        }

        $savingsPlans = $savingsPlans->latest()->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('admin.plan.savings', compact('pageTitle', 'emptyMessage', 'savingsPlans', 'days'));
    }

    public function create()
    {
        $pageTitle = 'Add New Savings Plan';
        $days = TimeInterval::all();
        return view('admin.plan.savings_form', compact('pageTitle', 'days'));
    }

    public function edit($id)
    {
        $plan = SavingsPlan::findOrFail($id);
        $pageTitle = 'Edit Savings Plan';
        $days = TimeInterval::all();
        return view('admin.plan.savings_form', compact('pageTitle', 'plan', 'days'));
    }

    public function saveSavingsPlan(Request $request, $id=0){
        $request->validate([
            'name'                 => 'required',
            'installment_interval' => 'required|integer|exists:time_intervals,day',
            'per_installment'      => 'required|numeric|gt:0',
            'giveable_amount'      => 'required|numeric|gt:0',
            'total_installment'    => 'required|integer|min:1',
            'description'          => 'required',
            'fixed_late_fee'       => 'required|numeric|min:0',
            'percent_late_fee'     => 'required|numeric|min:0',
            'field_name.*'         => 'sometimes|required'
        ],[
            'field_name.*.required'=>'All field is required'
        ]);

        $savingsAmount = $request->per_installment * $request->total_installment;
        if($savingsAmount > $request->giveable_amount){
            $notify[] = ['error', 'Giveable amount must be greater than savings amount'];
            return back()->withNotify($notify);
        }

        $inputForm = [];
        if ($request->has('field_name')) {
            for ($a = 0; $a < count($request->field_name); $a++) {
                $arr = [];
                $arr['field_name'] = strtolower(str_replace(' ', '_', $request->field_name[$a]));
                $arr['field_level'] = $request->field_name[$a];
                $arr['type'] = $request->type[$a];
                $arr['validation'] = $request->validation[$a];
                $inputForm[$arr['field_name']] = $arr;
            }
        }

        $savingsPlan = new SavingsPlan();
        $type = 'added';

        if($id){
            $savingsPlan = SavingsPlan::findOrFail($id);
            $type = 'updated';
        }

        $savingsPlan->name                 = $request->name;
        $savingsPlan->savings_amount       = $savingsAmount;
        $savingsPlan->giveable_amount      = $request->giveable_amount;
        $savingsPlan->installment          = $request->per_installment;
        $savingsPlan->installment_interval = $request->installment_interval;
        $savingsPlan->total_installment    = $request->total_installment;
        $savingsPlan->fixed_late_fee       = $request->fixed_late_fee;
        $savingsPlan->percent_late_fee     = $request->percent_late_fee;
        $savingsPlan->user_data            = $inputForm;
        $savingsPlan->description          = $request->description;
        $savingsPlan->save();

        $notify[] = ['success', "Saving plan $type successfully"];
        return back()->withNotify($notify);
    }

    public function status(Request $request){
        $plan = SavingsPlan::findOrFail($request->plan_id);
        $plan->status = !$plan->status;
        $plan->save();

        $notify[] = ['success', $plan->name.($plan->status ? ' Activated' : ' Deactivated').' Successfully'];
        return back()->withNotify($notify);
    }
}
