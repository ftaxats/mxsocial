@extends('admin.layouts.master')

@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-xxl-9 col-xl-8">
            <div class="i-card-md">
                <div class="card--header text-end">
                    <h4 class="card-title">
                        {{ translate('Affiliate Report (Current Year)')}}
                    </h4>
                </div>

                <div class="card-body">
                    <div id="affiliate-report"></div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-xl-4">
            @include('admin.partials.summary')
        </div>

        <div class="col-12">
            <div class="i-card-md">
                <div class="card-body">
                    <div class="search-action-area">
                        <div class="row g-3">
                            <div class="col-md-12 d-flex justify-content-end">
                            <div class="filter-wrapper">
                                <button class="i-btn btn--primary btn--sm filter-btn" type="button">
                                    <i class="las la-filter"></i>
                                </button>
                                <div class="filter-dropdown">
                                    <form action="{{route(Route::currentRouteName())}}" method="get">
                                        <div class="form-inner">
                                            <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}"  placeholder='{{translate("Filter by date")}}'>
                                        </div>
                                        <div class="form-inner">
                                            <select name="user" id="user" class="user">
                                                <option value="">
                                                    {{translate('Select User')}}
                                                </option>
                                                @foreach(system_users() as $user)
                                                   <option  {{Arr::get($user,"username",null) ==   request()->input('user') ? 'selected' :""}} value="{{Arr::get($user,"username",null)}}"> {{Arr::get($user,"name",null)}}
                                                  </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-inner">
                                            <input type="text"  name="search" value="{{request()->input('search')}}"  placeholder='{{translate("Search by Transaction ID")}}'>
                                        </div>
                                        <button class="i-btn btn--md info w-100">
                                            <i class="las la-sliders-h"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="ms-3">
                                <a href="{{route(Route::currentRouteName())}}"  class="i-btn btn--sm danger">
                                    <i class="las la-sync"></i>
                                </a>
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
                                        {{translate('TRX Number')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('User')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Referred User')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Subscription Package')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Commission Rate')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Amount')}}
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
                                                 {{ diff_for_humans($report->created_at)  }}
                                            </div>
                                        </td>
                                        <td  data-label="{{translate('Trx Code')}}">
                                            <span class="trx-number me-1">
                                                {{$report->trx_code}}
                                            </span>

                                            <span  data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Copy')}}" class="icon-btn  success fs-20 pointer copy-trx"><i class="lar la-copy"></i></span>
                                        </td>
                                        <td data-label='{{translate("User")}}'>
                                            <a href="{{route('admin.user.show',$report->user->uid)}}">
                                                {{$report->user->name}}
                                            </a>
                                        </td>
                                        <td data-label='{{translate("Referred To")}}'>
                                            @if($report->referral)
                                                <a href="{{route('admin.user.show',$report->referral->uid)}}">
                                                    {{$report->referral->name}}
                                                </a>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td data-label='{{translate("Subscription Package")}}'>
                                              {{$report->subscription? @$report->subscription->package->title  : '-'}}
                                        </td>
                                        <td data-label='{{translate("Commission Rate")}}'>
                                              {{$report->commission_rate}}%
                                        </td>
                                        <td data-label='{{translate("Amount")}}'>
                                            {{@num_format(
                                                number : $report->commission_amount??0,
                                                calC   : true
                                            )}}
                                        </td>
                                        <td data-label='{{translate("Options")}}'>
                                            <div class="table-action">
                                                <a  data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Info')}}" href="javascript:void(0);" data-report="{{$report}}" class="pointer show-info icon-btn info">
                                                    <i class="las la-info"></i></a>
                                            </div>
                                        </td>
                                   </tr>
                                @empty
                                    <tr>
                                        <td class="border-bottom-0" colspan="9">
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
        </div>
    </div>

    @php
        $symbol = @session()->get('currency')?->symbol ?? base_currency()->symbol;
    @endphp
@endsection

@section('modal')
    @include('modal.delete_modal')
    @include('modal.bulk_modal')

    <div class="modal fade" id="report-info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="report-info"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
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
    <script src="{{asset('assets/global/js/apexcharts.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/init.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";

        $(".select2").select2({});
        $(".user").select2({});
        $(".type").select2({});


        $(document).on('click','.show-info',function(e){

            e.preventDefault()

            var modal = $('#report-info');
            var report = JSON.parse($(this).attr('data-report'))

            var cleanContent = DOMPurify.sanitize(report.note);

            $('.content').html(cleanContent)

            modal.modal('show')
        });


        var labels = @json(array_keys($graph_data));
        var data   = @json(array_values($graph_data));

        var options = {
            series: [{
                name: "{{ translate('Total Earning') }}",
                data: data
            }],
            chart: {
                nonce:"{{ csp_nonce() }}",
                height: 300,
                type: 'bar',
                events: {
                    click: function(chart, w, e) {
                    }
                },
                toolbar: {
                       show: false
                    }
            },
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    distributed: true,
                }
            },
            dataLabels: {
                enabled: false
            },

            tooltip: {
                shared: false,
                intersect: true,
                y: {
                    formatter: function (value, { series, seriesIndex, dataPointIndex, w }) {
                    return formatCurrency(value);
                    }
                }
            },

            legend: {
                show: false
            },
            xaxis: {
                categories: labels,
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#affiliate-report"), options);
        chart.render();

        function formatCurrency(value) {
            var symbol =  "{{  $symbol }}" ;
            var suffixes = ["", "K", "M", "B", "T"];
            var order = Math.floor(Math.log10(value) / 3);
            var suffix = suffixes[order];
            if(value < 1)
            {return symbol+value}
            var scaledValue = value / Math.pow(10, order * 3);
            return symbol + scaledValue.toFixed(2) + suffix;
        }


	})(jQuery);
</script>
@endpush





