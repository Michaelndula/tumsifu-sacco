@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.savings.save') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row mb-none-15">
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Username or Email') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control " name="user" value="{{ old('user') }}" required/>
                                    <span class="text--danger email-check">@lang('No user found')</span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('User Name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control user_info user_name" readonly/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('User Mobile') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control user_info user_mobile" readonly/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('User Address') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control user_info user_address" readonly/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('User City') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control user_info user_city" readonly/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('User Country') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control user_info user_country" readonly/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Select Savings Plan') <span class="text-danger">*</span></label>
                                    <select name="plan_id" class="form-control" required>
                                        <option value="" hidden>@lang('Select One')</option>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}" data-plan="{{ $plan }}" data-installment_interval="{{ __(installmentInterval($plan->installment_interval, $days)) }}@lang(' payable')">{{ __($plan->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Savings Amount') <span class="text-danger">*</span></label>
                                    <div class="input-group has_append">
                                        <input type="text" class="form-control savings_amount" readonly/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Giveable Amount') <span class="text-danger">*</span></label>
                                    <div class="input-group has_append">
                                        <input type="text" class="form-control giveable_amount" readonly/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Installment Interval') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control installment_interval" readonly/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Total Installment') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control total_installment" readonly/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Installment') <span class="text-danger">*</span></label>
                                    <div class="input-group has_append">
                                        <input type="text" class="form-control installment" readonly/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('admin.savings.all') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush


@push('script')
    <script>
        (function ($) {
            "use strict";

            $('.email-check').hide();

            $('[name=user]').on('change', function(e){
                var user = e.target.value;
                var url = `{{ route('admin.user.check') }}`;

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {'_token': "{{ csrf_token() }}", 'user': user},
                    success: function (response) {
                        if(!response){
                            $('.email-check').show();
                            $('.user_info').val('');
                            $('[type=submit]').attr('disabled', true);
                        }else{
                            $('.email-check').hide();
                            $('.user_name').val(response.firstname+' '+response.lastname);
                            $('.user_mobile').val(response.mobile);
                            $('.user_address').val(response.address.address);
                            $('.user_city').val(response.address.city);
                            $('.user_country').val(response.address.country);
                            $('[type=submit]').attr('disabled', false);
                        }
                    }
                });

            });

            $('[name=plan_id]').on('change', function(e){
                var plan = $(this).find(':selected').data().plan;
                var savingsAmount = parseInt(plan.savings_amount).toFixed(2);
                $('.savings_amount').val(savingsAmount);
                $('.giveable_amount').val(parseInt(plan.giveable_amount).toFixed(2));
                $('.total_installment').val(plan.total_installment);
                $('.installment').val((savingsAmount / plan.total_installment).toFixed(2));
                $('.installment_interval').val($(this).find(':selected').data('installment_interval'));

            });
        })(jQuery);
    </script>
@endpush
