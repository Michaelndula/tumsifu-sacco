@extends('user.layouts.master')
@section('content')
    <div class="page-wrapper default-version">
        <div class="form-area bg_img" data-background="{{asset('assets/admin/images/1.jpg')}}">
            <div class="form-wrapper">
                <h4 class="logo-text mb-15">@lang('Welcome to') <strong>{{__($general->sitename)}}</strong></h4>
                <p>{{__($pageTitle)}} @lang('to')  {{__($general->sitename)}} @lang('dashboard')</p>
                <form action="{{ route('user.login') }}" method="POST"  onsubmit="return submitUserForm();" class="account--form cmn-form mt-30">
                    @csrf

                    <div class="form-group">
                        <label for="email">@lang('Username')</label>
                        <input type="text" name="username" class="form-control" id="username" value="{{ old('username') }}" placeholder="@lang('Enter your username')" required>
                    </div>

                    <div class="form-group">
                        <label for="pass">@lang('Password')</label>
                        <input type="password" name="password" class="form-control" id="pass" placeholder="@lang('Enter your password')" required>
                    </div>

                    <div class="form-group">
                        @php echo loadReCaptcha() @endphp
                    </div>

                    @include('partials.custom_captcha')

                    <div class="form-group d-flex justify-content-between align-items-center">
                        <a href="{{ route('user.password.request') }}" class="text-muted text--small">
                            <i class="las la-lock"></i>@lang('Forgot password?')
                        </a>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="submit-btn mt-25">@lang('Login') <i class="las la-sign-in-alt"></i></button>
                    </div>
                    <div class="text-center">
                        <span class="text-dark"> @lang('Don\'t have an account?') </span> <a href="{{ route('user.register') }}">@lang('Register now.')</a>
                     </div>
                </form>
            </div>
        </div><!-- login-area end -->
    </div>
@endsection

@push('script')
    <script>
        "use strict";

        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML =
                    '<span class="text-danger">@lang("Captcha field is required.")</span>';
                return false;
            }
            return true;
        }
    </script>
@endpush


