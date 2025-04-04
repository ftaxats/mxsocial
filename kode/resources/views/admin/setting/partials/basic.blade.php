<form class="settingsForm"   enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <h4 class="card-title">
                {{  Arr::get($tab,'title') }}
            </h4>

            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#cronjob"  class="i-btn btn--md btn--primary"> <i class="las la-key me-2"></i>
                {{trans('default.cron_setup')}}
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="site_name">
                            {{translate('Site Name')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[site_name]" id="site_name"  value="{{site_settings('site_name')}}" required placeholder='{{translate("Name")}}'>
                    </div>
                </div>

                <div class="col-lg-6">
                     <div class="form-inner">
                        <label for="user_site_name">
                            {{translate('User Site Name')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[user_site_name]" id="user_site_name"  value="{{site_settings('user_site_name')}}" required placeholder='{{translate("User Site Name")}}'>
                     </div>
                </div>

                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="phone">
                            {{translate('Phone')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[phone]" id="phone"  value="{{site_settings('phone')}}" required placeholder="{{translate('Phone')}}">
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="email">
                            {{translate('Email')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="email" name="site_settings[email]" id="email"  value="{{site_settings('email')}}"  placeholder='{{translate("Email")}}'>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-inner">
                        <label for="address">
                            {{translate('Address')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="text" name="site_settings[address]" id="address"  value="{{site_settings('address')}}"  placeholder='{{translate("Address")}}'>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="time_zone">
                            {{translate('Time Zone')}} <small class="text-danger" >*</small>
                        </label>
                        <select  name="site_settings[time_zone]" id="time_zone" class="select2">
                            @foreach($timeZones as $timeZone)
                                <option value="'{{@$timeZone}}'" @if(config('app.timezone') == $timeZone) selected @endif>{{$timeZone}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="input-date_format">
                            {{translate('Select Date Format')}} <small class="text-danger" >*</small>
                        </label>
                        <select name="site_settings[date_format]" id="input-date_format" class="select2" required>
                             @foreach (Arr::get(config("settings"),'date_format' ,[]) as  $date_format)
                                 <option {{site_settings("date_format") == $date_format ? 'selected' :"" }} value="{{$date_format}}">{{$date_format}}</option>
                             @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="input-time_format">
                            {{translate('Select Time Format')}} <small class="text-danger" >*</small>
                        </label>
                        <select name="site_settings[time_format]" id="input-time_format" class="select2" required>
                             @foreach (Arr::get(config("settings"),'time_format' ,[]) as  $time_format)
                                 <option {{site_settings("time_format") == $time_format ? 'selected' :"" }} value="{{$time_format}}">{{$time_format}}</option>
                             @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="country">
                            {{translate('Country')}} <small class="text-danger" >*</small>
                        </label>
                        <select   name="site_settings[country]" id="country" class="select2">
                             @foreach ($countries as $country)
                                 <option {{site_settings('country') == $country->name ? "selected" :"" }} value="{{$country->name}}">
                                      {{$country->name}}
                                </option>
                             @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="pagination_number">
                            {{translate('Data Per page')}} <small class="text-danger" >*</small>
                        </label>
                            <input type="number" min="0" name="site_settings[pagination_number]" id="pagination_number"  value="{{site_settings('pagination_number')}}" required placeholder='{{translate("Data Perpage")}}'>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-inner">
                        <label for="vistors">
                            {{translate('Web Visitors')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="number" min="0" name="site_settings[vistors]" id="vistors"  value="{{site_settings('vistors')}}" required placeholder='{{translate("Site Vistors")}}'>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="maintenance_title" class="form-label">
                            {{translate('Maintenance Mode Title')}} <small class="text-danger" >*</small>
                        </label>

                        <input type="text"  name="site_settings[maintenance_title]" id="maintenance_title"  value="{{site_settings('maintenance_title')}}" required placeholder='{{translate("Maintenance Mode Title")}}'>

                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="maintenance_description" class="form-label">
                            {{translate('Maintenance Mode Description')}} <small class="text-danger" >*</small>
                        </label>

                        <input type="text"  name="site_settings[maintenance_description]" id="maintenance_description"  value="{{site_settings('maintenance_description')}}" required placeholder='{{translate("Maintenance Mode Description")}}'>

                    </div>
                </div>

                <div class="col-xl-12">
                    <div class="form-inner">
                        <label for="copy_right_text">
                            {{translate('Copy Right Text')}} <small class="text-danger" >*</small>
                        </label>
                        <textarea name="site_settings[copy_right_text]" placeholder='{{translate("Copy Right Text")}}' id="copy_right_text" cols="30" rows="4">{{site_settings('copy_right_text')}}</textarea>
                    </div>
                </div>


                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="google_adsense_publisher_id" >
                            {{translate('Google Adsense Publisher Id')}} <small class="text-danger" >*</small>
                        </label>
                            <input type="checkbox" class="form-check-input status-update" {{ site_settings('google_ads') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}

                            data-key='google_ads'
                            data-status='{{ site_settings('google_ads') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                            data-route="{{ route('admin.setting.update.status') }}"  >
                        <input type="text"  name="site_settings[google_adsense_publisher_id]" id="google_adsense_publisher_id"  value="{{is_demo() ? '@@@' :site_settings('google_adsense_publisher_id')}}" required placeholder='{{translate("Enter Id")}}'>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="form-inner">
                        <label for="google_analytics_tracking_id">
                            {{translate('Google Analytics Tracking Id')}} <small class="text-danger" >*</small>
                        </label>
                        <input type="checkbox" class="form-check-input status-update" {{ site_settings('google_analytics') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                        data-key='google_analytics'
                        data-status='{{ site_settings('google_analytics') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                        data-route="{{ route('admin.setting.update.status') }}"  >
                        <input type="text"  name="site_settings[google_analytics_tracking_id]" id="google_analytics_tracking_id"  value="{{is_demo() ? '@@@' :site_settings('google_analytics_tracking_id')}}" required placeholder='{{translate("Enter Id")}}'>
                    </div>
                </div>



                <div class="col-xl-12">
                    <div class="form-inner">
                        <label for="map_api_key" class="form-label">
                            {{translate('Google Map API Key')}} <small class="text-danger" >*</small>
                        </label>

                        <input type="text"  name="site_settings[map_api_key]" id="map_api_key"  value="{{is_demo() ? '@@@@' : site_settings('map_api_key')}}" required placeholder='{{translate("@@@@")}}'>

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
