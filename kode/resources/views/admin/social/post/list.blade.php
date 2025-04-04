@extends('admin.layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    @if(check_permission('create_post') )
                        <div class="col-md-2 d-flex justify-content-start">
                            <div class="action">
                                <a href="{{route('admin.social.post.create')}}" class="i-btn btn--sm success">
                                    <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                </a>
                            </div>

                        </div>
                    @endif

                    <div class="col-md-10 d-flex justify-content-end">
                        <div class="search-area">
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
                                    <select name="account" id="accounts" class="account">
                                        <option value="">
                                            {{translate('Select Account')}}
                                        </option>

                                        @foreach($accounts as $account)
                                            <option  {{$account->account_id ==   request()->input('account') ? 'selected' :""}} value="{{$account->account_id}}"> {{$account->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-inner">
                                    <select name="status" id="status" class="status">
                                        <option value="">
                                            {{translate('Select Status')}}
                                        </option>

                                        @foreach(App\Enums\PostStatus::toArray() as $k => $v)
                                            <option  {{$v  ==   request()->input('status',-1) ? 'selected' :""}} value="{{$v}}">   {{$k}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route(Route::currentRouteName())}}"  class="i-btn btn--sm danger">
                                    <i class="las la-sync"></i>
                                </a>
                            </form>
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
                            <th scope="col">{{translate('Platform')}}</th>
                            <th scope="col">{{translate('Account')}}</th>
                            <th scope="col">{{translate('User')}}</th>
                            <th scope="col">{{translate('Admin')}}</th>
                            <th scope="col">{{translate('Schedule Time')}}</th>
                            <th scope="col">{{translate('Created time')}}</th>
                            <th scope="col">{{translate('Status')}}</th>
                            <th scope="col">{{translate('Post Type')}}</th>
                            <th scope="col">{{translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($posts as $post)
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
                                        <img class="rounded-circle avatar-sm" data-fallback="{{get_default_img()}}"
                                         src='{{@$post->account->account_information->avatar }}' alt="{{translate('Social profile image')}}">

                                        @if(@$post->account->account_information->link)
                                            <a target="_blank" href="{{@$post->account->account_information->link}}">
                                                <p>	{{ @$post->account->account_information->name}}</p>
                                            </a>
                                        @else
                                            <p>	{{ @$post->account->account_information->name}}</p>
                                        @endif
                                        @if( $post->platform_response && @$post->platform_response?->url )
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
                                        {{ '--'}}
                                    @endif

                                </td>

                                <td data-label='{{translate("Admin")}}'>
                                    {{@$post->admin ? @$post->admin->name : '--'}}
                                </td>

                                <td data-label='{{translate("Schedule time")}}'>
                                    {{@$post->schedule_time ? get_date_time($post->schedule_time ) : '--'}}
                                </td>

                                <td data-label='{{translate("Created time")}}'>
                                    {{@$post->updated_at ? diff_for_humans($post->updated_at ) : ('--')}}
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

                                        @if($post->status !=  App\Enums\PostStatus::SUCCESS->value)
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Post now')}}"  href="
                                                {{route('admin.social.post.send',['uid' => $post->uid])}}" class="fs-15 icon-btn info">
                                                   <i class="las la-paper-plane"></i>
                                                </a>
                                        @endif

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
                                <td class="border-bottom-0" colspan="10">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="Paginations">
                {{ $posts->links() }}
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
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/init.js')}}"></script>
@endpush
@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";

        $(".user").select2({});
        $(".account").select2({});
        $(".status").select2({});


        $(document).on('click','.show-info',function(e){
            e.preventDefault()
            var modal = $('#report-info');

            var report = $(this).attr('data-message')

            var cleanContent = DOMPurify.sanitize(report);

            $('.content').html(cleanContent)

            modal.modal('show')
        });

	})(jQuery);

</script>
@endpush





