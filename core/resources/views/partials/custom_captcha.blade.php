@php
	$captcha = loadCustomCaptcha('46', '100%');
@endphp
@if($captcha)
    <div class="form-group row ">
        <div class="col-sm-12">
            @php echo $captcha @endphp
        </div>
        <div class="col-sm-12 mt-4">
            <label class="form--label-2">@lang('Enter Code')</label>
            <input type="text" name="captcha" placeholder="@lang('Enter above code')" class="form-control form--control" required>
        </div>
    </div>
@endif
