@extends('staff.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('S.N.')</th>
                            <th>@lang('User Name')</th>
                            @if (request()->routeIs('staff.payment.history'))
                                <th>@lang('Type')</th>
                            @endif
                            <th>@lang('Plan Name')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Late Fee')</th>
                            <th>@lang('Total Amount')</th>
                            <th>@lang('Date Time')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)     
                                <tr>
                                    <td data-label="@lang('S.N')">{{ $loop->index + $payments->firstItem() }}</td>
                                    <td data-label="@lang('User Name')">{{ __($payment->user->fullname) }} <br>
                                        @<span>{{ $payment->user->username }}</span>
                                    </td>
                                    @if (request()->routeIs('staff.payment.history'))
                                    <td data-label="@lang('Type')">{{ $payment->loan_id ? __('Loan') : __('Savings') }}</td>
                                    @endif
                                    <td data-label="@lang('Plan Name')">{{ $payment->loan_id ? __($payment->loan->loanPlan->name) : __($payment->savings->savingsPlan->name) }}</td>
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
            @if ($payments->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($payments) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection