@extends('staff.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Username')</th>
                                <th>@lang('Savings Plan')</th>
                                @if (request()->routeIs('staff.savings.pending'))
                                    <th>@lang('Applied Date')</th>
                                @else
                                    <th>@lang('Savings Amount')</th>
                                @endif
                                <th>@lang('Installment')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($savingsList as $savings)
                            <tr>
                                <td data-label="@lang('S.N.')">{{ $savingsList->firstItem() + $loop->index }}</td>
                                <td data-label="@lang('Username')">{{ __($savings->user->fullname) }}<br>
                                    @<span>{{ $savings->user->username }}</span>
                                </td>
                                <td data-label="@lang('Savings Plan')">{{ __($savings->savingsPlan->name) }}</td>
                                @if (request()->routeIs('staff.savings.pending'))
                                    <td data-label="@lang('Applied Date')">{{ showDateTime($savings->created_at) }}</td>
                                @else 
                                    <td data-label="@lang('Savings Amount')">{{ $general->cur_sym }}{{ showAmount($savings->savings_amount) }}</td>
                                @endif
                                <td data-label="@lang('Installment')">{{ $general->cur_sym }}{{ showAmount($savings->installment) }}</td>
                                <td data-label="@lang('Status')">
                                    @if($savings->status == 0)
                                        <span class="text--small badge font-weight-normal badge--warning">@lang('Pending')</span>
                                    @elseif($savings->status == 1)
                                        <span class="text--small badge font-weight-normal badge--primary">@lang('Active')</span>
                                    @elseif($savings->status == 2)
                                        <span class="text--small badge font-weight-normal badge--success">@lang('Paid')</span>
                                    @else
                                        <span class="text--small badge font-weight-normal badge--danger">@lang('Close')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                         
                                    <button class="icon-btn  ml-1 details-btn" data-toggle="tooltip" title="" data-original-title="@lang('Details')"
                                        data-plan_name = "{{ __($savings->savingsPlan->name) }}"
                                        data-interval="{{ installmentInterval($savings->installment_interval, $days) }}@lang(' Payable')"
                                        data-savings_amount = "{{ getAmount($savings->savings_amount) }}"
                                        data-giveable_amount = "{{ getAmount($savings->giveable_amount) }}"
                                        data-total_paid="{{  getAmount($savings->total_paid) }}"
                                        data-installment = "{{ getAmount($savings->installment) }}"
                                        data-late_fee = "{{ $savings->next_installment < now()->format('Y-m-d') ? getAmount($savings->late_fee) : 0 }}"
                                        data-installment_remaining="{{ $savings->total_installment - $savings->installment_given }}"
                                        data-installment_given="{{ $savings->installment_given }}"
                                        data-last_installment="{{ $savings->last_installment ? showDateTime($savings->last_installment, 'M-d, y') : '-' }}"
                                        data-next_installment="{{ $savings->status == 1 ? showDateTime($savings->next_installment, 'M-d, y') : '-' }}"
                                        >
                                        <i class="las la-desktop"></i>
                                    </button>
                                    <button class="icon-btn btn--success installment-btn" data-toggle="tooltip" title="" data-original-title="@lang('Take Installment')"
                                            data-savings_id="{{ $savings->id }}"        
                                            data-user_email="{{ __($savings->user->email) }}"
                                            data-amount="{{ getAmount($savings->installment) }}"
                                            data-late_fee = "{{ $savings->next_installment < now()->format('Y-m-d') ? getAmount($savings->late_fee) : 0 }}"
                                            {{ $savings->status != 1 ? 'disabled':'' }}
                                        >
                                        <i class="las la-comment-dollar"></i>
                                    </button>
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
                @if ($savingsList->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($savingsList) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

{{--SAVINGS DETAILS MODAL --}}
<div id="savingsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Savings Details')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Savings Plan'):
                        <span class="savings-plan"></span>
                    </li>                  
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Savings Amount'):
                        <span class="savings-amount"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Total Receivable'):
                        <span class="total-receivable"></span>
                    </li>                  
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Total Paid')
                        <span class="total-paid"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Remaining Payable')
                        <span class="remaining-payble"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Installment Interval'):
                        <span class="installment-interval"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Installment Given')
                        <span class="installment-given"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Installment Remaining')
                        <span class="installment-remaining"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Installment')
                        <span class="installment"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Late Fee')
                        <span class="late-fee"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Installment payable')
                        <span class="installment-payable"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Last Installment')
                        <span class="last-installment"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Next Installment')
                        <span class="next-installment"></span>
                    </li>
                    
                </ul>
            </div>
            <div class="modal-footer">
                <button class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

{{-- CONFIRM INSTALLMENT MODAL --}}
<div id="installmentModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Savings Installment Taken Confirmation')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('staff.savings.installment')}}" method="POST">
                @csrf
                <input type="hidden" name="savings_id">
                <div class="modal-body">
                    <h6 class="mb-3">@lang('Is this installment taken')?</h6>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('User Email')
                            <span class="user-email"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Installment')
                            <span class="installment"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Late Fee')
                            <span class="late-fee"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Installment Payable')
                            <span class="installment-payable"></span>
                        </li>    
                    </ul>
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

<div class="d-flex flex-wrap justify-content-sm-end header-search-wrapper">
    <form action="" method="GET" class="header-search-form">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control bg-white text--black" placeholder="@lang('Username or Plan')" value="{{ request()->search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
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
        (function($) {
            var curSym = `{{ $general->cur_sym }}`;
            $('.details-btn').on('click', function(){
                var modal = $('#savingsModal');
                var data = $(this).data();
                var installment = parseFloat(data.installment);
                var lateFee = parseFloat(data.late_fee);

                modal.find('.savings-plan').text(data.plan_name);
                modal.find('.savings-amount').text(curSym+data.savings_amount);
                modal.find('.total-receivable').text(curSym+data.giveable_amount);
                modal.find('.total-paid').text(curSym+data.total_paid);
                modal.find('.installment-interval').text(data.interval);
                modal.find('.remaining-payble').text(curSym+(data.savings_amount-data.total_paid).toFixed(2));
                modal.find('.installment-remaining').text(data.installment_remaining);
                modal.find('.installment').text(curSym+installment);
                modal.find('.late-fee').text(curSym+lateFee.toFixed(2));
                modal.find('.installment-payable').text(curSym+(installment + lateFee).toFixed(2));
                modal.find('.installment-given').text(data.installment_given);
                modal.find('.last-installment').text(data.last_installment);
                modal.find('.next-installment').text(data.installment_remaining ? data.next_installment : '-');
                
                modal.modal('show');
            });

            $(document).on('click','.installment-btn',function () {
                var modal = $('#installmentModal');
                var data = $(this).data();
                var amount = parseFloat(data.amount);
                var lateFee = parseFloat(data.late_fee);

                modal.find('[name=savings_id]').val(data.savings_id);
                modal.find('.user-email').text(data.user_email);
                modal.find('.installment').text(curSym+amount);
                modal.find('.late-fee').text(curSym+lateFee.toFixed(2));
                modal.find('.installment-payable').text(curSym+(amount+lateFee).toFixed(2));
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush