<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Loan;
use App\Models\Savings;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\Page;
use App\Models\Staff;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $activeTemplate = activeTemplate();
        $general = GeneralSetting::first();
        $viewShare['general'] = $general;
        $viewShare['activeTemplate'] = $activeTemplate;
        $viewShare['activeTemplateTrue'] = activeTemplate(true);
        $viewShare['language'] = Language::all();
        $viewShare['pages'] = Page::where('tempname',$activeTemplate)->where('is_default',0)->get();
        view()->share($viewShare);


        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'banned_users_count'            => User::banned()->count(),
                'email_unverified_users_count'  => User::emailUnverified()->count(),
                'sms_unverified_users_count'    => User::smsUnverified()->count(),
                'banned_staffs_count'           => Staff::banned()->count(),
                'email_unverified_staffs_count' => Staff::emailUnverified()->count(),
                'sms_unverified_staffs_count'   => Staff::smsUnverified()->count(),
                'pending_loan_count'            => Loan::where('status', 0)->count(),
                'pending_savings_count'         => Savings::where('status', 0)->count(),
                'pending_matured_savings_count' => Savings::where('status', 2)->where('transfer_user', 0)->count(),
                'pending_user_ticket_count'     => SupportTicket::where('user_id', '!=', 0)->whereIN('status', [0,2])->count(),
                'pending_staff_ticket_count'    => SupportTicket::where('staff_id', '!=', 0)->whereIN('status', [0,2])->count(),
                'pending_deposits_count'        => Deposit::pending()->count(),

            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications'=>AdminNotification::where('read_status',0)->with('user')->orderBy('id','desc')->get(),
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        if($general->force_ssl){
            \URL::forceScheme('https');
        }

        Paginator::useBootstrap();

    }
}
