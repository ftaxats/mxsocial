@extends('layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
<div>
      <div
        class="w-100 d-flex align-items-center justify-content-between gap-lg-5 gap-3 flex-md-nowrap flex-wrap mb-4">
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

      <div class="collapse {{ hasFilter(['date']) ? 'show' : '' }} filterTwo mb-3" id="tableFilter">
        <div class="i-card-md">
          <div class="card-body">
            <div class="search-action-area p-0">
              <div class="search-area">
                <form action="{{route(Route::currentRouteName())}}">

                    <div class="form-inner">
                        <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}"  placeholder='{{translate("Filter by date")}}'>
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
                                                    <span class="icon-btn icon-btn-sm info circle">
                                                        <i class="bi bi-arrow-up-left"></i>
                                                    </span>
                                                    <div>
                                                        <h6>
                                                            {{translate("Date")}}
                                                        </h6>
                                                        <p> {{ get_date_time($report->created_at) }} </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="collapse{{$report->id}}" class="accordion-collapse collapse" data-bs-parent="#wordReports">
                                    <div class="accordion-body">
                                        @php
                                            $responseData = $report->webhook_response;
                                        @endphp
                                        @if ($responseData)
                                            <div class="p-2">
                                                @php  recursiveDisplay($responseData) @endphp
                                            </div>
                                        @else
                                            <p>
                                                {{translate('Webhook response data is null')}}
                                            </p>
                                        @endif
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





