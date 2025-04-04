  <header class="header">
    @php
        $lang = $languages->where('code',session()->get('locale'));
        $code = count($lang)!=0 ? $lang->first()->code:"en";
        $languages = $languages->where('code','!=',$code)->where('status',App\Enums\StatusEnum::true->status());
        $user = auth_user('web');
        $currencies = site_currencies()->where("code",'!=',session()->get('currency')->code);
    @endphp

    <div class="container-fluid px-0">
      <div class="header-container">
        <div class="d-flex align-items-center gap-4">
            <div class="mobile-menu-btn sidebar-trigger d-xl-none d-flex">
                <div class="burger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>

            <div class="page-title-content d-lg-block d-none">
                <h2>{{translate("Welcome")}}, <span class="text--primary">{{$user->name }}</span></h2>
            </div>
        </div>

        <div class="header-right">
            <a target="_blank"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Browse Frontend')}}" href="{{route('home')}}">
                <i class="bi bi-globe-americas"></i>
            </a>

            @php
                $notifications = \App\Models\Notification::where('notificationable_type','App\Models\User')
                                                ->where("notificationable_id",$user->id)
                                                ->unread()
                                                ->latest()
                                                ->take(8)
                                                ->get();
                $notificationCount = $notifications->count();
            @endphp

            <div class="header-right-item">
                <div class="dropdown noti-dropdown">
                    <a
                        class="noti-dropdown-btn dropdown-toggle"
                        href="javascript:void(0)"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span>{{$notificationCount}}</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end p-0">
                        <div class="dropdown-menu-title">
                            <h6>
                                {{translate("Notifications")}}
                            </h6>
                            <span class="i-badge danger">{{$notificationCount}} {{translate('New')}} </span>
                        </div>

                        <ul class="notification-items" data-simplebar>
                            @forelse($notifications as $notification)
                                <li class="notification-item">
                                    <a href="javascript:void(0)" class="read-notification" data-id="{{$notification->id}}" data-href="{{$notification->url}}">
                                        <div class="notify-icon">
                                          <img src="{{imageURL(@$user->file,'profile,user',true) }}" alt="{{@$user->file->name ?? 'profile.jpg'}}"/>
                                        </div>

                                        <div class="notification-item-content">
                                            <h5> {{$user->name}}
                                                    <small>
                                                    {{diff_for_humans($notification->created_at)}}
                                                </small>
                                            </h5>
                                            <p>{{ limit_words(strip_tags($notification->message),50) }}</p>
                                        </div>
                                        <span><i class="las la-times"></i></span>
                                    </a>
                                </li>
                            @empty
                                <li class="text-center mx-auto my-3">
                                    <p class="text-danger fw-bold fs-18">
                                        {{translate("Nothing Found !!")}}
                                    </p>
                                </li>
                            @endforelse

                        </ul>

                        @if($notifications->count() > 0)
                            <div class="dropdown-menu-footer">
                                <a href='{{route("user.notifications")}}' class="i-btn info btn--md capsuled">
                                    {{translate("View All")}}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            <div class="header-right-item">
                <div class="dropdown lang">
                    <button
                        class="lang-btn dropdown-toggle lang--toggle"
                        type="button"
                        @if(!$languages->isEmpty())
                        data-bs-toggle="dropdown"
                        aria-expanded="false" @endif >
                        <span class="flag">
                            <img src="{{asset('assets/images/global/flags/'.strtoupper($code).'.png') }}" alt="{{$code}}" />
                        </span>
                    </button>
                    @if(!$languages->isEmpty())
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach($languages as $language)
                                <li>
                                    <a href="{{route('language.change',$language->code)}}" class="dropdown-item" >
                                        <span class="flag">
                                            <img src="{{asset('assets/images/global/flags/'.strtoupper($language->code ).'.png') }}" alt="{{$language->code.'.jpg'}}" >
                                        </span>
                                        {{$language->name}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>



            <div class="header-right-item">
                <div class="dropdown profile-dropdown">
                    <div
                        class="profile-btn dropdown-toggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        role="button">
                        <span class="profile-img">
                           <img src="{{imageURL(@$user->file,'profile,user',true) }}" alt="{{@$user->file->name ?? 'user.jpg'}}"/>
                        </span>
                    </div>

                    <div class="dropdown-menu dropdown-menu-end">
                        <ul>
                            <li class="dropdown-menu-title">
                                <h6>
                                    {{translate("Welcome")}}, <span class="user-name">
                                        {{$user->name}}
                                    </span>!
                                </h6>

                                <div class="balance mt-2 d-block d-sm-none">
                                    <p>
                                        {{translate("Balance")}}
                                    </p>

                                    <h6>
                                        {{num_format(number:$user->balance,calC:true)}}
                                    </h6>
                                </div>
                            </li>

                            <li>
                                <a href="{{route('user.profile')}}" class="dropdown-item"
                                ><i class="bi bi-person"></i> {{translate("My Account")}}</a>
                            </li>

                            <li>
                                <a href="{{route('user.deposit.create')}}" class="dropdown-item"><i class="bi bi-wallet"></i>
                                    {{translate("Deposit")}}
                                </a>
                            </li>

                            <li>
                                <a href="{{route('user.withdraw.create')}}" class="dropdown-item"><i class="bi bi-layer-backward"></i>
                                    {{translate("Withdraw")}}
                                </a>
                            </li>

                            <li class="dropdown-menu-footer p-0">
                                <a href="{{route('user.logout')}}">
                                <i class="bi bi-box-arrow-left"></i>
                                    {{translate('Logout')}}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </header>

  @include('user.partials.sidebar')
