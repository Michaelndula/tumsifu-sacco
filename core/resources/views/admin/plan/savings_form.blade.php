@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.plan.savings.save', $plan->id??0) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row mb-none-15">
                            <div class="col-lg-12 col-xl-4">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ $plan->name??old('name') }}" required/>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xl-4 col-lg-6">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold receive-times">@lang('Per Installment')</label>
                                    <div class="input-group has_append">
                                        <input type="number" step="any" name="per_installment" value="{{ @$plan ? $plan->installment : old('per_installment')  }}" class="form-control"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xl-4 col-lg-6">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Total Installment') <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" name="total_installment" value="{{ @$plan ? getAmount($plan->total_installment) : old('total_installment') }}" required/>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xl-4 col-lg-6">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Installment Interval') <span class="text-danger">*</span></label>
                                    <select name="installment_interval" class="form-control" required>
                                        <option value="" hidden>@lang('Select One')</option>
                                        @foreach ($days as $day)
                                            <option value="{{ $day->day }}">{{ __($day->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xl-4 col-lg-6">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Savings Amount') <span class="text-danger">*</span></label>
                                    <div class="input-group has_append">
                                        <input type="number" step="any" class="form-control savings-amount" value="{{ @$plan ? getAmount($plan->savings_amount) : old('savings_amount') }}" readonly/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xl-4 col-lg-6">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Giveable Amount') <span class="text-danger">*</span></label>
                                    <div class="input-group has_append">
                                        <input type="number" step="any" class="form-control" name="giveable_amount" value="{{ @$plan ? getAmount($plan->giveable_amount) : old('giveable_amount') }}" required/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xl-4 col-lg-6">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Fixed Late Fee')</label>
                                    <div class="input-group has_append">
                                        <input type="number" step="any" class="form-control" name="fixed_late_fee" value="{{ @$plan ? getAmount($plan->fixed_late_fee) : old('fixed_late_fee') }}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ $general->cur_text }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xl-4 col-lg-6">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Percent Late Fee')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" name="percent_late_fee" value="{{ @$plan ? getAmount($plan->percent_late_fee) : old('percent_late_fee') }}"/>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xl-4 col-lg-6">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('Profit')</label>
                                    <div class="input-group has_append">
                                        <input type="number" step="any" class="form-control profit" readonly/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 my-3">
                                <div class="card border--dark">
                                    <h5 class="card-header bg--dark">@lang('User data')
                                        <button type="button" class="btn btn-sm btn-outline-light float-right addUserData">
                                            <i class="la la-fw la-plus"></i>@lang('Add New')
                                        </button>
                                    </h5>
                                    <div class="card-body">
                                        <div class="row addedField">
                                            @if(@$plan && $plan->user_data != null)
                                                @foreach($plan->user_data as $k => $v)
                                                    <div class="col-md-12 user-data">
                                                        <div class="form-group">
                                                            <div class="input-group mb-md-0 mb-4">
                                                                <div class="col-md-4">
                                                                    <input name="field_name[]" class="form-control" type="text" value="{{$v->field_level}}" required>
                                                                </div>
                                                                <div class="col-md-3 mt-md-0 mt-2">
                                                                    <select name="type[]" class="form-control">
                                                                        <option value="text" @if($v->type == 'text') selected @endif>
                                                                            @lang('Input Text')
                                                                        </option>
                                                                        <option value="textarea" @if($v->type == 'textarea') selected @endif>
                                                                            @lang('Textarea')
                                                                        </option>
                                                                        <option value="file" @if($v->type == 'file') selected @endif>
                                                                            @lang('File')
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3 mt-md-0 mt-2">
                                                                    <select name="validation[]" class="form-control">
                                                                        <option value="required" @if($v->validation == 'required') selected @endif> @lang('Required') </option>
                                                                        <option value="nullable" @if($v->validation == 'nullable') selected @endif>  @lang('Optional') </option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2 mt-md-0 mt-2 text-right">
                                                                    <span class="input-group-btn">
                                                                        <button class="btn btn--danger btn-lg removeBtn w-100" type="button">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="font-weight-bold">@lang('Description')</label>
                            <textarea rows="8" class="form-control border-radius-5 nicEdit" name="description">{{ @$plan ? $plan->description : old('description') }}</textarea>
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
    <a href="{{ route('admin.plan.savings.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('[name=installment_interval]').val({{ old('installment_interval') ?? @$plan->installment_interval }})

            receivableCal();

            $('[name=per_installment], [name=giveable_amount], [name=total_installment]').on('input', function(e){
                receivableCal();
            });

            function receivableCal()
            {
                var perInstallment = parseInt($('[name=per_installment]').val());
                var totalInstallment = parseInt($('[name=total_installment]').val());
                var giveableAmount = parseInt($('[name=giveable_amount]').val());
                var savingsAmount = perInstallment*totalInstallment;

                $('.savings-amount').val(savingsAmount.toFixed(2));
                $('.profit').val(giveableAmount-savingsAmount);
            }

            $('.addUserData').on('click', function () {
                var html = `
                    <div class="col-md-12 user-data">
                        <div class="form-group">
                            <div class="input-group mb-md-0 mb-4">
                                <div class="col-md-4">
                                    <input name="field_name[]" class="form-control" type="text" required placeholder="@lang('Field Name')">
                                </div>
                                <div class="col-md-3 mt-md-0 mt-2">
                                    <select name="type[]" class="form-control">
                                        <option value="text" > @lang('Input Text') </option>
                                        <option value="textarea" > @lang('Textarea') </option>
                                        <option value="file"> @lang('File') </option>
                                    </select>
                                </div>
                                <div class="col-md-3 mt-md-0 mt-2">
                                    <select name="validation[]"
                                            class="form-control">
                                        <option value="required"> @lang('Required') </option>
                                        <option value="nullable">  @lang('Optional') </option>
                                    </select>
                                </div>
                                <div class="col-md-2 mt-md-0 mt-2 text-right">
                                    <span class="input-group-btn">
                                        <button class="btn btn--danger btn-lg removeBtn w-100" type="button">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>`;

                $('.addedField').append(html);
            });

            $(document).on('click', '.removeBtn', function () {
                $(this).closest('.user-data').remove();
            });

        })(jQuery);
    </script>
@endpush


