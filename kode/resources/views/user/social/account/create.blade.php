@extends('layouts.master')
@section('content')

@section('content')

@php
    $accountTypes = App\Enums\AccountType::toArray();

    if($platform->slug != 'facebook' ) Arr::forget($accountTypes,['PAGE','GROUP']);
    $enumClassPrefix = ucfirst($platform->slug);
    $enumClass  = "App\\Enums\\{$enumClassPrefix}Connection";
    $connectionTypes = App\Enums\ConnectionType::toArray();
    if (class_exists($enumClass))  $connectionTypes = $enumClass::toArray();     $platforms = Arr::get(config('settings'),'platforms' ,[]);
    $platformConfig = Arr::get($platforms,$platform->slug ,null);

    if(isset($platformConfig['unofficial'])) Arr::forget($connectionTypes, App\Enums\ConnectionType::UNOFFICIAL->name);

    if(isset($platformConfig['official'])) Arr::forget($connectionTypes, App\Enums\ConnectionType::OFFICIAL->name);

    $inputs = Arr::get(config('settings.platforms_connetion_field'),$platform->slug,[]);

@endphp

<form action="{{route('user.social.account.store')}}" class="add-listing-form" enctype="multipart/form-data"
    method="post">
    @csrf
    <input hidden name="platform_id" value="{{$platform->id}}">
    <div class="i-card-md">
        <div class="card-header">
            <h4 class="card-title">
                {{($platform->name)}}
            </h4>

            <div>
                <a class="i-btn btn--md capsuled info"
                    href="{{route('user.social.account.list',['platform' => $platform->slug])}}">
                    {{translate("View Accounts")}}
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="connect-profile-tab">
                <ul class="nav nav-tabs mb-5" role="tablist">
                    @foreach( $connectionTypes as $k => $v)
                    <li class="nav-item" role="presentation">
                        <button class='nav-link
                                {{$loop->index == 0 ? "active" :""}}
                                ' id="lang-tab-{{t2k($k)}}" data-bs-toggle="pill"
                            data-bs-target="#lang-tab-content-{{t2k($k)}}" type="button" role="tab"
                            aria-controls="lang-tab-content-{{t2k($k)}}" aria-selected="true">
                            <img class="avatar-sm rounded-circle me-2"
                                src="{{imageURL(@$platform->file,'platform',true)}}" alt="{{$platform->name.' Preview image'}}">

                            <span>
                                {{ucfirst(strtolower(k2t($k)))}}
                            </span>
                        </button>
                    </li>
                    @endforeach
                </ul>

                <div id="{{$platform->slug}}" class="tab-content">
                    @foreach($connectionTypes as $k => $v)
                        <div class='tab-pane fade {{$loop->index == 0 ? " show active" :""}} '
                            id="lang-tab-content-{{t2k($k)}}" role="tabpanel">
                            <div class="row align-items-start gy-5">
                                <div class="col-lg-8">

                                    @if( $v == App\Enums\ConnectionType::UNOFFICIAL->value)
                                        <div class="form-inner">
                                            <label for="account_type">
                                                {{translate("Accout type")}} <span class="text-danger">*</span>
                                            </label>
                                            <select name="account_type" id="account_type">
                                                @foreach ($accountTypes as $type => $value )
                                                    <option {{  (old("account_type") && old("account_type")  == $value)  ? "selected" :""}} value="{{$value}}">
                                                        {{$type}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    @if($v == App\Enums\ConnectionType::UNOFFICIAL->value)

                                        <div class="form-inner d-none page-id">
                                            <label for="page_id">
                                                {{translate("Page ID")}} <span class="text-danger">*</span>
                                            </label>
                                            <input id="page_id" type="text" name="page_id"
                                                placeholder='{{translate("Enter Page ID")}}' value="{{old('page_id')}}">
                                        </div>

                                        <div class="form-inner d-none  group-id">
                                            <label for="group_id">
                                                {{translate("Group ID")}} <span class="text-danger">*</span>
                                            </label>
                                            <input id="group_id" type="text" name="group_id"
                                                placeholder='{{translate("Enter group ID")}}' value="{{old('group_id')}}">
                                        </div>

                                        @foreach ($inputs as $key )
                                            <div class="form-inner">
                                                <label for="{{$key}}">
                                                    {{translate(k2t($key))}} <span class="text-danger">*</span>
                                                </label>
                                                <input required id="{{$key}}" type="text" name="{{$key}}"
                                                    placeholder=' {{translate(k2t($key))}}' value="{{old('access_token')}}">
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="text-center mt-4">
                                        @if($v != App\Enums\ConnectionType::UNOFFICIAL->value)
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a @if($platform->slug == 'facebook') data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{trans("default.facebook_profile_warning_note")}}"  @endif href='{{route("account.connect",[ "guard"=>"web","medium" => $platform->slug ,"type" => t2k(App\Enums\AccountType::PROFILE->name) ])}}'
                                                class="i-btn btn--primary btn--lg capsuled">
                                                {{translate('Connect Account')}}
                                                <i class="bi bi-link-45deg"></i>
                                            </a>
                                        </div>
                                        @else
                                        <button type="submit" class="i-btn btn--primary btn--lg capsuled">
                                            {{translate('Connect')}}
                                            <i class="bi bi-link-45deg"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4 ps-lg-4">
                                    <div class="p-4 bg-danger-soft rounded-4">
                                        <p><span class="i-badge-solid danger me-2">{{translate("note")}} :</span>
                                            @if($v != App\Enums\ConnectionType::UNOFFICIAL->value)
                                              {{trans("default.on_click_note")}}
                                            @else
                                               {{trans("default.tokenize_note")}}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

@endsection


@section('modal')

    @include('modal.delete_modal')
    <div class="modal fade" id="warning-note-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="warning-note-modal"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Warning note')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>


                <div class="modal-body">
                    <div class="p-4 bg-danger-soft rounded-4">
                        <p><span class="i-badge-solid danger me-2">{{translate("note")}} :</span>
                            {{trans("default.facebook_profile_warning_note")}}
                        </p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="i-btn btn--md ripple-dark" data-anim="ripple" data-bs-dismiss="modal">
                        {{translate("Close")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script-push')
<script nonce="{{ csp_nonce() }}">
(function($) {
    "use strict";

    var warningModal = $("#warning-note-modal");

    $("#account_type").select2({
        placeholder: "{{translate('Select type')}}",
    })

    inputControl( $("#account_type").val())
    @if(old("account_type"))
      inputControl('{{old("account_type")}}')
    @endif

    $(document).on('change', '#account_type', function(e) {
        var val = $(this).val();
        inputControl(val)
        e.preventDefault()
    })

    function inputControl(val) {
        if(val){
            if (val == "{{App\Enums\AccountType::GROUP->value}}") {
                $('.page-id').addClass('d-none');
                $('.group-id').removeClass('d-none');
            } else if (val == "{{App\Enums\AccountType::PAGE->value}}") {
                $('.page-id').removeClass('d-none');
                $('.group-id').addClass('d-none');

            } else {
                if(warningModal && warningModal.length) {
                    warningModal.modal('show')
                }
                $('.page-id').addClass('d-none');
                $('.group-id').addClass('d-none');
            }

        }
    }
})(jQuery);
</script>
@endpush
