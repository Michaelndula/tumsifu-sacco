@extends('user.layouts.app')

@section('panel')
    
<div class="row mb-none-30">
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
            <div class="icon">
                <i class="las la-comment-dollar"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $widget['active_loans'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Active Loans')</span>
                </div>
                <a href="{{ route('user.loan.active') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--cyan b-radius--10 box-shadow">
            <div class="icon">
                <i class="las la-file-invoice-dollar"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{__($general->cur_sym)}}</span>
                    <span class="amount">{{ showAmount($widget['total_due']) }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Loan Total Due')</span>
                </div>
                <a href="{{ route('user.loan.active') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--orange b-radius--10 box-shadow ">
            <div class="icon">
                <i class="las la-coins"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $widget['active_savings'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Active Savings')</span>
                </div>
                <a href="{{ route('user.savings.active') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
            <div class="icon">
                <i class="las la-wallet"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="currency-sign">{{__($general->cur_sym)}}</span>
                    <span class="amount">{{ showAmount($widget['payable_savings']) }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Payable Savings')</span>
                </div>

                <a href="{{ route('user.savings.active') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->
</div><!-- row end-->

<div class="card b-radius--10 mt-4">
    <div class="card-header">
        <h5 class="d-inline">@lang('Payment History')</h5>
        <a href="{{ route('user.payment.history') }}" class="btn btn-sm btn--primary box--shadow1 text--small float-right">@lang('View All')</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive--md  table-responsive">
            <table class="table table--light style--two">
                <thead>
                <tr>
                    <th>@lang('S.N.')</th>
                    <th>@lang('Type')</th>
                    <th>@lang('Plan Name')</th>
                    <th>@lang('Paid Through')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Late Fee')</th>
                    <th>@lang('Total Amount')</th>
                    <th>@lang('Date Time')</th>
                </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)     
                        <tr>
                            <td data-label="@lang('S.N')">{{ $loop->iteration }}</td>
                            <td data-label="@lang('Type')">{{ $payment->loan_id ? __('Loan') : __('Savings') }}</td>
                            <td data-label="@lang('Plan Name')">{{ $payment->loan_id ? __($payment->loan->loanPlan->name) : __($payment->savings->savingsPlan->name) }}</td>
                            <td data-label="@lang('Paid Through')"> @if($payment->staff_id) @lang('Staff')  @elseif(!$payment->trx) @lang('Admin') @else @lang('Self') @endif</td>
                            <td data-label="@lang('Amount')">{{ $general->cur_sym }}{{ showAmount($payment->amount) }}</td>                                    
                            <td data-label="@lang('Late Fee')">{{ $general->cur_sym }}{{ showAmount($payment->late_fee) }}</td>                                    
                            <td data-label="@lang('Total Amount')">{{ $general->cur_sym }}{{ showAmount($payment->final_amount) }}</td>                                    
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