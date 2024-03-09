@extends('staff.layouts.app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
            <div class="icon">
                <i class="las la-money-bill-wave"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ __($widget['today_loan_collection_count']) }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Today Loan Collection Count')</span>
                </div>
                <a href="{{ route('staff.payment.loan.history', 'today') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--cyan b-radius--10 box-shadow">
            <div class="icon">
                <i class="las la-comments-dollar"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{__($general->cur_sym)}}</span>
                    <span class="amount">{{ showAmount($widget['today_loan_collection_amount']) }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Today Loan Collection')</span>
                </div>
                <a href="{{ route('staff.payment.loan.history', 'today') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--teal b-radius--10 box-shadow">
            <div class="icon">
                <i class="las la-money-check-alt"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $widget['today_savings_collection_count'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Today Savings Collection Count')</span>
                </div>
                <a href="{{ route('staff.payment.savings.history', 'today') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--green b-radius--10 box-shadow">
            <div class="icon">
                <i class="las la-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{__($general->cur_sym)}}</span>
                    <span class="amount">{{showAmount($widget['today_savings_collection_amount'])}}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Today Savings Collection')</span>
                </div>
                <a href="{{ route('staff.payment.savings.history', 'today') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
</div>


<div class="card b-radius--10 mt-4">
    <div class="card-header">
        <h5 class="d-inline">@lang('Payment History')</h5>
        <a href="{{ route('staff.payment.history') }}" class="btn btn-sm btn--primary box--shadow1 text--small float-right">@lang('View All')</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive--md  table-responsive">
            <table class="table table--light style--two">
                <thead>
                <tr>
                    <th>@lang('S.N.')</th>
                    <th>@lang('User Name')</th>
                    @if (request()->routeIs('staff.dashboard'))
                        <th>@lang('Type')</th>
                    @endif
                    <th>@lang('Plan Name')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Late Fee')</th>
                    <th>@lang('Final Amount')</th>
                    <th>@lang('Date Time')</th>
                </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)     
                        <tr>
                            <td data-label="@lang('S.N')">{{ $loop->iteration }}</td>
                            <td data-label="@lang('User Name')">{{ __($payment->user->fullname) }} <br>
                                @<span>{{ $payment->user->username }}</span>
                            </td>
                            @if (request()->routeIs('staff.dashboard'))
                            <td data-label="@lang('Type')">{{ $payment->loan_id ? __('Loan') : __('Savings') }}</td>
                            @endif
                            <td data-label="@lang('Plan Name')">{{ $payment->loan_id ? __($payment->loan->loanPlan->name) : __($payment->savings->savingsPlan->name) }}</td>
                            <td data-label="@lang('Amount')">{{ $general->cur_sym }}{{ showAmount($payment->amount) }}</td>                                    
                            <td data-label="@lang('Amount')">{{ $general->cur_sym }}{{ showAmount($payment->late_fee) }}</td>                                    
                            <td data-label="@lang('Amount')">{{ $general->cur_sym }}{{ showAmount($payment->final_amount) }}</td>                                    
                            <td data-label="@lang('Date Time')">{{ showDateTime($payment->created_at) }}</td>
                        </tr>
                    @empty
                    <tr>
                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table><!-- table end -->
        </div>
    </div>
</div>
@endsection
