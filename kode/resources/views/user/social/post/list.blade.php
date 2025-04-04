@extends('layouts.master')
@section('content')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
    <div class="i-card-md">
        <div class="card-header">
            <h4 class="card-title">
                {{translate("Latest feeds")}}
           </h4>
             <div class="d-flex align-items-center gap-2">
                <a href="{{route('user.social.post.create')}}" class="i-btn primary btn--md capsuled">
                    <i class="bi bi-plus-lg"></i>
                     {{translate('Create New Post')}}
                </a>
                <button class="icon-btn icon-btn-lg info circle" type="button" data-bs-toggle="collapse" data-bs-target="#tableFilter" aria-expanded="false"
                    aria-controls="tableFilter">
                    <i class="bi bi-sliders"></i>
                </button>
            </div>
        </div>

        <div class="collapse {{ hasFilter(['date', 'account', 'status']) ? 'show' : '' }}" id="tableFilter">
            <div class="search-action-area pb-0">
                 <div class="search-area">
                    <form action="{{route(Route::currentRouteName())}}" method="get">
                        <div class="form-inner mb-0">
                            <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}"  placeholder='{{translate("Filter by date")}}'>
                        </div>

                        <div class="form-inner mb-0">
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
         
               

                        <div class="form-inner mb-0">
                            <select name="status" id="status" class="status">
                                <option value="">
                                    {{translate('Select Status')}}
                                </option>

                                @foreach(App\Enums\PostStatus::toArray() as $k => $v)
                                    <option  {{ (  !is_null(request()->input('status')) && $v  ==   request()->input('status')) ? 'selected' :""}} value="{{$v}}">   {{$k}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="i-btn primary btn--lg capsuled">
                                <i class="bi bi-search"></i>
                            </button>

                            <a href="{{route(Route::currentRouteName())}}"  class="i-btn danger btn--lg capsuled">
                                 <i class="bi bi-arrow-repeat"></i>
                            </a>
                        </div>
                    </form>
                 </div>
            </div>
        </div>

        <div class="card-body px-0">

            @if($posts->count()  > 0)
                <div class="table-container ">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">
                                    #
                                </th>
                                <th scope="col">{{translate('Platform')}}</th>
                                <th scope="col">{{translate('Account')}}</th>
                                <th scope="col">{{translate('Schedule Time')}}</th>
                                <th scope="col">{{translate('Send time')}}</th>
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
                                            <img class="rounded-circle avatar-sm" src='{{imageURL(@$post->account->platform->file,"platform",true)}}' alt="{{ translate('Platform preview image') }}">
                                            <p>	 {{$post?->account?->platform?->name ?? '-'}}</p>
                                        </div>
                                    </td>
                                    <td data-label='{{translate("Account")}}'>
                                        <div class="user-meta-info d-flex align-items-center gap-2">
                                            <img data-fallback="{{get_default_img()}}" class="rounded-circle avatar-sm" src='{{@$post->account->account_information->avatar }}' alt="{{ translate('Social profile image') }}"/>
                                            @if(@$post->account->account_information->link)
                                                <a target="_blank" href="{{@$post->account->account_information->link}}">
                                                    <p>	{{ @$post->account->account_information->name ?? '-'}}</p>
                                                </a>
                                            @else
                                                <p>	{{ @$post->account->account_information->name?? '-'}}</p>
                                            @endif
                                            @if( @$post?->platform_response && @$post?->platform_response?->url )
                                                <a class="i-badge success fs-15" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('View')}}" target="_blank"  href="{{@$post?->platform_response?->url}}"> {{translate("View Post")}}</a>
                                            @endif
                                        </div>
                                    </td>

                                    <td data-label='{{translate("Schedule time")}}'>
                                        {{@$post->schedule_time ? get_date_time($post->schedule_time ) : '--'}}
                                    </td>

                                    <td data-label='{{translate("Send time")}}'>
                                        {{@$post->updated_at ? diff_for_humans($post->updated_at ) : '--'}}
                                    </td>

                                    <td data-label='{{translate("Status")}}'>
                                        <div class="d-flex align-items-center gap-2">
                                            @php echo (post_status($post->status))   @endphp
                                            @if( $post->platform_response && @$post->platform_response->response )
                                            <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Info')}}" data-message="{{$post->platform_response->response }}" class="icon-btn icon-btn-sm show-info info">
                                                <i class="bi bi-info fs-4"></i>
                                            </a>
                                        @endif
                                        </div>
                                    </td>

                                    <td data-label='{{translate("Type")}}'>
                                        @php echo (post_type($post->post_type))   @endphp
                                    </td>

                                    <td data-label='{{translate("Action")}}'>
                                        <div class="table-action">
                                            <a  data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Show')}}"  href="{{route('user.social.post.show',['uid' => $post->uid])}}" class="icon-btn icon-btn-sm info"><i class="bi bi-eye"></i>
                                            </a>

                                            <a data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Delete')}}" href="javascript:void(0);"    data-href="{{route('user.social.post.destroy',  $post->id)}}" class="icon-btn icon-btn-sm danger delete-item">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty

                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                 @include('admin.partials.not_found',['custom_message' => 'No post found'])
            @endif

            <div class="Paginations">
                {{ $posts->links() }}
            </div>
        </div>
    </div>

@endsection
@section('modal')
  @include('modal.delete_modal')

  <div class="modal fade" id="report-info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="report-info"   aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{translate('Response Message')}}
                </h5>

                <button type="button" class="icon-btn icon-btn-sm danger" data-bs-dismiss="modal">
                   <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="content">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-include')
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
    <script  nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/init.js')}}"></script>
@endpush
@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";

        $(".user").select2({
            placeholder:"{{translate('Select user')}}",
        });

        $(".account").select2({  placeholder:"{{translate('Select account')}}",});
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





