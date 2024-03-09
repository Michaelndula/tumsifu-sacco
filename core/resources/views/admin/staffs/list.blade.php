@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Staff')</th>
                                <th>@lang('Email-Phone')</th>
                                <th>@lang('Country')</th>
                                <th>@lang('Joined At')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($staffs as $staff)
                            <tr>
                                <td data-label="@lang('Staff')">
                                    <span class="font-weight-bold">{{$staff->fullname}}</span>
                                    <br>
                                    <span class="small">
                                    <a href="{{ route('admin.staffs.detail', $staff->id) }}"><span>@</span>{{ $staff->username }}</a>
                                    </span>
                                </td>

                                <td data-label="@lang('Email-Phone')">
                                    {{ $staff->email }}<br>{{ $staff->mobile }}
                                </td>
                                <td data-label="@lang('Country')">
                                    <span class="font-weight-bold" data-toggle="tooltip" data-original-title="{{ @$staff->address->country }}">{{ $staff->country_code }}</span>
                                </td>


                                <td data-label="@lang('Joined At')">
                                    {{ showDateTime($staff->created_at) }} <br> {{ diffForHumans($staff->created_at) }}
                                </td>

                                <td data-label="@lang('Action')">
                                    <a href="{{ route('admin.staffs.detail', $staff->id) }}" class="icon-btn" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                                        <i class="las la-desktop text--shadow"></i>
                                    </a>
                                </td>
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
                @if ($staffs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($staffs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

 
<div id="addModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Staff')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.staffs.register') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname" class="w-100 font-weight-bold">@lang('Firstname')<span class="text-danger">*</span></label>
                                <input type="text" name="firstname" class="form-control " id="firstname" value="{{ old('firstname') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastname" class="w-100 font-weight-bold">@lang('Lastname')<span class="text-danger">*</span></label>
                                <input type="text" name="lastname" class="form-control " id="lastname" value="{{ old('lastname') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username" class="w-100 font-weight-bold">@lang('Username')<span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control  checkUser" id="username" value="{{ old('username') }}" required>
                                <small class="text-danger usernameExist"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="w-100 font-weight-bold">@lang('Email')<span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control checkUser" id="email" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country" class="w-100 font-weight-bold">@lang('Country')<span class="text-danger">*</span></label>
                                <select name="country" class="form-control" id="country" required>
                                    @foreach($countries as $key => $country)
                                        <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}"> {{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="mobile" class="w-100 font-weight-bold">@lang('Mobile')<span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text mobile-code"></span>
                                        <input type="hidden" name="mobile_code">
                                        <input type="hidden" name="country_code">
                                        </div>
                                        <input type="number" name="mobile" class="form-control checkUser" id="mobile" value="{{ old('mobile') }}" required>
                                        <small class="text-danger mobileExist"></small>
                                    </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group hover-input-popup">
                                <label class="w-100 font-weight-bold">@lang('Password') <sup class="text--danger">*</sup></label>
                                <input type="password" id="password" name="password" class="form-control" required>
                                @if($general->secure_password)
                                    <div class="input-popup">
                                    <p class="error lower">@lang('1 small letter minimum')</p>
                                    <p class="error capital">@lang('1 capital letter minimum')</p>
                                    <p class="error number">@lang('1 number minimum')</p>
                                    <p class="error special">@lang('1 special character minimum')</p>
                                    <p class="error minimum">@lang('6 character password')</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="w-100 font-weight-bold">@lang('Confirm Password') <sup class="text--danger">*</sup></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                       
                    </div>
                   
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn--primary btn-block">@lang('Add New')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-sm-end header-search-wrapper">
        <form action="" method="GET" class="form-inline float-sm-right bg--white">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('Username or email')" value="{{ request()->search }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <button class="btn btn--primary box--shadow1 text--small" data-toggle="modal" href="#addModal"><i class="fa fa-fw fa-plus"></i>@lang('Add New')</button>
    </div>
@endpush

@push('style')
    <style>
        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center
        }
        .header-search-wrapper {
            gap: 15px
        }
        @media (max-width:400px) {
            .header-search-form {
                width: 100%
            }
        }
    </style>
@endpush


@push('script')
<script>
    "use strict";
    (function ($) {
        @if($mobile_code)
        $(`option[data-code={{ $mobile_code }}]`).attr('selected','');
        @endif
        $('select[name=country]').change(function(){
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));
        });
        $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
        $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
        $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));
        @if($general->secure_password)
            $('input[name=password]').on('input',function(){
                secure_password($(this));
            });
        @endif
        $('.checkUser').on('focusout',function(e){
            var url = '{{ route('admin.staffs.checkUser') }}';
            var value = $(this).val();
            var token = '{{ csrf_token() }}';
            if ($(this).attr('name') == 'mobile') {
                var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                var data = {mobile:mobile,_token:token}
            }
            if ($(this).attr('name') == 'email') {
                var data = {email:value,_token:token}
            }
            if ($(this).attr('name') == 'username') {
                var data = {username:value,_token:token}
            }
            $.post(url,data,function(response) {
              if (response['data'] && response['type'] == 'email') {
                $('#existModalCenter').modal('show');
              }else if(response['data'] != null){
                $(`.${response['type']}Exist`).text(`${response['type']} already exist`);
              }else{
                $(`.${response['type']}Exist`).text('');
              }
            });
        });
    })(jQuery);

  </script>
@endpush
