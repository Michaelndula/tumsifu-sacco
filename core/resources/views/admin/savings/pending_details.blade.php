@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('Savings Information')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Savings Plan'):
                            <span class="savings-plan">{{ __($savings->savingsPlan->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Savings Amount'):
                            <span class="savings-amount">{{ $general->cur_sym }}{{ showAmount($savings->savings_amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Receivable Amount'):
                            <span class="total-giveable">{{ $general->cur_sym }}{{ showAmount($savings->giveable_amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Installment Type'):
                            <span class="installment-type">{{ installmentInterval($savings->installment_interval, $days) }} @lang('Payable')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Installment Amount')
                            <span class="installment">{{ $general->cur_sym }}{{ showAmount($savings->installment) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Total Installment')
                            <span class="installment-times">{{ $savings->total_installment }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if ($savings->status == 0)
                                <span class="badge badge--warning">@lang('Pending')</span>
                            @else
                                <span class="badge badge--success">@lang('Approved')</span>
                            @endif
                        </li>
                        <li class="list-group-item">
                            @lang('Description') <hr class="mt-1 mb-2">
                            @php
                                echo $savings->savingsPlan->description;
                            @endphp
                         </li>
                    </ul>
                </div>
            </div>
            <div class="card my-3">
                <div class="card-header">
                    <h5>@lang('User Information')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('User Name'):
                            <span class="user-name">{{ __($savings->user->fullname) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('User Email'):
                            <span class="user-email">{{ $savings->user->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('More About User')
                            <span class="user-email">
                                <a href="{{ route('admin.users.detail', $savings->user->id) }}">@lang('View Details')</a>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            @if ($savings->staff_id)
                <div class="card my-3">
                    <div class="card-header">
                        <h5>@lang('Staff Information')</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Staff Name'):
                                <span class="staff-name">{{ __($savings->staff->fullname) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Staff Email'):
                                <span class="staff-email">{{ $savings->staff->email }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('More About Staff')
                                <span class="staff-email">
                                    <a href="{{ route('admin.staffs.detail', $savings->staff->id) }}">@lang('View Details')</a>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-6">
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5>@lang('User Documentation')</h5>
                </div>
                <div class="card-body">
                    @if($savings->user_information != null)
                        @foreach($savings->user_information as $k => $val)
                                @if($val->type == 'file')
                                    <div class="row mt-4">
                                        <div class="col-md-8">
                                            <h6>{{__(inputTitle($k))}}</h6>
                                            <img src="{{getImage('assets/images/verify/savings/'.$val->field_name)}}" alt="@lang('Image')">
                                        </div>
                                    </div>
                                @else
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <h6>{{__(inputTitle($k))}}</h6>
                                            <p>{{$val->field_name}}</p>
                                        </div>
                                    </div>

                                @endif
                        @endforeach
                    @endif
                    @if ($savings->status == 0)    
                        <div class="form-group mt-3">
                            <button class="btn btn--primary btn-block approve-btn" data-savings_id="{{ $savings->id }}">@lang('Approve')</button>
                        </div>
                    @endif
                </div>
           
            </div>
        </div>
    </div>

    
{{-- APPROVE MODAL --}}
<div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Approve Savings Confirmation')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.savings.approve')}}" method="POST">
                @csrf
                <input type="hidden" name="savings_id">
                <div class="modal-body">
                    <h6 class="mb-3">@lang('Are you sure to approve this savings')?</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.savings.pending') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.approve-btn').on('click', function(){
                var modal = $('#approveModal');
                $('[name=savings_id]').val($(this).data('savings_id'));
                modal.modal('show');
            })
        })(jQuery);
    </script>
@endpush
