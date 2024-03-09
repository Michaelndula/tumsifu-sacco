<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeInterval;
use App\Models\PaidLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManageCollectionController extends Controller
{
    public function collections($date = null, $staffId = null, $adminReceive = null){
        $emptyMessage = 'No collection found';
        $segment = request()->segment(3);
        $collections = PaidLog::query();

        if($segment == 'loan'){
            $pageTitle = 'Loan Collections';
            $collections = $collections->where('loan_id', '!=', 0)->with('loan.loanPlan');
        }elseif($segment == 'savings'){
            $pageTitle = 'Savings Collections';
            $collections = $collections->where('savings_id', '!=', 0)->with('savings.savingsPlan');
        }else{
            $pageTitle = 'All Collections';
            $collections = $collections->with('loan.loanPlan', 'savings.savingsPlan');
            if($date == 'yesterday'){
                $collections = $collections->whereDate('created_at', Carbon::yesterday()->format('Y-m-d'));
            }
        }

        if($date == 'today'){
            $collections = $collections->whereDate('created_at', Carbon::now()->format('Y-m-d'));
        }

        if($staffId){
            $collections = $collections->where('staff_id', $staffId)->whereDate('created_at', $date);
        }

        if($adminReceive != null){
            $collections = $collections->where('admin_receive', $adminReceive);
        }

        $searchKey = request()->search;
        if($searchKey){
            $collections = $collections->whereHas('user', function($user) use($searchKey){
                $user->where(function($query) use($searchKey){
                    $query->where('firstname', 'LIKE', "%$searchKey%")->orWhere('lastname', 'LIKE', "%$searchKey%")->orWhere('username', 'LIKE', "%$searchKey%");
                });
            })->orWhereHas('staff', function($staff) use($searchKey){
                $staff->where(function($query) use($searchKey){
                    $query->where('firstname', 'LIKE', "%$searchKey%")->orWhere('lastname', 'LIKE', "%$searchKey%")->orWhere('username', 'LIKE', "%$searchKey%");
                });
            });
        }

        $collections = $collections->where('status', 1)->with('user', 'staff')->latest()->paginate(getPaginate());
        $days = TimeInterval::select('name', 'day')->get()->toArray();

        return view('admin.collection.collections', compact('pageTitle', 'emptyMessage', 'collections', 'days'));

    }

    public function staffCollection(){

        $emptyMessage = 'No collection found';

        $segmentThree = request()->segment(3);
        $segmentFour  = request()->segment(4);

        $collections = PaidLog::where('staff_id', '!=', 0);

        if($segmentThree == 'loan-collections'){
            $pageTitle = 'Loan Collections By Staff';
            $collections = $collections->where('loan_id', '!=', 0);
        }elseif($segmentThree == 'savings-collections'){
            $pageTitle = 'Savings Collections By Staff';
            $collections = $collections ->where('savings_id', '!=', 0);
        }

        $collections = $collections->groupBy('staff_id')
        ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m-%d')")
        ->selectRaw('*, sum(amount) as total_amount, count(*) as count');

        if($segmentFour == 'pending'){
            $pageTitle = 'Pending '.$pageTitle;
            $collections = $collections->where('admin_receive', 0);
        }elseif($segmentFour == 'paid'){
            $pageTitle = 'Paid '.$pageTitle;
            $collections = $collections->where('admin_receive', 1);
        }

        $searchKey = request()->search;
        if($searchKey){
            $collections = $collections->whereHas('staff', function($staff) use($searchKey){
                $staff->where(function($query) use($searchKey){
                    $query->where('firstname', 'LIKE', "%$searchKey%")->orWhere('lastname', 'LIKE', "%$searchKey%")->orWhere('username', 'LIKE', "%$searchKey%");
                });
            });
        }

        $collections = $collections->with('staff')->latest()->paginate(getPaginate());

        return view('admin.collection.by_staff', compact('pageTitle', 'emptyMessage', 'collections'));
    }

    public function collectionConfirmation(Request $request){
        $request->validate([
            'staff_id' => 'required',
            'date'     => 'required|date'
        ]);
        $paidLogs = PaidLog::where('staff_id', $request->staff_id)
        ->whereDate('created_at', Carbon::parse($request->date));

        if($request->loan_id){
            $paidLogs = $paidLogs->where('loan_id', '!=', 0);
        }else{
            $paidLogs = $paidLogs->where('savings_id', '!=', 0);
        }

        $paidLogs->update(['admin_receive' => 1]);

        $notify[] = ['success', 'Received collections successfully'];
        return back()->withNotify($notify);
    }
}
