<form   class="settingsForm"   enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                {{  Arr::get($tab,'title') }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="api_route_rate_limit" >
                            {{translate('API Hit limit')}} <small class="text-danger" >*({{translate('Per Minute')}})</small>
                        </label>
                        <input type="number" name="site_settings[api_route_rate_limit]" id="api_route_rate_limit"  value="{{site_settings('api_route_rate_limit')}}" required placeholder="api_route_rate_limit">
                    </div>
                </div>
                <div class="col-lg-6 ">
                    <div class="form-inner">
                        <label for="web_route_rate_limit" >
                            {{translate('Web Route limit')}} <small class="text-danger" >*({{translate('Per Minute')}})</small>
                        </label>
                        <input type="number" name="site_settings[web_route_rate_limit]" id="web_route_rate_limit" value="{{site_settings('web_route_rate_limit')}}" required placeholder="web_route_rate_limit">
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