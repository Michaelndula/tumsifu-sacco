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
                                <th>@lang('Name')</th>
                                <th>@lang('Day')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($timeIntervals as $timeInterval)
                                <tr>
                                    <td data-label="@lang('S.N.')">{{ $loop->iteration }}</td>
                                    <td data-label="@lang('Name')">{{ __($timeInterval->name) }}</td>
                                    <td data-label="@lang('Day')">{{ __($timeInterval->day) }} @lang('Days')</td>
                                    <td data-label="@lang('Action')">
                                        <button class="icon-btn cuModalBtn edit-btn" data-modal_title="@lang('Update Time Interval')" data-id="{{ $timeInterval->id }}" data-name="{{ $timeInterval->name }}" data-day="{{ $timeInterval->day }}">
                                            <i class="la la-pen"></i>
                                        </button>
                                        <button data-toggle="modal" class="icon-btn bg--danger removeBtn" data-bs-toggle="modal" data-target="#removeModal" data-id="{{ $timeInterval->id }}">
                                            <i class="la la-trash"></i>
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
        </div>
    </div>
</div>

    <div class="modal fade" id="cuModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('admin.time.intervals.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Day')</label>
                            <div class="input-group">
                                <input type="text" name="day" class="form-control" id="day" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">@lang('Day')</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="removeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Delete Time Interval')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.time.intervals.delete')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <h6>@lang('Are you sure to delete this time interval?')</h6>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn--dark">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <button class="btn btn--primary btn-sm cuModalBtn add-btn" data-modal_title="@lang('Add New Time Interval')">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script')
<script>
    (function($){
        "use strict";
        $('.removeBtn').on('click',function () {
            var modal = $('#removeModal');
            modal.find('input[name=id]').val($(this).data('id'));
        });

        $('.add-btn').on('click', function ()
        {
            var modal   = $('#cuModal');
            var route= '{{ route('admin.time.intervals.store') }}';
            $('#cuModal form').attr('action', route)
            $('#cuModal .modal-title').text($(this).data('modal_title'))
            $('#cuModal #size').val('')
            modal.modal('show');
        });

        $('.edit-btn').on('click', function (e)
        {
            var modal   = $('#cuModal');
            var data = $(this).data();

            $('#cuModal #name').val(data.name)
            $('#cuModal #day').val(data.day)
            var route= '{{ route('admin.time.intervals.store', '') }}' + '/' + data.id;
            $('#cuModal form').attr('action', route)
            $('#cuModal .modal-title').text(data.modal_title)
            modal.modal('show');
        });

    })(jQuery);
</script>
@endpush
