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
                            <th>@lang('S.N.')</th>
                            <th>@lang('User Name')</th>
                            @if (request()->routeIs('admin.collection.all'))
                                <th>@lang('Type')</th>
                            @endif
                            <th>@lang('Plan')</th>
                            <th>@lang('Collected By')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($collections as $collection)
                        <tr>
                            <td data-label="@lang('S.N.')">{{ $collections->firstItem() + $loop->index }}</td>
                            <td data-label="@lang('Username')"><a href="{{ route('admin.users.detail',$collection->user->id) }}">{{ __($collection->user->username) }}</a></td>
                            @if (request()->routeIs('admin.collection.all'))
                                <td data-label="@lang('Type')">
                                    @if ($collection->loan_id)
                                        @lang('Loan')
                                    @else
                                        @lang('Savings')
                                    @endif
                                </td>
                            @endif
                            <td data-label="@lang('Plan')">{{ $collection->loan_id ? __($collection->loan->loanPlan->name) : __($collection->savings->savingsPlan->name) }}</td>
                            <td data-label="@lang('Collected By')">
                                @if ($collection->staff_id)
                                    <a href="{{ route('admin.staffs.detail', $collection->staff->id) }}">{{ __($collection->staff->username) }}</a>
                                @elseif(!$collection->trx)
                                    @lang('Admin')
                                @else
                                    @lang('Self')
                                @endif
                            </td>
                            <td data-label="@lang('Amount')">{{ $general->cur_sym }}{{ showAmount($collection->amount) }}</td>
                            <td data-label="@lang('Date')">{{ showDateTime($collection->created_at, 'M d, Y - h:i a') }}</td>
                            <td data-label="@lang('Action')">
                                <button class="icon-btn  ml-1 details-btn" data-toggle="tooltip" title="" data-original-title="@lang('Details')"
                                    data-user_name="{{ __($collection->user->fullname) }}"
                                    data-user_email="{{ $collection->user->email }}"
                                    data-plan_name = "{{ $collection->loan_id ? __($collection->loan->loanPlan->name) : __($collection->savings->savingsPlan->name) }}"
                                    data-interval="{{ $collection->loan_id ? installmentInterval($collection->loan->installment_interval, $days)  : installmentInterval($collection->savings->installment_interval, $days) }}@lang(' Payable')"
                                    data-amount = "{{ showAmount($collection->amount) }}"
                                    data-late_fee="{{ showAmount($collection->late_fee) }}"
                                    data-final_amount={{ showAmount($collection->final_amount) }}
                                    >
                                    <i class="las la-desktop"></i>
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
            @if ($collections->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($collections) }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- COLLECTIONS DETAILS MODAL  --}}
<div id="collectionModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Collection Details')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('User Name'):
                        <span class="user-name"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('User Email'):
                        <span class="user-email"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Plan Name'):
                        <span class="plan-name"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Type'):
                        <span class="loan-type"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Amount'):
                        <span class="amount"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Late Fee'):
                        <span class="late-fee"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Total Amount'):
                        <span class="total-amount"></span>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <form action="" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Username or Staff')" value="{{ request()->search }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush


@push('script')
    <script>
        "use strict";
        (function($) {
            var curSym = `{{ $general->cur_sym }}`;
            $('.details-btn').on('click', function(){
                var modal = $('#collectionModal');
                var data = $(this).data();

                modal.find('.user-name').text(data.user_name);
                modal.find('.user-email').text(data.user_email);
                modal.find('.loan-type').text(data.interval)
                modal.find('.plan-name').text(data.plan_name);
                modal.find('.amount').text(curSym+data.amount);
                modal.find('.late-fee').text(curSym+data.late_fee);
                modal.find('.total-amount').text(curSym+data.final_amount);

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
