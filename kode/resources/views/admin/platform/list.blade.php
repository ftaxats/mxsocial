@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.platform.bulk")}}' method="post">
                        @csrf
                        <input type="hidden" name="bulk_id" id="bulkid">
                        <input type="hidden" name="value" id="value">
                        <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('update_platform') )
                        <div class="col-md-6 d-flex justify-content-start gap-2">
                            <div class="i-dropdown bulk-action mx-0 d-none">
                                <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="las la-cogs fs-15"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    @if(check_permission('update_platform'))
                                        @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                            <li>
                                                <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                    <input name="search" value="{{request()->input('search')}}" type="search" placeholder="{{translate('Search by name ')}}">
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
                                @if(check_permission('update_platform'))
                                    <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                @endif
                                &nbsp;
                                {{translate('Name')}}
                            </th>
                            <th scope="col">{{translate('Total Accounts')}}</th>
                            <th scope="col">{{translate('Status')}}</th>
                            <th scope="col">{{translate('Feature')}}</th>
                            <th scope="col">{{translate('Is Integrated')}}</th>
                            <th scope="col">{{translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($platforms as $platform)
                            <tr>
                                <td data-label='{{translate("Name")}}'>
                                    <div class="user-meta-info d-flex align-items-center gap-2">
                                        @if( check_permission('update_platform'))
                                            <input  type="checkbox" value="{{$platform->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$platform->id}}" />
                                        @endif
                                        &nbsp;
                                        <img class="rounded-circle avatar-sm" src='{{imageURL(@$platform->file,"platform",true)}}' alt="{{@$platform->file->name}}">
                                        <p>	 {{ucfirst($platform->name)}}</p>
                                    </div>
                                </td>

                                <td data-label='{{translate("Total Account")}}'>
                                    <span>
                                        <a class="i-badge capsuled success" href="{{route('admin.social.account.list',['platform' => $platform->slug])}}">
                                          {{translate("Total Accounts")}} {{$platform->accounts_count}}
                                        </a>
                                    </span>
                                </td>
                                <td data-label='{{translate("Status")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_platform') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.platform.update.status') }}"
                                            data-status="{{ $platform->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$platform->uid}}" {{$platform->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$platform->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$platform->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Feature")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_platform') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="is_feature"
                                            data-route="{{ route('admin.platform.update.status') }}"
                                            data-status="{{ $platform->is_feature == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$platform->uid}}" {{$platform->is_feature ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-feature-{{$platform->id}}" >
                                        <label class="form-check-label" for="status-switch-feature-{{$platform->id}}"></label>
                                    </div>
                                </td>

                                <td data-label='{{translate("Integrated")}}'>

                                    @php  echo (intrgration_status($platform->is_integrated))  @endphp

                                </td>

                                <td data-label='{{translate("Action")}}'>
                                    <div class="table-action">
                                        @if(check_permission('update_platform') )
                                            @if(check_permission('update_platform'))
                                               @if($platform->is_integrated == App\Enums\StatusEnum::true->status())
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Add Account')}}"  href="{{route('admin.social.account.create',['platform' => $platform->slug])}}" class="fs-15 icon-btn info"><i class="las la-plus"></i>
                                                </a>
                                               @endif

                                                 @php
                                                     $url = url('/account/' . $platform->slug . '/callback' . ($platform->slug === 'tiktok' ? '' : '?medium=' . $platform->slug));
                                                 @endphp

                                                <a  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Configuration')}}" data-callback="{{$url}}" href="javascript:void(0);" data-id="{{$platform->id}}"  data-config = "{{collect($platform->configuration)}}" class="update-config fs-15 icon-btn danger"><i class="las la-tools"></i>
                                                </a>
                                                <a  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}"   href="javascript:void(0);" data-img ='{{imageURL(@$platform->file,"platform",true)}}'   data-platform = "{{$platform}}" class="update fs-15 icon-btn info"><i class="las la-pen"></i>
                                                </a>
                                            @endif
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
        </div>
    </div>

@endsection
@section('modal')

    <div class="modal fade" id="platform-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="platform-modal"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Update Platform')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.platform.update')}}" id="platformForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input   hidden name="id" type="text">

                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="image">
                                        {{translate('Image')}} <small class="text-danger">({{config("settings")['file_path']['platform']['size']}})</small>
                                    </label>
                                    <input data-size = "{{config('settings')['file_path']['platform']['size']}}" id="image" name="image" type="file" class="preview" >
                                    <div class="mt-2 image-preview-section">
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
                <form action="{{route('admin.platform.configuration.update')}}" id="platformConfigForm" method="post" enctype="multipart/form-data">
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
                                        <span class="input-group-text pointer copy-text pointer" data-type="modal"  data-text ='' ><i class="las la-copy"></i></span>
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
	(function($){
       	"use strict";

        $(document).on('click','.update',function(e){

            e.preventDefault()
            var platform = JSON.parse($(this).attr('data-platform'));
            var size = ($('#image').attr('data-size')).split("x");
            var modal = $('#platform-modal')
            modal.find('.modal-title').html("{{translate('Update Platform')}}")
            modal.find('input[name="id"]').val(platform.id)
            modal.modal('show')
        })

        $(document).on('click','.update-config',function(e){
            e.preventDefault()

            var config         = JSON.parse($(this).attr('data-config'));
            var id             = JSON.parse($(this).attr('data-id'));
            var callbackUrl    = ($(this).attr('data-callback'));
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

                                   <input value="${config[i]}" id='${convertedString}-${i}' required type="text" name="configuration[${i}]">
                                </div>`;

            }

            $("#configuration").html(html)
            $('#callbackUrl').val(callbackUrl)
            $('.copy-text').attr('data-text',callbackUrl)
            modal.modal('show')
        })

	})(jQuery);

</script>
@endpush





