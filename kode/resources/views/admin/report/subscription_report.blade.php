@extends('admin.layouts.master')

@push('style-include')
    <link  nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-xxl-9 col-xl-8">
            <div class="i-card-md">
                <div class="card--header text-end">
                    <h4 class="card-title">
                         {{ translate('Subscription Revenue (Current Year)')}}
                    </h4>
               </div>
                <div class="card-body">
                    <div id="subscrition-report"></div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-xl-4">
            @include('admin.partials.summary',['header' => true , "header_info" => [
                'title' => translate("Total subscription amount"),
                'total' => $total_subscription_amount,
                'note'  => translate('The total revenue generated from all subscription payments collected by the system'),
            ]])

    
        </div>

        <div class="col-12">
            <div class="i-card-md">
                <div class="card-body">
                    <div class="search-action-area">
                        <div class="row g-4">
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
                                                <select name="package" id="package" class="package">
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
                                        {{translate('TRX Number')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Expired In')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('User')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Package')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Status')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Paid Amount')}}
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
                                            <td  data-label="{{translate('Trx Code')}}">
                                                <span class="trx-number me-1">
                                                    {{$report->trx_code}}
                                                </span>
        
                                                <span  data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Copy')}}" class="icon-btn  success fs-20 pointer copy-trx"><i class="lar la-copy"></i></span>
                                            </td>
                                            <td data-label="{{translate('Expired In')}}">
                                                @if($report->expired_at)
                                                   {{ get_date_time($report->expired_at) }}
                                                   <div>
                                                       {{ diff_for_humans($report->expired_at) }}
                                                   </div>
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td data-label='{{translate("User")}}'>
                                                <a href="{{route('admin.user.show',$report->user->uid)}}">
                                                    {{$report->user->name}}
                                                </a>
                                            </td>
                                            <td data-label='{{translate("Package")}}'>
                                                 {{@$report->package?->title}}
                                            </td>
                                            <td data-label='{{translate("Status")}}'>
                                                 @php echo (subscription_status($report->status)) @endphp
                                            </td>
                                            <td data-label='{{translate("Payment Info")}}'>
                                                {{@num_format(
                                                    number : $report->payment_amount??0,
                                                    calC   : true
                                                )}}
                                            </td>
                                            <td data-label='{{translate("Date")}}'>
                                                @if($report->created_at)
                                                   {{ get_date_time($report->created_at) }}
                                                   <div>
                                                       {{ diff_for_humans($report->created_at) }}
                                                   </div>
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td data-label='{{translate("Options")}}'>
                                                <div class="table-action">
                                                    @php
                                                     $informations = collect([
        
                                                            "AI_word_balance"          => $report->word_balance,
                                                            "remaining_word_balance"   => $report->remaining_word_balance,
                                                            "carried_word_balance"     => $report->carried_word_balance,
        
                                                            "total_social_profile"     => $report->total_profile,
                                                            "carried_profile_balance"  => $report->carried_profile,
        
                                                            "social_post_balance"      => $report->post_balance,
                                                            "remaining_post_balance"   => $report->remaining_post_balance,
                                                            "carried_post_balance"     => $report->carried_post_balance,
                                                        ])->mapWithKeys(fn($value,$key) :array =>  [k2t($key) => $value])->toArray();
        
              
                                                    @endphp
        
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Info')}}" href="javascript:void(0);" data-remarks="{{$report->remarks}}" data-info ="{{collect($informations)}}"  class="pointer show-info icon-btn info">
                                                        <i class="las la-info"></i></a>
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Update')}}"  href="javascript:void(0);" data-report ="{{$report}}" class="update fs-15 icon-btn warning"><i class="las la-pen"></i></a>
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
    @include('admin.partials.modal.subscription_report')
    @include('admin.partials.modal.update_subscription')
@endsection


@push('script-include')
    <script  src="{{asset('assets/global/js/apexcharts.js')}}"></script>
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
        $(".package").select2({});



        $(document).on('click','.show-info',function(e){

            e.preventDefault()

            var modal = $('#report-info');

            var remark = ($(this).attr('data-remarks'))
            var infos  = JSON.parse($(this).attr('data-info'));

            
            var cleanContent = DOMPurify.sanitize(remark);

            $('#content').html(cleanContent)
            var lists = "";
            var val = ""
            for(var i in infos ){
                val = infos[i] == -1 ? "Unlimited" :infos[i];
                lists +=`<li class="list-group-item">${i.charAt(0).toUpperCase() + i.slice(1).replace('_', ' ')} :${val}</li>`
            }
            $("#additionalInfo").html(lists);

            modal.modal('show')

        });


        $(document).on('click','.update',function(e){

            e.preventDefault()

            var subscription = JSON.parse($(this).attr('data-report'))
            var modal = $('#updatesubscription')
            modal.find('input[name="id"]').val(subscription.id)
            modal.find('input[name="expired_at"]').val(subscription.expired_at)
            modal.find('textarea[name="remarks"]').html(subscription.remarks)
            modal.find('select[name="status"]').val(subscription.status)
            modal.modal('show')
        })




        
        var labels = @json(array_keys($graph_data));
        var data   = @json(array_values($graph_data));

        var options = {
            chart: {
                height: 425,
                type: "area",
                nonce:"{{ csp_nonce() }}",
                toolbar: {
                       show: false 
                    }
            },
            dataLabels: {
                enabled: false,
            },
           colors: ['var(--color-success)',  'var(--color-warning)' ,"var(--color-danger)"],
            series: [
                {
                name: "{{ translate('Subscriptions Income') }}",
                data: data,
                },

            ],
            xaxis: {
                categories: labels,
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


        var chart = new ApexCharts(document.querySelector("#subscrition-report"), options);
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





