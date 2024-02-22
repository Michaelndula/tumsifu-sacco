@extends('admin.layouts.master')
@php
    $policyPages = getContent('policy_pages.element');
@endphp

@section('content')
<div class="page-wrapper default-version">
    <div class="form-area bg_img" data-background="{{ asset('assets/admin/images/1.jpg') }}">
        <div class="form-wrapper">
            <h4 class="logo-text mb-15">@lang('Welcome to') <strong>{{ __($general->sitename) }}</strong></h4>
            <p>{{ __($pageTitle) }} @lang('to') {{ __($general->sitename) }} @lang('dashboard')</p>
            <form action="{{ route('user.register') }}" method="POST" onsubmit="return submitUserForm();" class="account--form cmn-form mt-30">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="firstname">@lang('Firstname')</label>
                            <input type="text" name="firstname" class="form-control " id="firstname" value="{{ old('firstname') }}" placeholder="@lang('Enter your firstname')" required>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lastname">@lang('Lastname')</label>
                            <input type="text" name="lastname" class="form-control " id="lastname" value="{{ old('lastname') }}" placeholder="@lang('Enter your lastname')" required>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="username">@lang('Username')</label>
                        <input type="text" name="username" class="form-control  checkUser" id="username" value="{{ old('username') }}" placeholder="@lang('Enter your username')" required>
                        <small class="text-danger usernameExist"></small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">@lang('Email')</label>
                        <input type="email" name="email" class="form-control checkUser" id="email" value="{{ old('email') }}" placeholder="@lang('Enter your email')" required>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="country">@lang('Country')</label>
                            <select name="country" class="form-control" id="country" required>
                                @foreach($countries as $key => $country)
                                    <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}"> {{ __($country->country) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobile">@lang('Mobile')</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text mobile-code"></span>
                                    <input type="hidden" name="mobile_code">
                                    <input type="hidden" name="country_code">
                                </div>
                                <input type="number" name="mobile" class="form-control checkUser" id="mobile" value="{{ old('mobile') }}" placeholder="@lang('Enter your mobile')" required>
                                <small class="text-danger mobileExist"></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group hover-input-popup">
                            <label>@lang('Password') <sup class="text--danger">*</sup></label>
                            <input type="password" id="password" name="password" placeholder="@lang('Create your password')" class="form-control" required>
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
                            <label>@lang('Confirm Password') <sup class="text--danger">*</sup></label>
                            <input type="password" name="password_confirmation" placeholder="@lang('Retype your password')" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    @php echo loadReCaptcha() @endphp
                </div>

                @include('partials.custom_captcha')

                @if($general->agree)
                <div class="form-group">
                    <input type="checkbox" id="agree" name="agree">
                    <label for="agree" class="ms-1">@lang('I agree with')
                        @foreach ($policyPages as $policyPage)
                            <a href="{{ route('policy', [$policyPage, slug($policyPage->data_values->title)]) }}" target="_blank">
                                {{ __($policyPage->data_values->title) }}@if(!$loop->last), @endif
                            </a>
                        @endforeach
                    </label>
                </div>
                @endif

                <div class="form-group">
                    <button type="submit" class="submit-btn mt-25">@lang('Register') <i class="las la-sign-in-alt"></i></button>
                </div>
            </form>
            <div class="text-center">
                <span class="text-dark">@lang('Already have an account?')</span> <a href="{{ route('user.login') }}">@lang('Login now.')</a>
            </div>
        </div>
    </div><!-- login-area end -->
</div>

{{-- Exists Modal --}}
<div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>

          </button>
        </div>
        <div class="modal-body">
          <h6 class="text-center">@lang('You already have an account please Sign in ')</h6>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn--danger" data-dismiss="modal">@lang('Close')</button>
          <a href="{{ route('user.login') }}" class="btn btn--primary">@lang('Login')</a>
        </div>
      </div>
    </div>
</div>
@endsection

@push('script-lib')
<script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush

@push('style')
    <style>
        .form-area .form-wrapper {
            width: 630px;
        }
    </style>
@endpush

@push('script')
    <script>
      "use strict";
      function submitUserForm() {
          var response = grecaptcha.getResponse();
          if (response.length == 0) {
              document.getElementById('g-recaptcha-error').innerHTML = '<span class="text-danger">@lang("Captcha field is required.")</span>';
              return false;
          }
          return true;
      }
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
                var url = '{{ route('user.checkUser') }}';
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
