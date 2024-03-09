@extends('staff.layouts.app')
@section('panel')
<div class="row mt-50 mb-none-30">
    @foreach ($savingsPlans as $plan)     
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card">
                <div class="card-body p-4 p-xxl-5">
                    <div class="pricing-table text-left">
                    <h4 class="package-name mb-20">{{ __($plan->name) }}</h4>
                    <span class="price text--dark font-weight-bold">{{ $general->cur_sym }}{{ showAmount($plan->installment) }}</span>
                    <p>{{ __(installmentInterval($plan->installment_interval, $days)) }}</p>
                    <ul class="list-group list-group-flush my-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            @lang('Savings amount')
                            <span>{{ $general->cur_sym }}{{ showAmount($plan->savings_amount) }}</span>
                          </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            @lang('Receivable amount')
                          <span>{{ $general->cur_sym }}{{ showAmount($plan->giveable_amount) }}</span>
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
                    <a href="{{ route('staff.savings.apply', $plan->id) }}" class="btn w-100 btn--primary py-2 box--shadow1">@lang('Apply Now')</a>
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    @endforeach
</div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('.statusBtn').on('click', function () {
                var modal = $('#statusModal');
                var status = $(this).data('status');
                var name = $(this).data('name');
                modal.find('input[name=plan_id]').val($(this).data('id'));

                if(status == 1){
                    $('.modal-title').text("@lang('Plan Inactivate Confirmation')");
                    $('.message').html(`@lang('Are you sure to inactivate') <span class="font-weight-bold">${name}</span> @lang('plan')?`);
                }else{
                    $('.modal-title').text("@lang('Plan Activate Confirmation')");
                    $('.message').html(`@lang('Are you sure to activate') <span class="font-weight-bold">${name}</span> @lang('plan')?`);
                }
            });

        })(jQuery);
    </script>
@endpush
