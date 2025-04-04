<form   class="settingsForm"   enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <div>
                <h4 class="card-title">
                    {{  Arr::get($tab,'title') }}
                </h4>
            </div>
        </div>
        <div class="card-body">
            <p class="mb-3">
                {{trans('default.loggin_note')}}
            </p>
            <div class="row g-3">
                <div class="col-xl-6 ">
                    <div class="form-inner">
                        <label for="sentry_dns">
                            {{translate('Sentry DNS')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[sentry_dns]" id="sentry_dns"  value="{{is_demo() ? '@@@' :site_settings('sentry_dns')}}" required placeholder='{{translate("Enter Dns")}}'>
                    </div>
                </div>
                <div class="col-xl-6 ">
                    <div class="module-note">
                        <h6 class="mb-2">
                            {{translate('Information')}}
                        </h6>
                        <p>
                            <a href="https://sentry.io" target="_blank">{{translate("Sentry")}}
                            </a>
                            <span>
                                {{trans('default.sentry_note')}}
                            </span>
                        </p>
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