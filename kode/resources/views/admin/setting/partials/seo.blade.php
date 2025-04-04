<form class="settingsForm"   enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                {{  Arr::get($tab,'title') }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-inner">
                        <label for="title_separator">
                            {{translate('Title Separator')}} <small class="text-danger" >*</small>
                        </label>
                         <input type="text" value="{{site_settings('title_separator')}}" name="site_settings[title_separator]" id="title_separator">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-inner">
                        <label for="site_description">
                            {{translate('Default Site Description')}} <small class="text-danger" >*</small>
                        </label>
                        <textarea name="site_settings[site_description]" id="site_description" cols="30" rows="10">{{site_settings('site_description')}}</textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-inner">
                        <label for="metaKeywords" >
                            {{translate('Default Meta Keywords')}} <small class="text-danger" >*</small>
                        </label>
                        <select multiple name="site_settings[site_meta_keywords][]" id="metaKeywords">
                            @if(is_array(json_decode(site_settings("site_meta_keywords"),true)))
                               @foreach (json_decode(site_settings("site_meta_keywords"),true) as  $keyword)
                                   <option selected value="{{$keyword}}">{{$keyword}}</option>
                               @endforeach
                            @endif
                        </select>
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


