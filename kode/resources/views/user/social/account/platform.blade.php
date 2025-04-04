@extends('layouts.master')
@section('content')

    @php
        $user = auth_user('web');
        $subscription = $user->runningSubscription;
        $accessPlatforms = (array) ($subscription ? @$subscription->package->social_access->platform_access : []);

        $platforms = get_platform()
                        ->whereIn('id', $accessPlatforms )
                        ->where("status",App\Enums\StatusEnum::true->status())
                        ->where("is_integrated",App\Enums\StatusEnum::true->status());

    @endphp

        <div class="i-card mb-4 border">
            <h4 class="card--title mb-4">
                 {{translate('Platform List')}}
            </h4>
            <ul class="account-connect-list">
                @forelse ($platforms as $platform )
                        <li>
                            <button>
                                <span><img  src='{{imageUrl(@$platform->file,"platform",true)}}' alt='{{$platform->name . " Preview image"}}'></span>{{$platform->name}}
                            </button>
                            <div class="button-group">
                                <a   href="{{route('user.social.account.create',['platform' => $platform->slug])}}" class="i-btn primary btn--sm capsuled">
                                    <i class="bi bi-plus-lg"></i>
                                    {{translate('Connect Account')}}
                                </a>
                            </div>
                        </li>
                @empty
                        <li class="no--access">
                            <div class="icon">
                                <svg  version="1.1"  width="50" height="50" x="0" y="0" viewBox="0 0 32 32" xml:space="preserve"><g><g data-name="Layer 2"><path d="M27.308 27.42H4.692a2.75 2.75 0 0 1-2.303-4.253L13.697 5.829a2.749 2.749 0 0 1 4.606 0L29.61 23.167a2.75 2.75 0 0 1-2.303 4.252zM16 6.08a1.23 1.23 0 0 0-1.047.567L3.645 23.986a1.25 1.25 0 0 0 1.047 1.933h22.616a1.25 1.25 0 0 0 1.047-1.933L17.047 6.648A1.23 1.23 0 0 0 16 6.081z"  opacity="1" data-original="#000000"></path><path d="M16 19.375a1 1 0 0 1-1-1v-6a1 1 0 0 1 2 0v6a1 1 0 0 1-1 1z"  opacity="1" data-original="#000000" ></path><circle cx="16" cy="22.375" r="1.25"  opacity="1" data-original="#000000" ></circle></g></g></svg>
                            </div>
                             <div><p>{{translate("You dont have any platform access")}}</p></div>
                        </li>
                @endforelse
            </ul>
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
                            <div class="col-lg-12" id ="accountConfig"></div>
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
