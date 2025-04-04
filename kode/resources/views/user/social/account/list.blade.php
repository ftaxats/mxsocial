@php use Illuminate\Support\Arr; @endphp
@extends('layouts.master')
@section('content')
    @php
        $user                   = auth_user('web');
        $subscription           = $user->runningSubscription;
        $accessPlatforms         = (array) ($subscription ? @$subscription->package->social_access->platform_access : []);
        $platforms = get_platform()
            ->whereIn('id', $accessPlatforms )
            ->where("status",App\Enums\StatusEnum::true->status())
            ->where("is_integrated",App\Enums\StatusEnum::true->status());
    @endphp

    <div>
        @if($platforms->count() > 0)
            <div class="i-card mb-4 border">
                <ul class="social-account-list-2">
                    @forelse ($platforms as $platform )
                        <li>
                            <a class="{{$platform->slug == request()->input('platform') ? 'active' :''}}" href="{{route('user.social.account.list',['platform' => $platform->slug])}}">
                                <span>
                                    <img  src='{{imageUrl(@$platform->file,"platform",true)}}' alt="{{ $platform->name .' Image preview' }}">
                                </span>
                                {{$platform->name}}
                            </a>
                        </li>
                    @empty

                    @endforelse

                </ul>
            </div>
        @endif


        <div class="i-card-md">
            <div class="card-header">
                <h4 class="card-title">
                    {{translate(Arr::get($meta_data,'title'))}}
                </h4>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    @if(request()->input("platform"))
                        <a   href="{{route('user.social.account.create',['platform' => request()->input('platform')])}}" class="i-btn primary btn--md capsuled">
                            <i class="bi bi-plus-lg"></i>
                            {{translate('Add New')}}
                        </a>
                    @endif

                    <button class="icon-btn icon-btn-lg info circle" type="button" data-bs-toggle="collapse" data-bs-target="#tableFilter" aria-expanded="false"
                        aria-controls="tableFilter">
                        <i class="bi bi-sliders"></i>
                    </button>
               </div>
           </div>

           <div class="collapse  {{ hasFilter(['name']) ? 'show' : '' }}" id="tableFilter">
                <div class="search-action-area pb-0">
                    <div class="search-area">
                        <form action="{{ route(Route::currentRouteName()) }}" method="get">
                            <input type="hidden" name="platform" value="{{request()->input('platform')}}">
                            <div class="form-inner">
                                <input placeholder="{{translate('Filter by name')}}" type="search" name="name"
                                    value="{{request()->input('name')}}">
                            </div>

                            <div class="d-flex gap-2">
                                <button class="i-btn primary btn--lg capsuled">
                                    <i class="bi bi-search"></i>
                                </button>
                                <a href="{{route('user.social.account.list',['platform' => request()->input('platform')])}}" class="i-btn danger btn--lg capsuled">
                                    <i class="bi bi-arrow-repeat"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body px-0">
                @if($accounts->count() >  0)
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{translate('Account Info')}}</th>
                                    <th scope="col">{{translate('Status')}}</th>
                                    <th scope="col">{{translate('Connection Status')}}</th>
                                    <th scope="col">{{translate('Connection Type')}}</th>
                                    <th scope="col">{{translate('Account Type')}}</th>
                                    <th scope="col">{{translate('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($accounts as $account)
                                    <tr>
                                        <td data-label="#">
                                            {{$loop->iteration}}
                                        </td>
                                        <td data-label='{{translate("name")}}'>
                                            <div class="user-meta-info d-flex align-items-center gap-2">
                                                <img
                                                    data-fallback="{{ get_default_img() }}"
                                                    class="rounded-circle avatar-sm"
                                                    src="{{ @$account->account_information->avatar }}"
                                                    alt="{{ translate('Social profile image') }}">
                                                @if(@$account->account_information->link)
                                                    <a target="_blank" href="{{@$account->account_information->link}}">
                                                        <p>	{{ @$account->account_information->name}}</p>
                                                    </a>
                                                @else
                                                    <p>	{{ @$account->account_information->name}}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label='{{translate("Status")}}'>
                                            <div class="form-check form-switch switch-center">
                                                <input  type="checkbox" class="status-update form-check-input"
                                                    data-column="status"
                                                    data-route="{{ route('user.social.account.update.status') }}"
                                                    data-status="{{ $account->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                                    data-id="{{$account->uid}}" {{$account->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                                id="status-switch-{{$account->id}}" >
                                                <label class="form-check-label" for="status-switch-{{$account->id}}"> </label>
                                            </div>
                                        </td>
                                        <td data-label='{{translate("Connection Status")}}'>
                                            @php echo (account_connection_status($account->is_connected)) @endphp
                                        </td>
                                        <td data-label='{{translate("Connection Type")}}'>
                                            @php echo (account_connection($account->is_official)) @endphp
                                        </td>
                                        <td data-label='{{translate("Account Type")}}'>
                                            @php echo (account_type($account->account_type)) @endphp
                                        </td>
                                        <td data-label='{{translate("Action")}}'>
                                            <div class="table-action">
                                                @php
                                                    $platforms           = Arr::get(config('settings'),'platforms' ,[]);
                                                    $platformConfig      = Arr::get($platforms,$account->platform->slug ,null);
                                                @endphp

                                                @if($account->is_connected ==  App\Enums\StatusEnum::false->status() && $account->platform->slug != 'twitter' )
                                                    @php
                                                        $url = 'javascript:void(0)';
                                                        $connectionClass  =   true;
                                                        if($account->platform->slug != 'facebook'){
                                                            $url = route("account.connect",[ "guard"=>"web","medium" => $account->platform->slug ,"type" => t2k(App\Enums\AccountType::PROFILE->name) ]);
                                                            $connectionClass  =   false;
                                                        }
                                                    @endphp
                                                    <a data-account = "{{$account}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Reconnect')}}"  href="{{$url}}" class=" {{$connectionClass ? 'reconnect' : ''}}  icon-btn icon-btn-sm danger"><i class="bi bi-plug"></i>
                                                    </a>
                                                @endif

                                                @if(isset($platformConfig['view_option']) && $account->is_official == App\Enums\ConnectionType::OFFICIAL->value  )
                                                        <a  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Show')}}"  href="{{route('user.social.account.show',['uid' => $account->uid])}}" class="icon-btn icon-btn-sm  success"><i class="bi bi-eye"></i>
                                                        </a>
                                                @endif
                                                @if(check_permission('delete_account') )
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);"    data-href="{{route('user.social.account.destroy',  $account->id)}}" class="icon-btn icon-btn-sm danger delete-item">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                @else
                                                    --
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="border-bottom-0" colspan="7">
                                            @include('admin.partials.not_found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                       @include('admin.partials.not_found',['custom_message' => 'No accounts found'])
                @endif
            </div>
        </div>

        <div class="Paginations">
            {{ $accounts->links() }}
        </div>
    </div>

@endsection


@section('modal')
    @include('modal.delete_modal')
    <div class="modal fade" id="reconnect-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="reconnect-modal"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Reconnect Account')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('user.social.account.reconnect')}}" id="platformForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input   hidden name="id" type="text">
                            <div class="col-lg-12" id ="accountConfig">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="i-btn btn--md ripple-dark" data-anim="ripple" data-bs-dismiss="modal">
                            {{translate("Close")}}
                        </button>
                        <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                            {{translate("Submit")}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
  "use strict";
   $(".user").select2({
    });

    $(document).on('click','.reconnect',function(e){
        e.preventDefault()
        var account        = JSON.parse($(this).attr('data-account'));
        var id             = account.id;

        var modal          = $('#reconnect-modal')
        modal.find('input[name="id"]').val(id)
        var html = "";

        html+= `<div class="form-inner">
                    <label for="token" class="form-label" >
                        {{translate('Access Token')}}  <span  class="text-danger">*</span>
                    </label>

                   <input value="${account.account_information.token}" required type="text" name="access_token">
                </div>`;
        $("#accountConfig").html(html)
        modal.modal('show')
    })
</script>
@endpush
