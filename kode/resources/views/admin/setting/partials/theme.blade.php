<form  class="settingsForm" enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                {{  Arr::get($tab,'title') }}
            </h4>
            <button type="button" class="i-btn btn--sm danger reset-color">
                <i class="las la-sync"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="primary_color">
                            {{translate('Primary Color')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[primary_color]" id="primary_color" class="colorpicker" value="{{site_settings('primary_color')}}" required >
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="secondary_color">
                            {{translate('Secondary Color')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[secondary_color]" id="secondary_color" class="colorpicker" value="{{site_settings('secondary_color')}}" required >
                    </div>
                </div>

                  
                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="btn_text_primary">
                            {{translate('Primary Color Text')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[btn_text_primary]" id="btn_text_primary" class="colorpicker" value="{{site_settings('btn_text_primary')}}" required >
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="btn_text_secondary">
                            {{translate('Secondary Color Text')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[btn_text_secondary]" id="btn_text_secondary" class="colorpicker" value="{{site_settings('btn_text_secondary')}}" required>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="text_primary_color">
                            {{translate('Body Text Primary')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[text_primary]" id="text_primary_color" class="colorpicker" value="{{site_settings('text_primary')}}" required >
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="text_secondary_color">
                            {{translate('Body Text Secondary')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[text_secondary]" id="text_secondary_color" class="colorpicker" value="{{site_settings('text_secondary')}}" required>
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