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
                                <th>@lang('Date')</th>
                                <th>@lang('Total Amount')</th>
                                <th>@lang('Count')</th>
                                <th>@lang('Receive Admin')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collections as $collection)
                            <tr>
                                <td data-label="@lang('S.N.')">{{ $collections->firstItem() + $loop->index }}</td>
                                <td data-label="@lang('Date')">{{ showDateTime($collection->created_at, 'M-d, Y') }}</td>
                                <td data-label="@lang('Total Amount')">{{ $general->cur_sym }}{{ showAmount($collection->total_amount) }}</td>
                                <td data-label="@lang('Count')">{{ $collection->count }}</td>
                                <td data-label="@lang('Receive Admin')">
                                    @if($collection->admin_receive == 1)
                                        <span class="text--small badge font-weight-normal badge--success">@lang('Yes')</span>
                                    @else
                                        <span class="text--small badge font-weight-normal badge--warning">@lang('No')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    @php $type = $collection->loan_id ? 'loan':'savings'; @endphp
                                    <a href="{{ route("staff.payment.$type.history", [\Carbon\Carbon::parse($collection->created_at)->format('Y-m-d'), $collection->admin_receive]) }}" class="icon-btn ml-1" data-toggle="tooltip" title="" data-original-title="@lang('View All')">
                                        <i class="las la-eye"></i>
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
            @if ($collections->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($collections) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
