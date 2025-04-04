<form  data-route="{{route('admin.setting.logo.store')}}"  class="settingsForm"  method="POST" enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                {{  Arr::get($tab,'title') }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach (Arr::get(config('settings'),'logo_keys' ,[]) as $logoKey )
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="{{$logoKey}}">
                                {{(k2t($logoKey))}} <small class="text-danger" >* ({{config("settings")['file_path'][$logoKey]['size']}})</small>
                            </label>
                            <input type="file" name="site_settings[{{$logoKey}}]" id="{{$logoKey}}" class="preview" data-size = "{{config('settings')['file_path'][$logoKey]['size']}}">
                            <div class="mt-2 image-preview-section logo-preview">
                                <img src="{{imageURL(@site_logo($logoKey)->file,$logoKey,true)}}" alt="{{$logoKey.'.jpg'}}" class="fav-preview">
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-12">
                    <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                        {{translate("Submit")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>