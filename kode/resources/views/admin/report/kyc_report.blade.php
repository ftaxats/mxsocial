@extends('admin.layouts.master')

@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')

    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="i-card-md">
                <div class="card--header text-end">
                    <h4 class="card-title">
                         {{ translate('KYC Statistics (Current Year)')}}
                    </h4>
               </div>
                <div class="card-body">
                    <div class="row g-2 text-center mb-5">
                        @include('admin.partials.summary',['style' => 'card','col' => 3])
                    </div>
                    <div id="kyc-report"></div>
                </div>
            </div>
        </div>
    </div>

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
                                {{translate('User')}}
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
                                        {{ diff_for_humans($report->created_at)  }}
                                    </div>
                                </td>
                                <td data-label='{{translate("User")}}'>
                                    <a href="{{route('admin.user.show',$report->user->uid)}}">
                                        {{$report->user->name}}
                                    </a>
                                </td>
                                <td  data-label='{{translate("Status")}}'>
                                    @php echo  (kyc_status($report->status))  @endphp
                                </td>
                                <td data-label='{{translate("Options")}}'>
                                    <div class="table-action">
                                        <a data-bs-toggle="tooltip" data-bs-placement="top"   data-bs-title="{{translate('Update')}}"  href="{{route('admin.kyc.report.details',$report->id)}}"  class="fs-15 icon-btn info"><i class="las la-pen"></i></a>

                                    </div>
                                </td>
                           </tr>
                        @empty
                            <tr>
                                <td class="border-bottom-0" colspan="5">
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


@push('script-include')
    <script  src="{{asset('assets/global/js/apexcharts.js')}}"></script>
    <script  nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script  nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
    <script  nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/init.js')}}"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";
        
        $(".user").select2({});
        $(".status").select2({});

        var options = {

            series: [

                {
                    name: "{{ translate('Total Log') }}",
                    data: @json(array_column($graph_data , 'total')),
                },
                {
                    name: "{{ translate('Approved Log') }}",
                    data: @json(array_column($graph_data , 'approved')),
                },
                {
                    name: "{{ translate('Pending Log') }}",
                    data: @json(array_column($graph_data , 'pending')),
                },
                {
                    name: "{{ translate('Rejected Log') }}",
                    data: @json(array_column($graph_data , 'rejected')),
                },

          ],
          chart: {
            nonce:"{{ csp_nonce() }}",
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: true
            }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: {
              position: 'bottom',
              offsetX: -10,
              offsetY: 0
            }
          }
        }],
        plotOptions: {
          bar: {
            horizontal: false,
            borderRadius: 10,
            borderRadiusApplication: 'end', 
            borderRadiusWhenStacked: 'last', 
            dataLabels: {
              total: {
                enabled: true,
                style: {
                  fontSize: '13px',
                  fontWeight: 900
                }
              }
            }
          },
        },
        xaxis: {
            categories: @json(array_keys($graph_data)),
        },
        legend: {
          position: 'bottom',
          offsetY: 10
        },
        fill: {
          opacity: 1
        }
        };

        var chart = new ApexCharts(document.querySelector("#kyc-report"), options);
        chart.render();

	})(jQuery);

</script>

@endpush





