@extends('layouts.master')

@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')


<div class="row">

    <div class="col-xl-12 mx-auto">
      <div class="w-100 d-flex align-items-center justify-content-between gap-lg-5 gap-3 flex-md-nowrap flex-wrap mb-4">
            <h4>
                {{translate(Arr::get($meta_data,'title'))}}
            </h4>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    @if( 0 < $genarated_words)
                        <h6> {{translate("Total Words")}}
                            <span class="ms-2 i-badge capsuled danger">{{truncate_price($genarated_words,0)}}</span>
                        </h6>
                    @endif
                </div>
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



       <div class="collapse {{ hasFilter(['date','template']) ? 'show' : '' }}  filterTwo mb-3" id="tableFilter">
            <div class="search-action-area">
                <div class="search-area">
                    <form action="{{route(Route::currentRouteName())}}">

                        <div class="form-inner">
                            <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}"  placeholder='{{translate("Filter by date")}}'>
                        </div>

                        <div class="form-inner">
                            <select name="template" id="template" class="select2">
                                <option value="">
                                    {{translate('Select Template')}}
                                </option>
                                @foreach($templates as $template)
                                    <option  {{$template->slug ==   request()->input('template') ? 'selected' :""}} value="{{$template->slug}}"> {{$template->name}}
                                    </option>
                                @endforeach
                            </select>
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

      <div class="i-card-md">
            <div class="card-body p-0">
                <div class="table-accordion">
                  @if($reports->count() > 0)
                      <div class="accordion" id="wordReports">
                          @forelse($reports as $report)
                              <div class="accordion-item">
                                  <div class="accordion-header">
                                      <div
                                          class="accordion-button collapsed"
                                          role="button"
                                          data-bs-toggle="collapse"
                                          data-bs-target="#collapse{{$report->id}}"
                                          aria-expanded="false"
                                          aria-controls="collapse{{$report->id}}">
                                          <div class="row align-items-center w-100 gy-4 gx-sm-3 gx-0">
                                              <div class="col-md-4">
                                                  <div class="table-accordion-header transfer-by">
                                                      <span class="icon-btn icon-btn-sm primary circle">
                                                        <i class="bi bi-file-text"></i>
                                                      </span>
                                                      <div>
                                                          <h6>
                                                              {{translate("Template")}}
                                                          </h6>
                                                          <p>{{$report->template->name}}</p>
                                                      </div>
                                                  </div>
                                              </div>

                                              <div class="col-md-4 col-6 text-sm-center">
                                                  <div class="table-accordion-header">
                                                      <h6>
                                                          {{translate("Generated On")}}
                                                      </h6>
                                                      <p>
                                                          {{ get_date_time($report->created_at) }}
                                                      </p>
                                                  </div>
                                              </div>

                                              <div class="col-md-4 col-6 text-end">
                                                  <div class="table-accordion-header d-flex justify-content-end align-items-center gap-2">
                                                      <h6>
                                                          {{translate("Words")}}
                                                      </h6>
                                                      <span class="i-badge capsuled info">
                                                          {{$report->total_words}}
                                                      </span>
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
                                                      {{translate('Generated Content')}}
                                                  </h6>
                                                  <p class="value">
                                                      @php echo ($report->content) @endphp
                                                  </p>
                                              </li>

                                              @foreach ($report->open_ai_usage as $key => $val )

                                                  <li class="list-group-item">
                                                      <h6 class="title">
                                                          {{k2t($key)}}
                                                      </h6>
                                                      <p class="value">
                                                          {{$val}}
                                                      </p>
                                                  </li>
                                              @endforeach
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
</div>

@endsection
@section('modal')

    <div class="modal fade" id="report-info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="report-info"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Report Information')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>


                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-12">
                            <div class="form-inner">
                                <label for="content" class="form-label" >
                                    {{translate('Genarated Content')}}
                                </label>

                                <textarea disabled name="content" id="content" cols="30" rows="4"></textarea>

                            </div>

                        </div>

                        <div class="col-lg-12">

                            <ul class="list-group list-group-flush" id="additionalInfo">

                            </ul>

                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="i-btn btn--md ripple-dark danger" data-anim="ripple" data-bs-dismiss="modal">
                        {{translate("Close")}}
                    </button>

                </div>

            </div>
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

        $(".select2").select2({});

	})(jQuery);
</script>
@endpush


