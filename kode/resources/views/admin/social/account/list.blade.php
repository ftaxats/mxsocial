@extends('admin.layouts.master')

@section('content')

    @php
        $platforms = get_platform()
                        ->where("status",App\Enums\StatusEnum::true->status())
                        ->where("is_integrated",App\Enums\StatusEnum::true->status());


    @endphp


    <div>
        <div class="basic-setting-left">
            <div class="sticky-side-div mb-4">
                <ul class="nav nav-tabs account-tab gap-sm-3 gap-2 social-account-list flex-row border-0" role="tablist">


                    @forelse ($platforms as $platform)
                        @if($platform->status == App\Enums\StatusEnum::true->status()  && $platform->is_integrated == App\Enums\StatusEnum::true->status() )
                            <li class="d-flex nav-item justify-content-between align-items-center flex-row-reverse gap-md-2 gap-1">
                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Configuration')}}"  data-callback="{{ url('/account/' . $platform->slug . '/callback' . ($platform->slug === 'tiktok' ? '' : '?medium=' . $platform->slug)) }}" href="javascript:void(0);" data-id="{{$platform->id}}"  data-config = "{{collect($platform->configuration)}}" class="update-config fs-15 icon-btn warning"><i class="las la-tools"></i>
                                </a>

                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Add Account')}}"  href="{{route('admin.social.account.create',['platform' => $platform->slug])}}" class="fs-15 icon-btn info"><i class="las la-plus"></i>
                                </a>

                                <a class="nav-link border-0 flex-grow-1 rounded-3 {{$platform->slug == request()->input('platform') ? 'active' :''}}"  href="{{route('admin.social.account.list',['platform' => $platform->slug])}}" >
                                    <div class="user-meta-info d-flex align-items-center gap-2">
                                        <img class="rounded-circle avatar-sm" src='{{imageURL(@$platform->file,"platform",true)}}' alt="{{@$platform->file->name}}">
                                        <p class="fs-13">	 {{$platform->name}}</p>
                                    </div>
                                </a>

                            </li>
                        @endif
                    @empty
                            <li class="text-center p-4">
                                {{translate("No Active Platform found")}}
                            </li>
                    @endforelse

                </ul>
            </div>
        </div>

        <div class="basic-setting-right">
            <div class="i-card-md">
                <div class="card-body">

                    <div class="search-action-area">
                        <div class="row g-3">
                            <form hidden id="bulkActionForm" action='{{route("admin.social.account.bulk")}}' method="post">
                                @csrf
                                <input type="hidden" name="bulk_id" id="bulkid">
                                <input type="hidden" name="value" id="value">
                                <input type="hidden" name="type" id="type">
                            </form>
                            @if(check_permission('create_account') || check_permission('update_account') || check_permission('delete_account') )
                                <div class="col-md-6 d-flex justify-content-start gap-2">
                                    @if(check_permission('update_account'))
                                        <div class="i-dropdown bulk-action mx-0 d-none">
                                            <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="las la-cogs fs-15"></i>
                                            </button>
                                                <ul class="dropdown-menu">

                                                    @if(check_permission('update_account'))
                                                        @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                            <li>
                                                                <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                            </li>
                                                        @endforeach
                                                    @endif

                                                </ul>

                                        </div>
                                        @endif

                                    @if(check_permission('create_account') &&  request()->input('platform'))
                                        <a href="{{route('admin.social.account.create',['platform' => request()->input('platform')])}}" class="i-btn btn--sm success create">
                                            <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                        </a>
                                    @endif


                                </div>
                            @endif
                            <div class="col-md-6 d-flex justify-content-end">
                                <div class="search-area">
                                    <form action="{{route(Route::currentRouteName())}}" method="get">

                                        <input type="hidden" name="platform" value="{{request()->input('platform')}}">
                                            <div class="form-inner">

                                                <input placeholder="{{translate('Search by name')}}" type="search" name="name" value="{{request()->input('value')}}">

                                            </div>
                                        <button class="i-btn btn--sm info">
                                            <i class="las la-sliders-h"></i>
                                        </button>
                                        <a href="{{route('admin.social.account.list',['platform' => request()->input('platform')])}}"  class="i-btn btn--sm danger">
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
                                    @if(check_permission('update_account') || check_permission('delete_account'))
                                        <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                    @endif#
                                </th>
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
                                            @if( check_permission('update_account') || check_permission('delete_account'))
                                                <input  type="checkbox" value="{{$account->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$account->id}}" />
                                            @endif
                                            {{$loop->iteration}}
                                        </td>

                                        <td data-label='{{translate("name")}}'>


                                            <div class="user-meta-info d-flex align-items-center gap-2">
                                                <img data-fallback={{get_default_img()}} class="rounded-circle avatar-sm"  src='{{@$account->account_information->avatar}}'    alt="{{translate('Social Profile image')}}" >

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
                                            @if(!$account->user_id)
                                                <div class="form-check form-switch switch-center">
                                                    <input {{!check_permission('update_account') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                                        data-column="status"
                                                        data-route="{{ route('admin.social.account.update.status') }}"
                                                        data-status="{{ $account->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                                        data-id="{{$account->uid}}" {{$account->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                                    id="status-switch-{{$account->id}}" >
                                                    <label class="form-check-label" for="status-switch-{{$account->id}}"> </label>

                                                </div>
                                            @else
                                                --
                                            @endif

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

                                                @if($account->is_connected ==  App\Enums\StatusEnum::false->status() && $account->platform->slug != 'twitter' &&  !$account->user_id)
                                                    @php

                                                        $url          = 'javascript:void(0)';
                                                        $connectionClass  =   true;

                                                        if($account->platform->slug != 'facebook'){
                                                            $url   = route("account.connect",
                                                                                  [ "guard"=>"admin" , "medium" => $account->platform->slug ,"type" => t2k(App\Enums\AccountType::PROFILE->name) ]);
                                                            $connectionClass  =   false;
                                                        }

                                                    @endphp
                                                    <a    data-account = "{{$account}}" data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Reconnect')}}"  href="{{$url}}" class=" {{$connectionClass ? 'reconnect' : ''}}  fs-15 icon-btn danger"><i class="las la-plug"></i>
                                                    </a>
                                                    @endif

                                                @if(isset($platformConfig['view_option']) && $account->is_official == App\Enums\ConnectionType::OFFICIAL->value  )
                                                        <a data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Show')}}"  href="{{route('admin.social.account.show',['uid' => $account->uid])}}" class="fs-15 icon-btn success"><i class="las la-eye"></i>
                                                        </a>
                                                @endif
                                                @if(check_permission('delete_account') )
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Delete')}}" href="javascript:void(0);"    data-href="{{route('admin.social.account.destroy',  $account->id)}}" class="pointer delete-item icon-btn danger">
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
                                        <td class="border-bottom-0" colspan="7">
                                            @include('admin.partials.not_found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>


                        </table>
                    </div>

                    <div class="Paginations">
                        {{ $accounts->links() }}
                    </div>

                </div>
            </div>
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
                <form action="{{route('admin.social.account.reconnect')}}" id="connectAccount" method="post" enctype="multipart/form-data">
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

    <div class="modal fade" id="config-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="config-modal"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Update Configuration')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.platform.configuration.update')}}" id="platformForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input   hidden name="id" type="text">
                            <div class="col-lg-12" id ="configuration">
                            </div>
                            <div class="col-xl-12">
                                <div class="form-inner">
                                    <label for="callbackUrl">
                                        {{translate('Callback URL')}}
                                    </label>
                                    <div class="input-group">
                                        <input id="callbackUrl"  readonly  type="text" class="form-control" >

                                        <span class="input-group-text pointer copy-text pointer" data-type="modal"
                                        data-text ='' >
                                            <i class="las la-copy"></i>
                                        </span>
                                    </div>
                                </div>
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
   $(".user").select2({});



    $(document).on('click','.update-config',function(e){
        e.preventDefault()
        var config         = JSON.parse($(this).attr('data-config'));
        var id             = JSON.parse($(this).attr('data-id'));
        var callbackUrl    = ($(this).attr('data-callback'));
        var is_verified    = ($(this).attr('data-isVerified'));
        var modal          = $('#config-modal')

        modal.find('input[name="id"]').val(id)
        var html = "";
        for(let i in config){
            var withoutUnderscores =  i.replace(/_/g, ' ');
            var convertedString = withoutUnderscores.replace(/\b\w/g, function (match) {
                return match.toUpperCase();
            });

            html+= `<div class="form-inner">
                                <label for="${convertedString}-${i}" class="form-label" >
                                    ${convertedString}  <span  class="text-danger">*</span>
                                </label>

                            <input value="${config[i]}"  id='${convertedString}-${i}' required type="text" name="configuration[${i}]">
                            </div>`;

        }

        $("#configuration").html(html)
        $('#callbackUrl').val(callbackUrl)
        $('.copy-text').attr('data-text',callbackUrl)
        modal.modal('show')
    })

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
