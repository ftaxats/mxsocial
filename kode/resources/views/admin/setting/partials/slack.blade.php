<form  class="settingsForm" enctype="multipart/form-data">
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
                        <label for="slack_channel">
                            {{translate("Slack Channel")}} <small class="text-danger" >({{translate("optional")}})</small>
                        </label>
                        <input type="text" name="site_settings[slack_channel]" id="slack_channel"  value="{{is_demo() ? '@@@' :site_settings('slack_channel')}}"  placeholder='{{translate("Slack Channel")}}'>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="slack_web_hook_url">
                            {{translate('Slack Web Hook URL')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[slack_web_hook_url]" id="slack_web_hook_url"  value="{{is_demo() ? '@@@' :site_settings('slack_web_hook_url')}}" required placeholder='{{translate("Slack Web Hook Url")}}'>
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