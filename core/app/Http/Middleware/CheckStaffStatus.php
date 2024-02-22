<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStaffStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::guard('staff')->check()){
            $staff = auth()->guard('staff')->user();
            if ($staff->status  && $staff->ev  && $staff->sv  && $staff->tv) {
                return $next($request);
            } else {
                return redirect()->route('staff.authorization');
            }
        }
        abort(403);
    }
}
