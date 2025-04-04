@extends('layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div>
        <div class="w-100 d-flex align-items-center justify-content-between gap-lg-5 gap-3 flex-md-nowrap flex-wrap mb-4">
            <div>
                <h4>
                    {{translate(Arr::get($meta_data,'title'))}}
                </h4>
            </div>

            <div>
                <button
                    class="icon-btn icon-btn-lg solid-info circle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#tableFilter"
                    aria-expanded="false"
                    aria-controls="tableFilter">
                    <i class="bi bi-sliders"></i>
                </button>
            </div>
        </div>

        <div class="collapse {{ hasFilter(['date','trx_type','search']) ? 'show' : '' }} filterTwo mb-3" id="tableFilter">
            <div class="i-card-md">
                <div class="card-body">
                    <div class="search-action-area p-0">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}">
                                <div class="form-inner">
                                    <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}"  placeholder='{{translate("Filter by date")}}'>
                                </div>

                                <div class="form-inner">
                                    <select name="trx_type" id="type" class="type">
                                        <option value="">
                                            {{translate('Select TRX Type')}}
                                        </option>
                                        <option {{ App\Models\Transaction::$PLUS == request()->input('trx_type') ? 'selected' :""  }} value="{{App\Models\Transaction::$PLUS}}">{{translate("Plus")}}</option>
                                        <option {{ App\Models\Transaction::$MINUS == request()->input('trx_type') ? 'selected' :""  }} value="{{App\Models\Transaction::$MINUS}}">{{translate("Minus")}}</option>

                                    </select>
                                </div>

                                <div class="form-inner">
                                    <input type="text"  name="search" value="{{request()->input('search')}}"  placeholder='{{translate("Search by Transaction ID or remarks")}}'>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="i-btn primary btn--lg capsuled">
                                        <i class="bi bi-search"></i>
                                    </button>

                                    <a href="{{route(Route::currentRouteName())}}"  class="i-btn btn--lg danger capsuled">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="i-card-md">
            <div class="card-body p-0">
                <div class="table-accordion">
                    @if($reports->count() > 0)
                        <div class="accordion" id="wordReports">
                            @forelse($reports as $report)
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <div class="accordion-button collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$report->id}}"
                                            aria-expanded="false" aria-controls="collapse{{$report->id}}">
                                            <div class="row align-items-center w-100 gy-4 gx-sm-3 gx-0">
                                                <div class="col-lg-3 col-sm-4 col-12">
                                                    <div class="table-accordion-header transfer-by">
                                                        <span class="icon-btn icon-btn-sm primary circle">
                                                            <i class="bi bi-file-text"></i>
                                                        </span>
                                                        <div>
                                                            <h6>
                                                                {{translate("TRX Code")}}
                                                            </h6>
                                                            <p> {{$report->trx_code}}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-sm-4 col-6 text-lg-center text-sm-center text-start">
                                                    <div class="table-accordion-header">
                                                        <h6>
                                                            {{translate("Date")}}
                                                        </h6>
                                                        <p>
                                                            {{ get_date_time($report->created_at) }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-sm-end text-end">
                                                    <div class="table-accordion-header">
                                                        <h6>
                                                            {{translate("Balance")}}
                                                        </h6>

                                                        <p class='text--{{$report->trx_type == App\Models\Transaction::$PLUS ? "success" :"danger" }}'>
                                                            <i class='bi bi-{{$report->trx_type == App\Models\Transaction::$PLUS ? "plus" :"dash" }}'></i>
                                                            {{num_format($report->amount,$report->currency)}}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-start">
                                                    <div class="table-accordion-header">
                                                        <h6>
                                                            {{translate("Post Balance")}}
                                                        </h6>

                                                        <p>
                                                            {{@num_format(
                                                                number : $report->post_balance??0,
                                                                calC   : true
                                                            )}}
                                                        </p>

                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-lg-end text-md-center text-end">
                                                    <div class="table-accordion-header">
                                                        <h6>
                                                            {{translate("Remark")}}
                                                        </h6>
                                                        <p>
                                                            {{k2t($report->remarks)}}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="collapse{{$report->id}}" class="accordion-collapse collapse" data-bs-parent="#wordReports">
                                        <div class="accordion-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <h6 class="title">
                                                        {{translate("Report Information")}}
                                                    </h6>
                                                    <p class="value">
                                                        {{$report->details}}
                                                    </p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    @else
                        @include('admin.partials.not_found',['custom_message' => "No Reports found!!"])
                    @endif

                </div>
            </div>
        </div>

        <div class="Paginations">
            {{ $reports->links() }}
        </div>
    </div>

@endsection


@push('script-include')
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/init.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
        "use strict";
        $(".type").select2({});
	})(jQuery);
</script>
@endpush





