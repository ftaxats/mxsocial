@extends('admin.layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush
@section('content')

<div class="page-title-box">
  <h4 class="page-title">
       {{translate($title)}}
  </h4>
  <div class="page-title-right d-flex justify-content-end align-items-center gap-3">
      <form action="{{route(Route::currentRouteName())}}" method="get">
          <div class="date-search">
              <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}"  placeholder="{{translate('Filter by date')}}">
              <button type="submit" class="me-2"><i class="bi bi-search"></i></button>
              <a href="{{route(Route::currentRouteName())}}"  class="i-btn btn--sm danger">
                <i class="las la-sync"></i>
              </a>
          </div>
      </form>
  </div>
</div>

<div class="row mb-3 g-3">
  <div class="col-xl-6">
    <div class="row g-3">
      <div class="col-lg-6 col-md-6 col-sm-6">
          <div class="i-card-sm style-2 primary">
            <div class="card-info">
                <h3>
                    {{Arr::get($data,"total_account",0)}}
                </h3>
                <h5 class="title">
                  {{translate("Total Account")}}
                </h5>
                <a href="{{route('admin.social.account.list')}}" class="i-btn btn--sm btn--primary-outline">
                      {{translate("View All")}}
                </a>
            </div>
            <div class="d-flex flex-column align-items-end gap-4">
                <div class="icon">
                  <i class="las la-user-friends"></i>
                </div>
            </div>
          </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="i-card-sm style-2 success">
            <div class="card-info">
                <h3>
                  {{Arr::get($data,"total_post",0)}}
                </h3>
                <h5 class="title">
                  {{translate("Total Post")}}
                </h5>
                <a href="{{route('admin.social.post.list')}}" class="i-btn btn--sm btn--primary-outline">
                  {{translate("View All")}}
                </a>
            </div>
            <div class="d-flex flex-column align-items-end gap-4">
              <div class="icon">
                <i class="las la-comments"></i>
              </div>
            </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6">
          <div class="i-card-sm style-2 warning">
              <div class="card-info">
                <h3>
                  {{(Arr::get($data,"pending_post",0))}}
                </h3>
                <h5 class="title">
                    {{translate('Pending Post')}}
                </h5>
                <a href="{{route('admin.social.post.list',['status' =>  App\Enums\PostStatus::PENDING->value])}}" class="i-btn btn--sm btn--primary-outline">
                      {{translate("View All")}}
                </a>
              </div>
              <div class="d-flex flex-column align-items-end gap-4">
                <div class="icon">
                  <i class="las la-comment-alt"></i>
                </div>
              </div>
          </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="i-card-sm style-2 danger">
          <div class="card-info">
            <h3>{{Arr::get($data,"schedule_post",0)}} </h3>
            <h5 class="title">{{translate('Schedule Post')}}</h5>
            <a href="{{route('admin.social.post.list',['status' =>  App\Enums\PostStatus::SCHEDULE->value])}}" class="i-btn btn--sm btn--primary-outline">
                {{translate("View All")}}
            </a>
          </div>
          <div class="d-flex flex-column align-items-end gap-4">

            <div class="icon">
              <i class="las la-comment"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="i-card-sm style-2 success">
          <div class="card-info">
                <h3>
                {{Arr::get($data,"success_post",0)}}
                </h3>
                <h5 class="title">{{translate('Success Post')}}</h5>
                <a href="{{route('admin.social.post.list',['status' =>  App\Enums\PostStatus::SUCCESS->value])}}" class="i-btn btn--sm btn--primary-outline">
                    {{translate("View All")}}
                </a>
         </div>
          <div class="d-flex flex-column align-items-end gap-4">
            <div class="icon">
              <i class="las la-comment"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="i-card-sm style-2 danger">
          <div class="card-info">
                <h3>
                {{Arr::get($data,"failed_post",0)}}
                </h3>
                <h5 class="title">{{translate('Failed Post')}}</h5>
                <a href="{{route('admin.social.post.list',['status' =>  App\Enums\PostStatus::FAILED->value])}}" class="i-btn btn--sm btn--primary-outline">
                    {{translate("View All")}}
                </a>
         </div>
          <div class="d-flex flex-column align-items-end gap-4">
            <div class="icon">
              <i class="las la-comment"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-6 col-xl-6">
    <div class="i-card-md">
      <div class="card--header">
        <h4 class="card-title">
           {{translate("Social Post  by platform")}}
        </h4>

        <a href="{{route('admin.social.post.list')}}" class="i-btn btn--sm btn--primary btn--outline">
           {{translate("View All")}}
        </a>
      </div>
      <div class="card-body">
        <div id="platformReport" class="apex-chart"></div>
        <div class="row g-2 mt-4 text-center">

          <div class="col-6 col-sm-6">
              <div class="p-3 border border-dashed border-start-0 rounded-2">
                  <h5 class="mb-1">
                      <span>
                        {{Arr::get($data,"platform",0)}}
                      </span>
                  </h5>
                  <p class="text-muted mb-0">
                      {{translate("Platforms")}}
                  </p>
              </div>
          </div>

          <div class="col-6 col-sm-6">
              <div class="p-3 border border-dashed border-start-0 rounded-2">
                  <h5 class="mb-1"><span>
                    {{Arr::get($data,"total_post",0)}}
                  </span></h5>
                  <p class="text-muted mb-0">
                       {{translate("Total Post")}}
                  </p>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-12">
    <div class="i-card-md">
      <div class="card--header">
        <h4 class="card-title">
            {{translate("Social Post")}}
        </h4>
      </div>
      <div class="card-body">
          <div class="row row-cols-lg-5 row-cols-md-5 row-cols-sm-2 row-cols-2 g-2 text-center mb-5">

            <div class="col">
                <div class="p-3 border border-dashed border-start-0 rounded-2">
                    <h5 class="mb-1">
                        <span>
                          {{Arr::get($data,"total_post",0)}}
                        </span>
                    </h5>
                    <p class="text-muted mb-0">
                        {{translate("Total Post")}}
                    </p>
                </div>
            </div>

            <div class="col">
                <div class="p-3 border border-dashed border-start-0 rounded-2">
                    <h5 class="mb-1"><span>
                      {{Arr::get($data,"pending_post",0)}}
                    </span></h5>
                    <p class="text-muted mb-0">
                        {{translate("Pending")}}
                    </p>
                </div>
            </div>
            <div class="col">
                <div class="p-3 border border-dashed border-start-0 rounded-2">
                    <h5 class="mb-1"><span>
                      {{Arr::get($data,"success_post",0)}}
                    </span></h5>
                    <p class="text-muted mb-0">
                        {{translate("Success")}}
                    </p>
                </div>
            </div>
            <div class="col">
                <div class="p-3 border border-dashed border-start-0 rounded-2">
                    <h5 class="mb-1"><span>
                      {{Arr::get($data,"schedule_post",0)}}
                    </span></h5>
                    <p class="text-muted mb-0">
                        {{translate("Schedule Post")}}
                    </p>
                </div>
            </div>
            <div class="col">
                <div class="p-3 border border-dashed border-start-0 rounded-2">
                    <h5 class="mb-1"><span>
                      {{Arr::get($data,"failed_post",0)}}
                    </span></h5>
                    <p class="text-muted mb-0">
                        {{translate("Failed Post")}}
                    </p>
                </div>
            </div>

          </div>
        <div id="postReport" class="apex-chart"></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-xxl-12 col-xl-12">
    <div class="i-card-md">
      <div class="card--header">
        <h4 class="card-title">
           {{translate("Latest Post")}}
        </h4>

        <a href="{{route('admin.social.post.list')}}" class="i-btn btn--sm btn--primary btn--outline">
          {{translate("View All")}}
       </a>
      </div>

      <div class="card-body">
          <div class="table-container">
            <table >
              <thead>
                  <tr>
                      <th scope="col">
                        #
                    </th>
                    <th scope="col">{{translate('Platform')}}</th>
                    <th scope="col">{{translate('Account')}}</th>
                    <th scope="col">{{translate('User')}}</th>
                    <th scope="col">{{translate('Admin')}}</th>
                    <th scope="col">{{translate('Schedule Time')}}</th>
                    <th scope="col">{{translate('Status')}}</th>
                    <th scope="col">{{translate('Post Type')}}</th>
                    <th scope="col">{{translate('Options')}}</th>
                  </tr>
              </thead>

              <tbody>
                @forelse (Arr::get($data,'latest_post',[]) as $post)
                <tr>
                  <td data-label="#">
                      {{$loop->iteration}}
                  </td>
                  <td data-label='{{translate("Name")}}'>
                      <div class="user-meta-info d-flex align-items-center gap-2">
                          <img class="rounded-circle avatar-sm" src='{{imageURL(@$post->account->platform->file,"platform",true)}}' alt="{{@$post->account->platform->file}}">
                          <p>	 {{$post?->account?->platform?->name ?? '-'}}</p>
                      </div>

                  </td>
                  <td data-label='{{translate("Account")}}'>
                      <div class="user-meta-info d-flex align-items-center gap-2">
                          <img class="rounded-circle avatar-sm" data-fallback={{get_default_img()}} src='{{@$post->account->account_information->avatar }}'   alt="{{translate('Social Account Image')}}" >

                          @if(@$post->account->account_information->link)
                              <a target="_blank" href="{{@$post->account->account_information->link}}">
                                  <p>	{{ @$post->account->account_information->name}}</p>
                              </a>
                          @else
                              <p>	{{ @$post->account->account_information->name}}</p>
                          @endif
                          @if( @$post?->platform_response && @$post?->platform_response?->url )
                          -  <a class="i-badge success fs-15" title="{{translate('Show')}}" target="_blank"  href="{{@$post->platform_response?->url}}"> {{translate("View Post")}}
                              </a>

                          @endif
                      </div>
                  </td>

                  <td data-label='{{translate("User")}}'>
                      @if($post->user )
                          <a href="{{route('admin.user.show',$post->user->uid)}}">
                               {{ @$post->user->name }}
                          </a>
                      @else
                          --
                      @endif

                  </td>

                  <td data-label='{{translate("Admin")}}'>
                      {{@$post->admin ? @$post->admin->name : ('--')}}
                  </td>

                  <td data-label='{{translate("Admin")}}'>
                      {{@$post->schedule_time ? get_date_time($post->schedule_time ) : ('--')}}
                  </td>

                  <td data-label='{{translate("Status")}}'>
                       @php echo (post_status($post->status))   @endphp
                       @if( $post->platform_response && @$post->platform_response->response )
                          <a href="javascript:void(0);" data-message="{{$post->platform_response->response }}" class="pointer show-info icon-btn danger">
                              <i class="las la-info"></i></a>
                        @endif


                  </td>
                  <td data-label='{{translate("Type")}}'>
                       @php echo (post_type($post->post_type))   @endphp
                  </td>


                  <td data-label='{{translate("Action")}}'>
                      <div class="table-action">

                          <a data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Show')}}"  href="{{route('admin.social.post.show',['uid' => $post->uid])}}" class="fs-15 icon-btn success"><i class="las la-eye"></i>
                          </a>

                          @if(check_permission('delete_post') )
                              <a data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Delete')}}" href="javascript:void(0);"    data-href="{{route('admin.social.post.destroy',  $post->id)}}" class="pointer delete-item icon-btn danger">
                                  <i class="las la-trash-alt"></i>
                              </a>
                          @else
                              --
                          @endif
                      </div>
                  </td>
              </tr>
              @empty
                  <tr>
                      <td class="border-bottom-0" colspan="9">
                          @include('admin.partials.not_found')
                      </td>
                  </tr>
              @endforelse

              </tbody>
          </table>
          </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('modal')
  @include('modal.delete_modal')

  <div class="modal fade" id="report-info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="report-info"   aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{translate('Response Message')}}
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
  "use strict";

    /** account repots */

    $(document).on('click','.show-info',function(e){

        e.preventDefault()

         var modal = $('#report-info');

         var report = $(this).attr('data-message')


         var cleanContent = DOMPurify.sanitize(report);

         $('.content').html(cleanContent)

          modal.modal('show')
    });

    var monthlyLabel = @json(array_keys($data['monthly_post_graph']));

    var accountValues = [];
    var totalPost =   @json(array_values($data['monthly_post_graph']));
    var pendigPost =   @json(array_values($data['monthly_pending_post']));
    var schedulePost =   @json(array_values($data['monthly_schedule_post']));
    var successPost =   @json(array_values($data['monthly_success_post']));
    var failedPost =   @json(array_values($data['monthly_failed_post']));



    var monthlyLabel = @json(array_keys($data['monthly_post_graph']));
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
      colors: ['var(--color-primary)','var(--color-info)','var(--color-success)',  'var(--color-warning)' ,"var(--color-danger)"],
      series: [
        {
          name: "{{ translate('Total Post') }}",
          data: totalPost,
        },
        {
          name: "{{ translate('Pending Post') }}",
          data: pendigPost,
        },
        {
          name: "{{ translate('Success Post') }}",
          data: successPost,
        },
        {
          name: "{{ translate('Schedule Post') }}",
          data: schedulePost,
        },
        {
          name: "{{ translate('Failed Post') }}",
          data: failedPost,
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

    var chart = new ApexCharts(document.querySelector("#postReport"), options);
    chart.render();

    var platformValues =  @json(array_values($data['post_by_platform']));
    var platformLabel  =  @json(array_keys($data['post_by_platform']));
    var options = {

          series: platformValues,
          chart: {

          nonce:"{{ csp_nonce() }}",
          width: 400,
          type: 'donut',
          toolbar: {
                       show: false
                    },
          dropShadow: {
            enabled: true,
            color: '#111',
            top: -1,
            left: 3,
            blur: 3,
            opacity: 0.2
          }
        },

        stroke: {
          width: 0,
        },
        plotOptions: {
          pie: {
            donut: {
              labels: {
                show: true,
                total: {
                  showAlways: true,
                  show: true
                }
              }
            }
          }
        },

        labels: platformLabel,
        dataLabels: {
          dropShadow: {
            blur: 3,
            opacity: 0.8
          }
        },
        fill: {
          opacity: 1,
          pattern: {
            enabled: true,
          },
          colors: ['var(--color-primary)','var(--color-info)','var(--color-success)',  'var(--color-warning)' ,"var(--color-danger)"],

        },
        states: {
          hover: {
            filter: 'none'
          }
        },

        responsive: [{
          breakpoint: 991,
          options: {
            chart: {
              width: "100%",
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
    };
    var chart = new ApexCharts(document.querySelector("#platformReport"), options);
    chart.render();


</script>
@endpush




