@extends('admin.layouts.master')
@section('content')
<form class="settingsForm" enctype="multipart/form-data" novalidate method="post">
    @csrf
    <div class="i-card-md">
        <div class="card-body">
            <div class="row">
                 <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="webhook_api_key"
                        class="form-label">{{ translate('API Key') }}
                            <small class="text-danger" >*</small>
                        </label>
                        <div class="input-group">
                            <input id="webhook_api_key" value="{{site_settings('webhook_api_key')}}" name="site_settings[webhook_api_key]"  type="text" class="form-control" >
                            <span class="input-group-text  pointer key-generate"><i class="las la-sync"></i></span>
                        </div>
                    </div>
                 </div>
                 <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="webhook_url"
                        class="form-label">{{ translate('Webhook URL') }}
                        </label>
                        <div class="input-group">
                            <input readonly id="webhook_url" value="{{route('webhook')}}"  type="text" class="form-control" >
                            <span data-text ="{{route('webhook')}}" class="input-group-text  pointer copy-text"><i class="las la-copy"></i></span>
                        </div>
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

