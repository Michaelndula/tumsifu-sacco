<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeInterval;
use Illuminate\Http\Request;

class TimeIntervalController extends Controller
{
    public function index(){
        $pageTitle      = 'Time Intervals';
        $timeIntervals        = TimeInterval::get();
        $emptyMessage   = 'Time interval not found';
        return view('admin.time_intervals.index',compact('pageTitle','timeIntervals','emptyMessage'));
    }

    public function saveDay(Request $request, $id = 0){
        $request->validate([
            'name'=>'required',
            'day'=>'required|integer|gt:0',
        ]);

        if($id){
            $day = TimeInterval::findOrFail($id);
            $notification = 'Timer interval updated successfully';
        }else{
            $day = new TimeInterval();
            $notification = 'Timer interval added successfully';
        }

        $day->name = $request->name;
        $day->day = $request->day;
        $day->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id'=>'required|integer',
        ]);

        $timeInterval = TimeInterval::where('id', $request->id)->delete();

        if($timeInterval){
            $notify[] = ['success','Time Interval deleted successfully'];
            return back()->withNotify($notify);
        }

        $notify[] = ['error', 'Something wrong'];
        return back()->withNotify($notify);
    }
}
