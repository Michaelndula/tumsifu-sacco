@extends('staff.layouts.app')
@section('panel')
<div class="row mt-50 mb-none-30">
    @foreach ($loanPlans as $plan)     
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card">
                <div class="card-body p-4 p-xxl-5">
                    <div class="pricing-table text-left">
                    <h4 class="package-name mb-20">{{ __($plan->name) }}</h4>
                    <span class="price text--dark font-weight-bold">{{ $general->cur_sym }}{{ showAmount($plan->installment) }}</span>
                    <p>{{ __(installmentInterval($plan->installment_interval, $days)) }}</p>
                    <ul class="list-group list-group-flush my-4">
                      <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        @lang('Loan amount')
                        <span>{{ $general->cur_sym }}{{ showAmount($plan->loan_amount) }}</span>
                      </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            @lang('Payable amount')
                          <span>{{ $general->cur_sym }}{{ showAmount($plan->payable_amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                          @lang('Total Installment')
                          <span>{{ $plan->total_installment }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                          @lang('Late Fee')
                          <span>{{ $general->cur_sym }}{{ showAmount($plan->fixed_late_fee + ($plan->installment * $plan->percent_late_fee / 100)) }}</span>
                        </li>
                      </ul>
                    <a href="{{ route('staff.loan.apply', $plan->id) }}" class="btn w-100 btn--primary py-2 box--shadow1">@lang('Apply Now')</a>
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    @endforeach
</div>
@endsection