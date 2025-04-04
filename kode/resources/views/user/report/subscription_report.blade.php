@extends('layouts.master')

@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')

<div>


    <div class="w-100 d-flex align-items-end justify-content-between gap-lg-5 gap-3 flex-md-nowrap flex-wrap mb-4">
        <h4>
            {{translate(Arr::get($meta_data,'title'))}}
        </h4>

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

    <div class="collapse {{ hasFilter(['date','package','search']) ? 'show' : '' }} filterTwo mb-3" id="tableFilter">
        <div class="i-card-md">
            <div class="card-body">
            <div class="search-action-area p-0">
                <div class="search-area">
                <form action="{{route(Route::currentRouteName())}}">
                    <div class="form-inner">
                        <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}"  placeholder='{{translate("Filter by date")}}'>
                    </div>

                    <div class="form-inner">
                        <select name="package" id="package" class="form-select">
                            <option value="">
                                {{translate('Select Package')}}
                            </option>
                            @foreach($packages as $package)
                            <option  {{$package->slug ==  request()->input('package') ? 'selected' :""}}
                                value="{{$package->slug}}"> {{$package->title}}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-inner">
                        <input type="text"  name="search" value="{{request()->input('search')}}" placeholder='{{translate("Search by Transaction ID")}}'>
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
                                            <div class="col-lg-2 col-sm-4 col-12">
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

                                            <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-sm-center text-start">
                                                <div class="table-accordion-header">
                                                    <h6>
                                                        {{translate("Expired In")}}
                                                    </h6>

                                                    <p>
                                                        @if($report->expired_at)
                                                            {{ get_date_time($report->expired_at,'d M, Y') }}
                                                        @else
                                                            --
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-sm-end text-end">
                                                <div class="table-accordion-header">
                                                    <h6>
                                                        {{translate("Package")}}
                                                    </h6>

                                                    <p>
                                                        {{@$report->package->title}}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-start">
                                                <div class="table-accordion-header">
                                                    <h6>
                                                        {{translate("Status")}}
                                                    </h6>
                                                    @php echo (subscription_status($report->status)) @endphp
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-sm-4 col-6 text-sm-center text-end">
                                                <div class="table-accordion-header">
                                                    <h6>
                                                        {{translate("Payment Amount")}}
                                                    </h6>
                                                    <p>
                                                        {{@num_format(
                                                            number : $report->payment_amount??0,
                                                            calC   : true
                                                        )}}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-sm-4 col-6 text-sm-end text-start">
                                                <div class="table-accordion-header">
                                                    <h6>
                                                        {{translate("Date")}}
                                                    </h6>

                                                    <p>
                                                        @if($report->created_at)
                                                            {{ get_date_time($report->created_at) }}
                                                        @else
                                                             --
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="collapse{{$report->id}}" class="accordion-collapse collapse" data-bs-parent="#wordReports">
                                    <div class="accordion-body">
                                        <ul class="list-group list-group-flush">

                                            @php
                                                $informations = [
                                                    "AI_word_balance"          => $report->word_balance,
                                                    "remaining_word_balance"   => $report->remaining_word_balance,
                                                    "carried_word_balance"     => $report->carried_word_balance,
                                                    "total_social_profile"     => $report->total_profile,
                                                    "carried_profile_balance"  => $report->carried_profile,
                                                    "social_post_balance"      => $report->post_balance,
                                                    "remaining_post_balance"   => $report->remaining_post_balance,
                                                    "carried_post_balance"     => $report->carried_post_balance,
                                                ];
                                            @endphp


                                            @foreach ($informations  as  $key => $val)

                                                <li class="list-group-item">
                                                    <h6 class="title">
                                                        {{k2t($key)}}
                                                    </h6>
                                                    <p class="value">
                                                        {{$val == App\Enums\PlanDuration::UNLIMITED->value ? App\Enums\PlanDuration::UNLIMITED->name : $val }}
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

        $(".package").select2({});

        $(".select2").select2({});

	})(jQuery);
</script>
@endpush





