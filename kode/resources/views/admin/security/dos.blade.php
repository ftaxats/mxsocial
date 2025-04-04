@extends('admin.layouts.master')
@section('content')
        
<div class="row g-4">
    <div class="col-xl-12">
        <div class="i-card-md">
            <div class="card-body">   
                <div class="d-flex align-items-center gap-3 mb-20">
                    <label for="dos_prevent">{{translate('Prevent Dos Attack')}}</label>
                    <div class="form-check form-switch form-switch-md" dir="ltr">
                        <input
                        {{ site_settings('dos_prevent') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                        type="checkbox" class="form-check-input status-update"
                        data-key='dos_prevent'
                        data-status='{{ site_settings('dos_prevent') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                        data-route="{{ route('admin.setting.update.status') }}" id="dos_prevent">
                        <label class="form-check-label mb-0" ></label>
                    </div>
                </div>
                <form action='{{route("admin.security.dos.update")}}' method="post">
                    @csrf
                    <div class="d-flex align-items-center flex-wrap d-dos-input">
                        <div class="form-inner d-flex align-items-center gap-2 me-4">
                            <label class="w-nowrap" > 
                                {{translate("If there are more than")}}
                            </label>
                            <input value='{{site_settings("dos_attempts")}}'  required type="number" name="site_settings[dos_attempts]" >
                        </div>
                        <div class="form-inner d-flex align-items-center gap-2">
                            <label class="w-nowrap" > 
                                {{translate("attempts in")}}
                            </label>

                            <input value='{{site_settings("dos_attempts_in_second")}}'  required type="number" name="site_settings[dos_attempts_in_second]" >

                            <label class="w-nowrap"> 
                                {{translate("second")}}
                            </label>
                        </div>
                    </div>
                    <div class="form-inner d-flex">
                        <div class="me-3">
                            <input class="form-check-input" {{site_settings("dos_security") == "captcha" ? "checked" :"" }} type="radio" name="site_settings[dos_security]" id="captcha" value="captcha">
                            <label class="form-check-label" for="captcha">
                                {{translate('Show Captcha')}}
                            </label>
                        </div>
                        <div>
                            <input class="form-check-input" type="radio" {{site_settings("dos_security") == "block_ip" ?  "checked" :"" }} name="site_settings[dos_security]" id="blokedIp" value="block_ip">
                            <label class="form-check-label" for="blokedIp">
                                {{translate('Block Ip')}}
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                        {{translate("Submit")}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>   

@endsection






