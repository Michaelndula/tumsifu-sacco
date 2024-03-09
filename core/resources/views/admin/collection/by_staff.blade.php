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
                            <th>@lang('Date')</th>
                            <th>@lang('Staff Name')</th>
                            <th>@lang('Total Amount')</th>
                            <th>@lang('Count')</th>
                            <th>@lang('Receive Admin')</th>
                            @if (request()->routeIs('admin.staff.collection.loan.pending') || request()->routeIs('admin.staff.collection.savings.pending'))
                                <th>@lang('Action')</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($collections as $collection)
                        @php $type = $collection->loan_id ? 'loan' : 'savings'; @endphp
                        <tr>
                            <td data-label="@lang('S.N.')">{{ $collections->firstItem() + $loop->index }}</td>
                            <td data-label="@lang('Date')">{{ showDateTime($collection->created_at, 'M-d, Y') }}</td>
                            <td data-label="@lang('Staff Name')"><a href="{{ route('admin.staffs.detail', $collection->staff->id) }}">{{ __($collection->staff->fullname) }}</a></td>
                            <td data-label="@lang('Total Amount')">{{ $general->cur_sym }}{{ showAmount($collection->total_amount) }}</td>
                            <td data-label="@lang('Count')"><a href="{{ route("admin.collection.$type", [$collection->created_at->format('Y-m-d'), $collection->staff_id, $collection->admin_receive]) }}" class="icon-btn">{{ $collection->count }}</a></td>
                            <td data-label="@lang('Receive Admin')">
                                @if($collection->admin_receive == 1)
                                    <span class="text--small badge font-weight-normal badge--success">@lang('Yes')</span>
                                @else
                                    <span class="text--small badge font-weight-normal badge--warning">@lang('No')</span>
                                @endif
                            </td>
                            @if (request()->routeIs('admin.staff.collection.loan.pending') || request()->routeIs('admin.staff.collection.savings.pending'))
                            <td data-label="@lang('Action')">
                                <button class="icon-btn btn--success ml-1 confirm-btn" data-toggle="tooltip" title="" data-original-title="@lang('Confirmation')" {{ $collection->admin_receive ? 'disabled':'' }}
                                    data-amount="{{ showAmount($collection->total_amount) }}"
                                    data-date="{{ $collection->created_at }}"
                                    data-staff_id="{{ $collection->staff_id }}"
                                    data-loan_id="{{ $collection->loan_id }}"
                                    data-savings_id="{{ $collection->savings_id }}"
                                    >
                                    <i class="las la-check"></i>
                                </button>
                            </td>
                            @endif
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

{{-- CONFIRM INSTALLMENT MODAL --}}
<div id="confirmModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Staff Collection Receive Confirmation')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.collection.confirm')}}" method="POST">
                @csrf
                <input type="hidden" name="staff_id">
                <input type="hidden" name="date">
                <input type="hidden" name="loan_id">
                <input type="hidden" name="savings_id">
                <div class="modal-body">
                    <h6 class="mb-3">@lang('Are you sure to receive ')<span class="amount"></span>?</h6>
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
    <form action="" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Staff')" value="{{ request()->search }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
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
            $(document).on('click','.confirm-btn',function () {
                var modal = $('#confirmModal'); 
                var data = $(this).data();
                modal.find('.amount').text(curSym+$(this).data('amount'));
                modal.find('[name=staff_id]').val(data.staff_id);
                modal.find('[name=date]').val(data.date);
                modal.find('[name=loan_id]').val(data.loan_id);
                modal.find('[name=savings_id]').val(data.savings_id);
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
