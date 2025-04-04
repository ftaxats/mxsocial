<form class="settingsForm" data-route="{{route('admin.setting.plugin.store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                {{  Arr::get($tab,'title') }}
            </h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2 flex-wrap">

                <div class="form-group form-check form-check-success">
                    <input {{ site_settings('default_recaptcha') == App\Enums\StatusEnum::true->status() ? 'checked' :"" }} type="checkbox" class="form-check-input status-update"
                    data-key ='default_recaptcha'
                    data-status ='{{ site_settings('default_recaptcha') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() :App\Enums\StatusEnum::true->status()}}'
                    data-route="{{ route('admin.setting.update.status') }}"  id="defaultCaptcha" >
                    <label class="form-check-label mb-0" for="defaultCaptcha">
                        {{translate("Use Default Captcha")}}
                    </label>
                </div>

                <div class="form-group form-check form-check-success">
                    <input {{ site_settings('captcha_with_registration') == App\Enums\StatusEnum::true->status() ? 'checked' :"" }} type="checkbox" class="form-check-input status-update"
                    data-key ='captcha_with_registration'
                    data-status ='{{ site_settings('captcha_with_registration') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() :App\Enums\StatusEnum::true->status()}}'
                    data-route="{{ route('admin.setting.update.status') }}"  id="captcha_with_registration" >
                    <label class="form-check-label mb-0" for="captcha_with_registration">
                        {{translate("Captcha With Registration")}}
                    </label>
                </div>
                
                <div class="form-group form-check form-check-success">
                    <input {{ site_settings('captcha_with_login') == App\Enums\StatusEnum::true->status() ? 'checked' :"" }} type="checkbox" class="form-check-input status-update"
                    data-key ='captcha_with_login'
                    data-status ='{{ site_settings('captcha_with_login') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() :App\Enums\StatusEnum::true->status()}}'
                    data-route="{{ route('admin.setting.update.status') }}"  id="captcha_with_login" >
                    <label class="form-check-label mb-0" for="captcha_with_login">
                        {{translate("Captcha With Login")}}
                    </label>
                </div>
            </div>
            <div class="mt-20">
                <h6 class="mb-20">
                    {{translate('Google Recaptcha (V3)')}}
                </h6>
                <div class="row google-captcha">
                    @foreach($google_recaptcha as $key => $settings)
                        <div class="col-xl-6">
                            <div class="form-inner">
                                <label for="{{$key}}">
                                    {{
                                        Str::ucfirst(str_replace("_"," ",$key))
                                    }}  <small class="text-danger" >*</small>
                                </label>
                                @if($key == 'status')
                                <select class="select2"  name='site_settings[google_recaptcha][{{$key}}]'  id="{{$key}}" >
                                    @foreach( App\Enums\StatusEnum::toArray() as $key => $val)
                                        <option {{$settings ==  $val ? 'selected' :""}}  value="{{$val}}">
                                            {{$key}}
                                        </option>
                                    @endforeach
                                </select>
                                @else
                                <input id="{{$key}}" required  value="{{is_demo() ? '@@@' :$settings}}" name='site_settings[google_recaptcha][{{$key}}]' placeholder="************" type="text">
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="col-12 ">
                        <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                            {{translate("Submit")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>