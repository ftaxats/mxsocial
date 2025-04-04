@extends('admin.layouts.master')

@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <div class="col-md-12 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                    <div class="form-inner">
                                        <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}"  placeholder='{{translate("Filter by date")}}'>
                                    </div>
                                    <button class="i-btn btn--sm info">
                                        <i class="las la-sliders-h"></i>
                                    </button>
                                    <a href="{{route(Route::currentRouteName())}}"  class="i-btn btn--sm danger">
                                        <i class="las la-sync"></i>
                                    </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-container position-relative">
                @include('admin.partials.loader')

                <table >
                    <thead>
                        <tr>
                            <th scope="col">
                                 #
                            </th>

                            <th scope="col">
                                {{translate('Date')}}
                            </th>


                            <th scope="col">
                                {{translate('Options')}}
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($reports as $report)

                                <tr>
                                    <td data-label="#">
                                        {{$loop->iteration}}
                                    </td>

                                    <td data-label='{{translate("Date")}}'>
                                        {{ get_date_time($report->created_at) }}
                                        <div>
                                            {{ diff_for_humans($report->created_at) }}
                                       </div>
                                    </td>

                                    <td data-label='{{translate("Options")}}'>
                                        <div class="table-action">
                                            @php
                                               $responseData = $report->webhook_response;
                                            @endphp
                                            <a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Info')}}" href="javascript:void(0);" data-report="{{$responseData ? recursiveDisplay($responseData) : translate('Webhook response data is null')}}" class="pointer show-info icon-btn info">
                                                <i class="las la-info"></i></a>


                                            @if(check_permission('delete_report') )
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);" data-href="{{route('admin.webhook.report.destroy',$report->id)}}" class="pointer delete-item icon-btn danger">
                                                <i class="las la-trash-alt"></i></a>
                                            @endif

                                        </div>
                                    </td>
                               </tr>

                            @empty

                                <tr>
                                    <td class="border-bottom-0" colspan="3">
                                        @include('admin.partials.not_found',['custom_message' => "No Reports found!!"])
                                    </td>
                                </tr>

                           @endforelse

                    </tbody>
                </table>
            </div>
            <div class="Paginations">
                {{ $reports->links() }}
            </div>
        </div>
    </div>

@endsection
@section('modal')
    @include('modal.delete_modal')

    <div class="modal fade" id="report-info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="report-info"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Webhook Information')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="content">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script-include')
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
    <script  nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/init.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";

        $(document).on('click','.show-info',function(e){

            e.preventDefault()
            var modal = $('#report-info');
            var report =($(this).attr('data-report'))


            var cleanContent = DOMPurify.sanitize(report);

            $('.content').html(cleanContent)

            modal.modal('show')
        });

	})(jQuery);
</script>
@endpush





