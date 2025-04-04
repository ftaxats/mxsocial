<form data-route="{{route('admin.setting.store')}}"  class="settingsForm"  method="POST" enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                  {{  Arr::get($tab,'title') }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-inner">
                        <label for="loginWith">
                            {{translate('Login With')}}
                            <small class="text-danger" >*</small>
                        </label>
                        <select class="select2" required multiple id="loginWith" name="site_settings[login_with][]">
                            @foreach($loginAttributes  as $auth )
                                    <option @if(in_array($auth ,$authSetup?? [] )) selected @endif   value="{{$auth}}">{{$auth}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(site_settings('login_attempt_validation') == App\Enums\StatusEnum::true->status())
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="max_login_attemtps">
                                {{translate('Maximum Login Attempts')}} <small class="text-danger" >*({{translate('Per Minute')}})</small>
                            </label>
                            <input type="number" name="site_settings[max_login_attemtps]" id="max_login_attemtps"  value="{{site_settings('max_login_attemtps')}}" required placeholder="max_login_attemtps">
                        </div>
                    </div>
                @endif
                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="otp_expired_in">
                            {{translate('OTP expired in')}} <small class="text-danger" >*({{translate('Second')}})</small>
                        </label>
                        <input type="number" name="site_settings[otp_expired_in]" id="otp_expired_in"  value="{{site_settings('otp_expired_in')}}" required placeholder='{{translate("Otp expired in")}}'>
                    </div>
                </div>
                <div class="col-lg-12 d-none otp-activation">
                    <div class="form-inner">
                        <label for="otpVerification">
                            {{translate('Mobile OTP Verification')}}
                            <span class="text-danger" >*</span>
                        </label>
                        <select class="select2" name="site_settings[sms_otp_verification]" id="otpVerification">
                            @foreach( App\Enums\StatusEnum::toArray() as $key => $val)
                                <option {{site_settings('sms_otp_verification') ==  $val ? 'selected' :""}}  value="{{$val}}">
                                    {{$key}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                    {{translate("Submit")}}
                </button>
            </div>
        </div>
    </div>
</form>