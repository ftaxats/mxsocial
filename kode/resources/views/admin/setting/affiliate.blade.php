@extends('admin.layouts.master')
@section('content')
<form  class="settingsForm" enctype="multipart/form-data" novalidate method="post">
    @csrf
    <div class="i-card-md">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="affiliate_system"
                        class="form-label">{{ translate('Affiliate System') }}
                            <small class="text-danger" >*</small>
                        </label>
                        <select name="site_settings[affiliate_system]" id="affiliate_system" class="select2">
                            @foreach (App\Enums\StatusEnum::toArray() as $key => $val )
                                <option value="{{$val}}" {{site_settings("affiliate_system") == $val  ? "selected" :""}} >
                                    {{ $key }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                 </div>
                 <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="continuous_commission"
                        class="form-label">{{ translate('Allow Commission Continuously') }}
                            <small class="text-danger" >*</small>
                        </label>
                        <select name="site_settings[continuous_commission]" id="continuous_commission" class="select2">
                            @foreach (App\Enums\StatusEnum::toArray() as $key => $val )
                                <option value="{{$val}}" {{site_settings("continuous_commission") == $val  ? "selected" :""}} >
                                    {{ $key }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text--primary">
                            {{translate("If enabled, user will get commission for each subscriptions of referred user. Otherwise only for the first subscription.
                            ")}}
                        </small>
                    </div>
                 </div>
                <div class="col-12 ">
                    <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                        {{translate("Submit")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
  "use strict";

    $(".select2").select2({
    });
</script>
@endpush
