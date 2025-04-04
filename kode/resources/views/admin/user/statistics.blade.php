@extends('admin.layouts.master')
@push('styles')
   <style nonce="{{ csp_nonce() }}">
            .h-330 {
                height: 330px;
            }

            .top-country-users ul {
                -moz-column-count: 3;
                column-count: 3;
                font-size: 14px;
            }
            .top-country-users ul li{
                padding: 3px 0;
            }

   </style>
@endpush

@section('content')
    <div class="row g-3 mb-3 row-cols-xxl-5 row-cols-md-3 row-cols-sm-2 row-cols-1">
        @php
                    $cards = ([
                                [
                                    "class"  => 'col',
                                    "title"  => translate("Total users"),
                                    "total"  => count(system_users()),
                                    "icon"   => '<i class="las la-user-friends"></i>',
                                    "bg"     => 'primary',
                                ],
                                [
                                    "class"  => 'col',
                                    "title"  => translate("Active users"),
                                    "total"  => $active_users,
                                    "icon"   => '<i class="las la-user-check"></i>',
                                    "bg"     => 'info',
                                ],
                                [
                                    "class"  => 'col',
                                    "title"  => translate("Inactive users"),
                                    "total"  => $banned_users,
                                    "icon"   => '<i class="las la-user-minus"></i>',
                                    "bg"     => 'danger',
                                ],
                                [
                                    "class"  => 'col',
                                    "title"  => translate("Subscribed users"),
                                    "total"  => $subscribed_users,
                                    "icon"   => '<i class="las la-user-tag"></i>',
                                    "bg"     => 'success',
                                ],
                                [
                                    "class"  => 'col',
                                    "title"  => translate("Unsubscribed users"),
                                    "total"  => $unsubscribed_users,
                                    "icon"   => '<i class="las la-user-injured"></i>',
                                    "bg"     => 'warning',
                                ]
                           ]);
        @endphp
        @include("admin.partials.report_card")

    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="i-card-md home home">
                <div class="card--header">
                        <h4 class="card-title">
                            {{translate("Users By Countries")}}
                        </h4>
                </div>
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-6">
                            <div id="usersByCountry" class="apex-chart h-330"></div>
                        </div>
                        <div class="col-lg-6">
                            <h6 class="mb-3">
                                {{translate("Top 30 Countries")}}
                            </h6>
                            <div class="top-country-users ul">
                                    <ul>
                                        @foreach ($top_countries as $country)
                                            <li>
                                                <span> {{ $country->name  }} </span> -  <span>  {{ $country->users_count  }}  </span>
                                            </li>
                                        @endforeach

                                    </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-3 g-3">
        <div class="col-xl-6">
            <div class="i-card-md">
                <div class="card--header">
                    <h4 class="card-title">
                        @php
                                $date = \DateTime::createFromFormat('!m',  request()->input('month', date("m")));
                                $monthName = $date->format('F');
                        @endphp
                       {{   request()->input('month', date("m")) == date("m") ? translate('Current')  :  $monthName  }} {{translate("month users")}}
                    </h4>

                    <div class="d-flex justify-content-end align-items-center gap-2 custom--filter">
                        <select class="form-select" name="months" id="months">


                            @for ($month = 1; $month <= 12; $month++)
                                <option {{$month == request()->input('month', date("m")) ? "selected" : ''}} value="{{ $month }}">{{ Carbon\Carbon::create(null, $month)->format('F') }}</option>
                            @endfor


                        </select>
                        <a href="{{route(Route::currentRouteName())}}"  class="i-btn btn--sm danger-transparent">
                            <i class="las la-sync"></i>
                          </a>
                    </div>
                </div>
                <div class="card-body">

                    <div id="monthlyUsers" class="apex-chart"></div>

                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="i-card-md">
                <div class="card--header">
                    <h4 class="card-title">
                        {{translate("Current year users")}}
                    </h4>
                </div>
                <div class="card-body">

                      <div id="yearlyUsers" class="apex-chart"></div>

                </div>
            </div>
        </div>
    </div>

@endsection


@push('script-include')
  <script src="{{asset('assets/global/js/googlemaps/loader.js')}}"></script>
  <script   src="{{asset('assets/global/js/apexcharts.js')}}"></script>
@endpush


@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";

			var mapData = [];
			for (const [key, value] of Object.entries(JSON.parse(`<?php echo $user_by_countries; ?>`))) {
				mapData.push([`${key}`, `${value}`]);
			}


           google.charts.load('current', {
				'packages':['geochart'],
				'mapsApiKey': '{{site_settings("map_api_key")}}',

			});

			google.charts.setOnLoadCallback(drawMapChart);

			function drawMapChart() {

                var root = document.documentElement;
                var style = getComputedStyle(root);
                var color = style.getPropertyValue('--color-danger');
				var options = {colors: [color] , nonce:"{{ csp_nonce() }}"};
				var result = [];

				result.push(['Country', 'Users']);

				mapData.map(function(row) { result.push([row[0], parseInt(row[1])]); });

				var data = google.visualization.arrayToDataTable(result);
				var chart = new google.visualization.GeoChart(document.getElementById('usersByCountry'));
				chart.draw(data, options);
			}



        /** Current year users */

        var monthlyLabel = @json(array_keys($user_by_year));
        var options = {
            chart: {
                nonce:"{{ csp_nonce() }}",
                height: 350,
                type: "bar",
                toolbar: {
                       show: false
                    }

            },
            dataLabels: {
                enabled: false,
            },
            colors: ["var(--color-info)"],
            series: [
                {
                    name: "{{ translate('Total Users') }}",
                    data:  @json(array_values($user_by_year)),
                },

            ],
            xaxis: {
                categories: monthlyLabel,
            },

               tooltip: {
                    shared: false,
                    intersect: true,
                    y: {
                        formatter: function (value, { series, seriesIndex, dataPointIndex, w }) {
                        return parseInt(value);
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

        var chart = new ApexCharts(document.querySelector("#yearlyUsers"), options);
        chart.render();




       /** Current month users */
        var monthlyLabel = @json(array_keys($user_by_month));

        var options = {
            chart: {
                nonce:"{{ csp_nonce() }}",
                height: 350,
                type: "line",
                toolbar: {
                       show: false
                    }
            },
            dataLabels: {
                enabled: false,
            },
            colors: ["var(--color-success)"],
            series: [
                {
                    name: "{{ translate('Total Users') }}",
                    data:  @json(array_values($user_by_month)),
                },

            ],
            xaxis: {
                categories: monthlyLabel,
            },

           tooltip: {
                shared: false,
                intersect: true,
                y: {
                    formatter: function (value, { series, seriesIndex, dataPointIndex, w }) {
                    return parseInt(value);
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

        var chart = new ApexCharts(document.querySelector("#monthlyUsers"), options);
        chart.render();


        $(document).on('change','#months',function(e){
            window.location.href = "{{url()->current()}}/?"+"month="+$(this).val()
        });


	})(jQuery);
</script>
@endpush





