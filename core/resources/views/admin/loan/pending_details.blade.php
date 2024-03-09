
@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('Loan Information')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Loan Plan'):
                            <span class="loan-plan">{{ __($loan->loanPlan->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Loan Amount'):
                            <span class="loan-amount">{{ $general->cur_sym }}{{ showAmount($loan->loan_amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Payable Amount'):
                            <span class="total-payable">{{ $general->cur_sym }}{{ showAmount($loan->payable_amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Installment Type'):
                            <span class="installment-type">{{ installmentInterval($loan->installment_interval, $days) }} @lang('Payable')</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Installment Amount')
                            <span class="installment">{{ $general->cur_sym }}{{ showAmount($loan->installment) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Total Installment')
                            <span class="installment-times">{{ $loan->total_installment }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Applied Date')
                            <span class="installment-times">{{ showDateTime($loan->created_at) }}</span>

                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if ($loan->status == 0)
                                <span class="badge badge--warning">@lang('Pending')</span>
                            @else
                                <span class="badge badge--success">@lang('Approved')</span>
                            @endif
                        </li>
                        <li class="list-group-item">
                            @lang('Description') <hr class="mt-1 mb-2">
                            @php
                                echo $loan->loanPlan->description;
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
                            <span class="user-name">{{ __($loan->user->fullname) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('User Email'):
                            <span class="user-email">{{ $loan->user->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('More About User')
                            <span class="user-email">
                                <a href="{{ route('admin.users.detail', $loan->user->id) }}">@lang('View Details')</a>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            @if ($loan->staff_id)
                <div class="card">
                    <div class="card-header">
                        <h5>@lang('Staff Information')</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Staff Name'):
                                <span class="staff-name">{{ __($loan->staff->fullname) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Staff Email'):
                                <span class="staff-email">{{ $loan->staff->email }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('More About Staff')
                                <span class="staff-email">
                                    <a href="{{ route('admin.staffs.detail', $loan->staff->id) }}">@lang('View Details')</a>
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
                    @if($loan->user_information != null)
                        @foreach($loan->user_information as $k => $val)
                                @if($val->type == 'file')
                                    <div class="row mt-4">
                                        <div class="col-md-8">
                                            <h6>{{__(inputTitle($k))}}</h6>
                                            <img src="{{getImage('assets/images/verify/loan/'.$val->field_name)}}" alt="@lang('Image')">
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
                    @if ($loan->status == 0)
                        <div class="form-group mt-3">
                            <button class="btn btn--primary btn-block approve-btn" data-loan_id="{{ $loan->id }}">@lang('Approve')</button>
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
                <h5 class="modal-title">@lang('Approve Loan Confirmation')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.loan.approve')}}" method="POST">
                @csrf
                <input type="hidden" name="loan_id">
                <div class="modal-body">
                    <h6 class="mb-3">@lang('Are you sure to approve this loan')?</h6>
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
    <a href="{{ route('admin.loan.pending') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.approve-btn').on('click', function(){
                var modal = $('#approveModal');
                $('[name=loan_id]').val($(this).data('loan_id'));
                modal.modal('show');
            })
        })(jQuery);
    </script>
@endpush
