@extends('admin.layouts.master')

@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')

    <div class="row g-4">
        <div class="col-xxl-3 col-xl-4">
            @include('admin.partials.summary',['header' => true , "header_info" => [
                'title' => translate("Total withdraw amount"),
                'total' => $total_withdraw,
                'note'  => translate('The Total Withdraw Amount Accumulated From All Users Within The System.'),
            ]])

        </div>

        <div class="col-xxl-9 col-xl-8">
            <div class="i-card-md h-100">
            <div class="card--header text-end">
                <h4 class="card-title">
                        {{ translate('Withdraw Report (Current Year)')}}
                </h4>
            </div>
                <div class="card-body">
                    <div id="withdraw-report"></div>
                </div>
            </div>
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
                                                <select name="status" id="status" class="status">
                                                    <option value="">
                                                        {{translate('Select status')}}
                                                    </option>
                                                    @foreach(App\Enums\WithdrawStatus::toArray() as $k => $v)
                                                        <option  {{$v ==   request()->input('status') ? 'selected' :""}} value="{{$v}}">
                                                            {{ucfirst(t2k($k))}}
                                                       </option>
                                                    @endforeach
                                                </select>
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
                                                <input type="text"  name="search" value="{{request()->input('search')}}"  placeholder='{{translate("Search by Transaction ID or remarks")}}'>
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
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">
                                       #
                                    </th>
                                    <th scope="col">
                                        {{translate('Date')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('User')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Method')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('TRX Number')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Receivable Amount')}}
                                   </th>
                                    <th scope="col">
                                        {{translate('Payment Amount')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Status')}}
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
                                        <td data-label='{{translate("User")}}'>
                                            <a href="{{route('admin.user.show',$report->user->uid)}}">
                                                {{$report->user->name}}
                                            </a>
                                        </td>
                                        <td data-label='{{translate("Payment Method")}}'>
                                            {{$report->method?->name}}
                                        </td>
                                        <td  data-label="{{translate('Trx Code')}}">
                                            <span class="trx-number me-1">
                                                {{$report->trx_code}}
                                            </span>
                                            <span  data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Copy')}}" class="icon-btn  success fs-20 pointer copy-trx"><i class="lar la-copy"></i></span>
                                        </td>
                                        <td  data-label='{{translate("Receivable Amount")}}'>
                                            {{num_format($report->amount,@$report->currency)}}
                                        </td>

                                        <td  data-label='{{translate("Final Amount")}}'>
                                              {{num_format($report->final_amount,@$report->currency)}}
                                        </td>
                                        <td  data-label='{{translate("Status")}}'>

                                            @php echo  (withdraw_status($report->status))  @endphp
                                        </td>
                                        <td data-label='{{translate("Options")}}'>
                                            <div class="table-action">
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Update')}}"  href="{{route('admin.withdraw.report.details',$report->id)}}"  class="fs-15 icon-btn info"><i class="las la-pen"></i></a>
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

@push('script-include')
    <script   src="{{asset('assets/global/js/apexcharts.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}"src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/init.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";

        $(".select2").select2({});
        $(".user").select2({});
        $(".status").select2({});



        var options = {

            chart: {
              height: 468,
              type: "line",
              nonce:"{{ csp_nonce() }}",
              toolbar: {
                       show: false
                    }
            },
            dataLabels: {
                enabled: false,
            },
          colors: ['var(--color-info)','var(--color-primary)','var(--color-success)','var(--color-warning)',  'var(--color-danger)'],
          series: [
            {
              name: "{{ translate('Total Withdraw') }}",
              data: @json(array_column($graph_data , 'total')),
            },
            {
              name: "{{ translate('Total Charge') }}",
              data: @json(array_column($graph_data , 'charge')),
            },
            {
              name: "{{ translate('Success Withdraw') }}",
              data: @json(array_column($graph_data , 'approved')),
            },
            {
              name: "{{ translate('Pending Withdraw') }}",
              data: @json(array_column($graph_data , 'pending')),
            },
            {
              name: "{{ translate('Rejected Withdraw') }}",
              data: @json(array_column($graph_data , 'rejected')),
            },
          ],
          xaxis: {
            categories: @json(array_keys($graph_data)),
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
          markers: {
            size: 6,
          },
          stroke: {
            width: [4, 4],
          },
          legend: {
            horizontalAlign: "left",
            offsetX: 40,
          },
        };





        var chart = new ApexCharts(document.querySelector("#withdraw-report"), options);
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





