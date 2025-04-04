
<header class="header">
  @php
            $currencies = site_currencies()->where("code",'!=',session()->get('currency')?->code);
  @endphp
  <div class="header-container">
    <div class="d-flex align-items-center gap-lg-3 gap-2">
      <div class="header-icon">
        <button class="btn-icon vertical-menu-btn ripple-dark" data-anim="ripple">
          <i class="las la-bars"></i>
        </button>
      </div>
    </div>
    <div class="d-flex align-items-center gap-lg-3 gap-2">
      <div class="header-icon d-flex">
        <div class="btn-icon fullscreen-btn ripple-dark" data-anim="ripple">
          <i class="las la-expand"></i>
        </div>
      </div>
      <div class="header-icon d-flex">
        <div class="btn-icon ripple-dark" data-anim="ripple">
           <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Cache clear')}}" href="{{route('admin.setting.cache.clear')}}">
              <i class="las la-broom"></i>
           </a>
        </div>
      </div>
      <div class="header-icon">
        <div class="btn-icon ripple-dark" data-anim="ripple">
           <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Browse frontend')}}" target="_blank" href="{{url('/')}}">
              <i class="las la-globe"></i>
           </a>
        </div>
      </div>
      @if(site_settings('database_notifications') ==  App\Enums\StatusEnum::true->status() && check_permission('view_notification'))
          @php
               $notifications = \App\Models\Notification::where('notificationable_type','App\Models\Admin')
                                                          ->unread()
                                                          ->latest()
                                                          ->take(8)
                                                          ->get();
          @endphp

          <div class="header-icon">
            <div class="notification-dropdown">
              @if($notifications->count() > 0)
                <span>{{$notifications->count()}}</span>
              @endif
              <div class="btn-icon dropdown-toggle ripple-dark"  role="button" data-anim="ripple" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="lar la-bell"></i>
              </div>
              <div class="dropdown-menu dropdown-menu-end">
                <div class="dropdown-menu-title">
                  <h6>
                      {{translate("Notification")}}
                  </h6>
                  <span class="i-badge success">{{$notifications->count()}} {{translate("New")}}   </span>
                </div>
                <div class="notification-items" data-simplebar>
                    <div class="notification-item">
                        <ul>
                          @forelse($notifications as $notification)
                              <li>
                                <a href="javascript:void(0)" class="read-notification" data-id="{{$notification->id}}" data-href="{{$notification->url}}">
                                  <div class="notify-icon">
                                    <img class="rounded-circle"
                                      src='{{imageURL(auth_user()->file,"profile,admin",true) }}'
                                      alt="profile.jpg" />
                                  </div>
                                  <div class="notification-item-content">
                                    <h5> {{auth_user()->name}} <small>
                                        {{diff_for_humans($notification->created_at)}}
                                      </small></h5>
                                    <p>
                                        {{
                                          limit_words(strip_tags($notification->message),50)
                                        }}
                                    </p>
                                  </div>
                                </a>
                              </li>
                          @empty
                              <li class="text-center mx-auto mb-2 no-notification-item">
                                  <p>
                                    {{translate("Nothing Found !!")}}
                                  </p>
                              </li>
                          @endforelse
                        </ul>
                    </div>
                </div>
                  @if($notifications->count() >0)
                    <div class="dropdown-menu-footer">
                        <a href='{{route("admin.notifications")}}'>
                           {{translate("View All")}}
                        </a>
                    </div>
                  @endif
              </div>
            </div>
          </div>
      @endif
       <!-- currency switcher -->
       <div class="header-icon">
        <div class="lang-dropdown">
          <div class="btn-icon btn--text dropdown-toggle" role="button" @if($currencies->count() > 0) data-bs-toggle="dropdown" aria-expanded="false" @endif >
            {{session()->get('currency')?->code}}
          </div>
          @if($currencies->count() > 0)
            <div class="dropdown-menu dropdown-menu-end">
              <ul>
                @foreach(site_currencies()->where("code",'!=',session()->get('currency')->code) as $currency)
                  <li>
                    <a href="{{route('currency.change',$currency->code)}}">
                      {{$currency->code}}
                    </a>
                  </li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>
      </div>
      <!-- language switcher -->
      <div class="header-icon">
        @php
          $lang = $languages->where('code',session()->get('locale'));
          $code = count($lang)!=0 ? $lang->first()->code:"en";
          $languages = $languages->where('code','!=',$code)->where('status',App\Enums\StatusEnum::true->status());
        @endphp
        <div class="lang-dropdown">
          <div class="btn-icon dropdown-toggle" role="button"  @if(!$languages->isEmpty()) data-bs-toggle="dropdown" aria-expanded="false" @endif >
              <img id="header-lang-img" class="flag-img" src="{{asset('assets/images/global/flags/'.strtoupper($code ).'.png') }}" alt="{{$code.'.jpg'}}" height="20">
          </div>
          @if(!$languages->isEmpty())
            <div class="dropdown-menu dropdown-menu-end">
              <ul>
                  @foreach($languages as $language)
                    <li>
                      <a href="{{route('language.change',$language->code)}}">
                        <img src="{{asset('assets/images/global/flags/'.strtoupper($language->code ).'.png') }}" alt="{{$language->code.'.jpg'}}" >
                        {{$language->name}}
                      </a>
                    </li>
                  @endforeach
              </ul>
            </div>
          @endif
        </div>
      </div>
     <!-- profile -->
      <div class="header-icon">
        <div class="profile-dropdown">
          <div class="topbar-profile dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src='{{imageURL(@auth_user()->file,"profile,admin",true)}}' alt="{{@auth_user()->file->name}}">
          </div>
          <div class="dropdown-menu dropdown-menu-end">
            <ul>
              <li>  <span class="dropdown-item">{{translate('Welcome')}} {{auth_user()->name}}!</span></li>
              <li>
                  <a class="dropdown-item" href="{{route('admin.profile.index')}}"> <i class="las la-cog"></i>
                    {{translate("Setting")}}
                  </a>
              </li>
              <li>
                  <a href="{{route('admin.logout')}}" class="pointer dropdown-item " >  <i class="las la-sign-out-alt"></i>
                    {{translate('logout')}}
                  </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

