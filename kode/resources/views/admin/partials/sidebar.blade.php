
<div class="sidebar">
  <div class="sidebar-logo">
    <a href="{{route('admin.home')}}">
      <img
        src='{{imageURL(@site_logo("site_logo")->file,"site_logo",true)}}'
        alt="site-logo.jpg" />
    </a>

  </div>

  <div class="sidebar-menu-container" data-simplebar>
    <ul class="sidebar-menu">

        @if(check_permission('view_dashboard'))
          <li class="sidebar-menu-title">  {{trans('default.home')}}</li>
            <li class="sidebar-menu-item">
                <a class="sidebar-menu-link {{sidebar_awake('admin.home')}}" data-anim="ripple" href='{{route("admin.home")}}' aria-expanded="false">
                    <span><i class="las la-chart-line"></i></span>
                    <p> {{translate("Dashboard")}}</p>
                </a>
            </li>
        @endif

        @if( check_permission('view_role') ||  check_permission('view_staff') )
          <li class="sidebar-menu-item">
              <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#role_staff" role="button"
                aria-expanded="false" aria-controls="role_staff">
                <span><i class="las la-user-lock"></i></span>
                  <p>
                    {{translate('Access Control')}}
                  </p>
                  <small >
                    <i class="las la-angle-down"></i>
                  </small>
              </a>
            <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.role.*','admin.staff.*'],'drop_down')}} " id="role_staff">
              <ul class="sub-menu">
                @if(check_permission('view_role'))
                  <li class="sub-menu-item">
                      <a class="sidebar-menu-link {{sidebar_awake('admin.role.*')}}" href="{{route('admin.role.list')}}">
                        <span></span>
                          <p>
                            {{translate('Roles & Permissions')}}
                          </p>
                      </a>
                  </li>
                @endif

                @if(check_permission('view_staff'))
                  <li class="sub-menu-item">
                      <a class="sidebar-menu-link  {{sidebar_awake('admin.staff.*')}}" href="{{route('admin.staff.list')}}">
                          <span></span>
                          <p>
                            {{translate('Staffs')}}
                          </p>
                      </a>
                  </li>
                @endif
              </ul>
            </div>
          </li>
        @endif



        @if(check_permission('view_package'))
          <li class="sidebar-menu-item">
            <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#packages" role="button"
              aria-expanded="false" aria-controls="packages">
              <span><i class="lab la-hornbill"></i></span>
              <p>
                {{translate('Subscription Services')}}
              </p>
              <small >
                <i class="las la-angle-down"></i>
              </small>
            </a>
            <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.subscription.package.*'],'drop_down')}} " id="packages">
              <ul class="sub-menu">
                <li class="sub-menu-item">
                  <a class="sidebar-menu-link {{sidebar_awake(['admin.subscription.package.list' ,'admin.subscription.package.edit'])}}" href="{{route('admin.subscription.package.list')}}">
                    <span></span>
                      <p>
                        {{translate('Packages')}}
                      </p>
                  </a>
                </li>
                <li class="sub-menu-item">
                  <a class="sidebar-menu-link  {{sidebar_awake('admin.subscription.package.create')}}" href="{{route('admin.subscription.package.create')}}">
                    <span></span>
                    <p>
                      {{translate('Add New')}}
                    </p>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif

        @if(check_permission('view_account') || check_permission('view_platform'))
         <li class="sidebar-menu-title">  {{translate('Social Media')}}</li>
          <li class="sidebar-menu-item">
            <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#socialAccount" role="button"
              aria-expanded="false" aria-controls="socialAccount">
            <span><i class="las la-tools"></i></span>
              <p>
                {{translate('Accounts')}}
              </p>
              <small >
                  <i class="las la-angle-down"></i>
              </small>
            </a>
            <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.platform.*','admin.social.account.*'],'drop_down')}} " id="socialAccount">
              <ul class="sub-menu">
                @if(check_permission('view_platform'))
                  <li class="sub-menu-item">
                      <a  href='{{route("admin.platform.list")}}' class='sidebar-menu-link {{sidebar_awake("admin.platform.list")}} '>
                        <span></span>
                        <p>{{translate('Platforms')}}</p>
                      </a>
                  </li>
                @endif

                @if(check_permission('view_account'))
                  <li class="sub-menu-item">
                      <a  href="{{route('admin.social.account.list',['platform' =>'facebook'])}}"  class='sidebar-menu-link {{sidebar_awake("admin.social.account.*")}}'>
                          <span></span>
                          <p>{{translate('Accounts')}}</p>
                      </a>
                  </li>
                @endif
              </ul>
            </div>
          </li>
        @endif

        @if(check_permission('view_post'))
          <li class="sidebar-menu-item">
              <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#socialFeed" role="button"
                aria-expanded="false" aria-controls="socialFeed">
              <span><i class="las la-paper-plane"></i></span>
                <p>
                  {{translate('Posts')}}
                </p>
                <small >
                    <i class="las la-angle-down"></i>
                </small>
              </a>
              <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.social.post.*'],'drop_down')}} " id="socialFeed">
                <ul class="sub-menu">
                  @if(check_permission('view_account'))
                  <li class="sub-menu-item">
                    <a  href="{{route('admin.social.post.analytics')}}"  class='sidebar-menu-link {{sidebar_awake("admin.social.post.analytics")}}'>
                        <span></span>
                        <p>{{translate('Analytics')}}</p>
                    </a>
                  </li>
                    <li class="sub-menu-item">
                        <a  href="{{route('admin.social.post.list')}}"  class='sidebar-menu-link {{sidebar_awake(["admin.social.post.list","admin.social.post.show"])}}'>
                            <span></span>
                            <p>{{translate('All Post')}}</p>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a  href="{{route('admin.social.post.create')}}"  class='sidebar-menu-link {{sidebar_awake("admin.social.post.create")}}'>
                            <span></span>
                            <p>{{translate('Create New')}}</p>
                        </a>
                    </li>
                  @endif
                </ul>
              </div>
          </li>
        @endif

        @if(check_permission('view_user'))
            <li class="sidebar-menu-title">
              {{translate('User Statistics & Support')}}
            </li>
            <li class="sidebar-menu-item">
              <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#users" role="button" aria-expanded="false" aria-controls="users">
                <span><i class="las la-users-cog"></i></span>
                <p>
                    {{translate('Manage Users')}}
                </p>
                <small >
                    <i class="las la-angle-down"></i>
                </small>
              </a>
              <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.user.*'],'drop_down')}} " id="users">
                <ul class="sub-menu">

                  <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake(["admin.user.statistics"])}}'  href='{{route("admin.user.statistics")}}'>
                      <span></span>
                      <p>
                          {{translate('Statistics')}}
                      </p>
                    </a>
                  </li>


                  <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake(["admin.user.list","admin.user.show"])}}'  href='{{route("admin.user.list")}}'>
                      <span></span>
                      <p>
                          {{translate('All Users')}}
                      </p>
                    </a>
                  </li>
                  <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake("admin.user.active")}}'  href='{{route("admin.user.active")}}'>
                      <span></span>
                      <p>
                          {{translate('Active Users')}}
                      </p>
                    </a>
                  </li>
                  <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake("admin.user.banned")}}'  href='{{route("admin.user.banned")}}'>
                      <span></span>
                      <p>
                          {{translate('Banned Users')}}
                      </p>
                    </a>
                  </li>

                  <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake("admin.user.kyc.verfied")}}'  href='{{route("admin.user.kyc.verfied")}}'>
                      <span></span>
                      <p>
                          {{translate('KYC Verified')}}
                      </p>
                    </a>
                  </li>

                  <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake("admin.user.kyc.banned")}}'  href='{{route("admin.user.kyc.banned")}}'>
                      <span></span>
                      <p>
                          {{translate('KYC Banned')}}
                      </p>
                    </a>
                  </li>
                </ul>
              </div>
            </li>
        @endif

        @if(check_permission('view_ticket'))
            <li class="sidebar-menu-item">
              <a
                class='sidebar-menu-link {{sidebar_awake("admin.ticket.*")}}'
                data-anim="ripple"
                href='{{route("admin.ticket.list")}}'
                aria-expanded="false">
                <span><i class="las la-question"></i></span>
                <p> {{translate("Support Tickets")}}
                    @if($pending_tickets > 0)
                      <span  data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Pending tickets')}}" class="i-badge danger">{{$pending_tickets}}</span>
                    @endif
                </p>
              </a>
            </li>
        @endif

        @if(check_permission('view_report'))
          <li class="sidebar-menu-item">
            <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#report" role="button"
              aria-expanded="false" aria-controls="report">
              <span><i class="las la-stream"></i></span>
                <p>
                  {{translate('Report')}}
                    @if($pending_deposits > 0 || $pending_withdraws > 0 || $pending_kycs > 0  )
                      <span  data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Pending reports')}}"  class="i-badge danger">
                          <i class="las la-info"></i>
                      </span>
                    @endif
                </p>
                <small >
                  <i class="las la-angle-down"></i>
                </small>
            </a>
            <div class='side-menu-dropdown collapse {{sidebar_awake(["admin.template.report.*","admin.subscription.report.*","admin.payment.report.*","admin.transaction.report.*","admin.credit.report.*","admin.withdraw.report.*","admin.deposit.report.*" ,"admin.affiliate.report.*","admin.kyc.report.*","admin.webhook.*"],"drop_down")}}' id="report">
              <ul class="sub-menu">
                <li class="sub-menu-item">
                  <a  href='{{route("admin.template.report.list")}}'  class='sidebar-menu-link {{sidebar_awake("admin.template.report.list")}}'>
                      <span></span>
                      <p>
                        {{translate("Template Reports")}}
                      </p>
                  </a>
                </li>
                <li class="sub-menu-item">
                  <a class='sidebar-menu-link {{sidebar_awake("admin.subscription.report.*")}}'  href='{{route("admin.subscription.report.list")}}'>
                    <span></span>
                    <p>
                      {{translate('Subscription Reports')}}
                    </p>
                  </a>
                </li>
                <li class="sub-menu-item">
                  <a class='sidebar-menu-link {{sidebar_awake("admin.credit.report.*")}}'  href='{{route("admin.credit.report.list")}}'>
                    <span></span>
                    <p>
                      {{translate('Credit Reports')}}
                    </p>
                  </a>
                </li>
                <li class="sub-menu-item">
                  <a class='sidebar-menu-link {{sidebar_awake("admin.deposit.report.*")}}'  href='{{route("admin.deposit.report.list")}}'>
                    <span></span>
                    <p>
                      {{translate('Deposit Reports')}}

                      @if($pending_deposits > 0 )
                         <span  data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Pending deposit')}}" class="i-badge danger">{{$pending_deposits}}</span>
                      @endif
                    </p>
                  </a>
                </li>
                <li class="sub-menu-item">
                  <a class='sidebar-menu-link {{sidebar_awake("admin.withdraw.report.*")}}'  href='{{route("admin.withdraw.report.list")}}'>
                    <span></span>
                    <p>
                      {{translate('Withdraw Reports')}}

                      @if($pending_withdraws > 0 )
                          <span   data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Pending withdraws')}}" class="i-badge danger">{{$pending_withdraws}}</span>
                      @endif
                    </p>
                  </a>
                </li>
                <li class="sub-menu-item">
                  <a class='sidebar-menu-link {{sidebar_awake("admin.affiliate.report.*")}}'  href='{{route("admin.affiliate.report.list")}}'>
                    <span></span>
                    <p>
                      {{translate('Affiliate Reports')}}
                    </p>
                  </a>
                </li>
                <li class="sub-menu-item">
                  <a class='sidebar-menu-link {{sidebar_awake("admin.transaction.report.*")}}'  href='{{route("admin.transaction.report.list")}}'>
                    <span></span>
                    <p>
                      {{translate('Transaction Reports')}}
                    </p>
                  </a>
                </li>
                <li class="sub-menu-item">
                  <a class='sidebar-menu-link {{sidebar_awake("admin.kyc.report.*")}}'  href='{{route("admin.kyc.report.list")}}'>
                    <span></span>
                    <p>
                      {{translate('KYC Reports')}}

                        @if($pending_kycs > 0 )
                            <span  data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Pending KYC logs')}}" class="i-badge danger">{{$pending_kycs}}</span>
                        @endif
                    </p>
                  </a>
                </li>

                <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake("admin.webhook.report.*")}}'  href='{{route("admin.webhook.report.list")}}'>
                      <span></span>
                      <p>
                        {{translate('Webhook Reports')}}
                      </p>
                    </a>
                </li>

              </ul>
            </div>
          </li>
        @endif




        @if(check_permission('view_ai_template'))
            <li class="sidebar-menu-title">
                {{translate('AI Content')}}
            </li>

            <li class="sidebar-menu-item">
              <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#aiTemplate" role="button"
                aria-expanded="false" aria-controls="aiTemplate">
              <span><i class="las la-sliders-h"></i></span>
                <p>
                  {{translate('AI Templates')}}
                </p>
                <small >
                    <i class="las la-angle-down"></i>
                </small>
              </a>
              <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.ai.template.*' , 'admin.category.*'],'drop_down')}} " id="aiTemplate">
                <ul class="sub-menu">

                  <li class="sub-menu-item">
                      <a  href='{{route("admin.category.list")}}' class='sidebar-menu-link {{sidebar_awake(["admin.category.*"])}}'>
                        <span></span>
                        <p>
                          {{translate("Categories")}}
                        </p>
                      </a>
                  </li>

                  <li class="sub-menu-item">
                      <a  href='{{route("admin.ai.template.list")}}' class='sidebar-menu-link {{sidebar_awake(["admin.ai.template.list","admin.ai.template.edit","admin.ai.template.create","admin.ai.template.content"])}} '>
                        <span></span>
                        <p>
                          {{translate("Templates")}}
                        </p>
                      </a>
                  </li>
                  <li class="sub-menu-item">
                    <a  href='{{route("admin.ai.template.default")}}'  class='sidebar-menu-link {{sidebar_awake("admin.ai.template.default")}}'>
                        <span></span>
                        <p>
                          {{translate("Default Templates")}}
                        </p>
                    </a>
                  </li>

                </ul>
              </div>
            </li>
        @endif

        @if(check_permission('view_content'))
            <li class="sidebar-menu-item">
                <a class='sidebar-menu-link {{sidebar_awake("admin.content.*")}}'  href='{{route("admin.content.list")}}'>
                  <span><i class="las la-clipboard-list"></i></span>
                  <p>
                    {{translate('Contents')}}
                  </p>
                </a>
            </li>
        @endif



        <li class="sidebar-menu-title">
            {{translate('Frontend Configuration')}}
        </li>
        <li class="sidebar-menu-item">
          <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#frontend" role="button"
            aria-expanded="false" aria-controls="frontend">
            <span><i class="las la-puzzle-piece"></i></span>
            <p>
                {{translate('Sections')}}
            </p>
            <small >
              <i class="las la-angle-down"></i>
            </small>
          </a>

          <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.appearance.*','admin.menu.*','admin.page.*'],'drop_down')}} " id="frontend">
            <ul class="sub-menu">
              @if(check_permission('view_frontend'))
                @php
                    $appearanceSegment = collect(request()->segments())->last();
                @endphp
                @foreach (get_appearance(true) as $key => $appearance)
                    @if (isset($appearance['builder']) && $appearance['builder'])

                      <li class="sub-menu-item">
                          <a class="sidebar-menu-link @if ($key == $appearanceSegment ||  (@$appearance['child_section']  && @$appearance['child_section'] == request()->route('key')) ) active @endif"  href='{{route("admin.appearance.list",$key)}}'>
                            <span></span>
                            <p>
                              {{translate(k2t($appearance['name']))}}
                            </p>
                          </a>
                      </li>
                    @endif
                @endforeach
              @endif

              @if(check_permission('view_page'))
                <li class="sub-menu-item">
                  <a class='sidebar-menu-link {{sidebar_awake("admin.page.*")}}'  href='{{route("admin.page.list")}}'>
                    <span></span>
                    <p>
                      {{translate('Pages')}}
                    </p>
                  </a>
                </li>
              @endif

              @if(check_permission('view_menu'))
                <li class="sub-menu-item">
                  <a class='sidebar-menu-link {{sidebar_awake("admin.menu.*")}}' href='{{route("admin.menu.list")}}'>
                    <span></span>
                    <p>
                      {{translate('Menu')}}
                    </p>
                  </a>
                </li>
              @endif
            </ul>
          </div>
        </li>

        @if(check_permission('view_blog') || check_permission('view_category'))
          <li class="sidebar-menu-item">
            <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#blog" role="button"
              aria-expanded="false" aria-controls="blog">
              <span><i class="las la-newspaper"> </i> </span>
              <p>
                {{translate('Blogs')}}
              </p>
              <small >
                <i class="las la-angle-down"></i>
              </small>
            </a>
            <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.blog.*'],'drop_down')}} " id="blog">
              <ul class="sub-menu">


                @if(check_permission('view_blog'))

                  <li class="sub-menu-item">
                        <a class='sidebar-menu-link {{sidebar_awake(["admin.blog.list","admin.blog.edit"])}}' href='{{route("admin.blog.list")}}'>
                        <span></span>
                          <p>
                            {{translate('Blogs')}}
                          </p>
                        </a>
                  </li>
                  <li class="sub-menu-item">
                      <a class="sidebar-menu-link  {{sidebar_awake('admin.blog.create')}}" href="{{route('admin.blog.create')}}">
                        <span></span>
                        <p>
                          {{translate('Add New')}}
                        </p>
                      </a>
                  </li>
                @endif
              </ul>
            </div>
          </li>
        @endif


        @if(check_permission('view_frontend'))

            <li class="sidebar-menu-item">
              <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#marketing" role="button"
                aria-expanded="false" aria-controls="marketing">
                <span><i class="las la-ad"></i> </span>
                  <p>
                    {{translate('Manage Promotions')}}
                  </p>
                  <small >
                    <i class="las la-angle-down"></i>
                  </small>
              </a>
              <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.contact.*','admin.subscriber.*','admin.ad.*'],'drop_down')}} " id="marketing">
                <ul class="sub-menu">
                    <li class="sub-menu-item">
                        <a class='sidebar-menu-link {{sidebar_awake("admin.contact.*")}}'  href='{{route("admin.contact.list")}}'>
                          <span></span>
                            <p>
                              {{translate('Contacts')}}
                            </p>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                      <a class='sidebar-menu-link {{sidebar_awake("admin.subscriber.*")}}'  href='{{route("admin.subscriber.list")}}'>
                        <span></span>
                          <p>
                            {{translate('Subscribers')}}
                          </p>
                      </a>
                    </li>
                </ul>
              </div>
            </li>
        @endif


       @if(check_permission('view_settings') || check_permission('view_language'))
          <li class="sidebar-menu-title">
              {{translate('System Configuration')}}
          </li>
          <li class="sidebar-menu-item">
            <a  class="sidebar-menu-link" data-bs-toggle="collapse" href="#setting" role="button"
              aria-expanded="false" aria-controls="setting">
              <span><i class="las la-cog"></i></span>
                <p>
                  {{translate('System Settings')}}

                </p>
                <small >
                  <i class="las la-angle-down"></i>
                </small>
            </a>
            <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.setting.*','admin.language.*'],'drop_down')}} " id="setting">
              <ul class="sub-menu">

                @if(check_permission('view_settings'))
                    <li class="sub-menu-item">
                        <a class='sidebar-menu-link {{sidebar_awake("admin.setting.list")}}'  href='{{route("admin.setting.list")}}'>
                        <span></span>
                        <p>
                            {{translate('App Settings')}}
                        </p>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a class='sidebar-menu-link {{sidebar_awake("admin.setting.configuration.*")}}'  href='{{route("admin.setting.configuration.index")}}'>
                        <span></span>
                        <p>
                            {{translate('System Preferences')}}
                        </p>
                        </a>
                    </li>
                @endif

                @if(check_permission('view_language'))
                  <li class="sub-menu-item">
                      <a class='sidebar-menu-link {{sidebar_awake("admin.language.*")}}'  href='{{route("admin.language.list")}}'>
                        <span></span>
                        <p>
                          {{translate('Languages')}}
                        </p>
                      </a>
                  </li>
                @endif

                @if(check_permission('view_settings'))
                    <li class="sub-menu-item">
                        <a class='sidebar-menu-link {{sidebar_awake("admin.setting.openAi")}}'  href='{{route("admin.setting.openAi")}}'>
                        <span></span>
                            <p>
                            {{translate('AI Configuration')}}
                            </p>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                        <a class='sidebar-menu-link {{sidebar_awake("admin.setting.webhook")}}'  href='{{route("admin.setting.webhook")}}'>
                        <span></span>
                            <p>
                            {{translate('Webhook Configuration')}}
                            </p>
                        </a>
                    </li>
                    <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake("admin.setting.affiliate")}}'  href='{{route("admin.setting.affiliate")}}'>
                        <span></span>
                        <p>
                            {{translate('Affiliate Configuration')}}
                        </p>
                    </a>
                    </li>
                    <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake("admin.setting.kyc")}}'  href='{{route("admin.setting.kyc")}}'>
                        <span></span>
                        <p>
                            {{translate('KYC Configuration')}}
                        </p>
                    </a>
                    </li>
                @endif
              </ul>
            </div>
          </li>
       @endif

       @if(check_permission('view_Security'))
          <li class="sidebar-menu-item">
            <a  class="sidebar-menu-link" data-bs-toggle="collapse" href="#securitySetting" role="button"
              aria-expanded="false" aria-controls="securitySetting">
              <span><i class="las la-shield-alt"></i></span>
                <p>
                  {{translate('Security Settings')}}

                </p>
                <small >
                  <i class="las la-angle-down"></i>
                </small>
            </a>
            <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.security.*'],'drop_down')}} " id="securitySetting">
              <ul class="sub-menu">
                  <li class="sub-menu-item">
                      <a class='sidebar-menu-link {{sidebar_awake("admin.security.country.list")}}'  href='{{route("admin.security.country.list")}}'>
                        <span></span>
                        <p>
                          {{translate('Countries')}}
                        </p>
                      </a>
                  </li>
                  <li class="sub-menu-item">
                      <a class='sidebar-menu-link {{sidebar_awake("admin.security.ip.list")}}'  href='{{route("admin.security.ip.list")}}'>
                        <span></span>
                        <p>
                           {{translate('IP List')}}
                        </p>
                      </a>
                  </li>
                  <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake("admin.security.dos")}}'  href='{{route("admin.security.dos")}}'>
                      <span></span>
                      <p>
                         {{translate('Dos Security')}}
                      </p>
                    </a>
                </li>
              </ul>
            </div>
          </li>
       @endif

       @if(check_permission('view_currency'))
          <li class="sidebar-menu-item">
              <a class='sidebar-menu-link {{sidebar_awake("admin.currency.list")}}' data-anim="ripple" href="{{route('admin.currency.list')}}" aria-expanded="false">
                  <span><i class="las la-euro-sign"></i></span>
                  <p>{{translate('Currencies')}}</p>
              </a>
          </li>
       @endif

       @if(check_permission('view_method'))
        <li class="sidebar-menu-item">
          <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#payment" role="button"
            aria-expanded="false" aria-controls="payment">
            <span><i class="las la-money-bill"></i></span>
              <p>
                {{translate('Payment Gateway')}}
              </p>
              <small >
                  <i class="las la-angle-down"></i>
              </small>
          </a>
          <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.paymentMethod.*'],'drop_down')}} " id="payment">
            <ul class="sub-menu">
                @foreach (['automatic','manual'] as $type)
                  <li class="sub-menu-item">
                      <a class="sidebar-menu-link @if(request()->route('type') == $type )  active @endif"  href='{{route("admin.paymentMethod.list",$type)}}'>
                        <span></span>
                        <p>
                          {{ ucfirst($type).translate(' Method')}}
                        </p>
                      </a>
                  </li>
                @endforeach
            </ul>
          </div>
        </li>
       @endif

      @if(check_permission('view_withdraw'))
        <li class="sidebar-menu-item">
            <a class='sidebar-menu-link {{sidebar_awake(["admin.withdraw.list","admin.withdraw.edit","admin.withdraw.create"])}}' data-anim="ripple" href="{{route('admin.withdraw.list')}}" aria-expanded="false">
                <span><i class="las la-dolly-flatbed"></i></span>
                <p>{{translate('Withdraw Method')}}</p>
            </a>
        </li>
      @endif

      @if(check_permission('view_template'))
          <li class="sidebar-menu-item">
            <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#notificationTemplates" role="button"
              aria-expanded="false" aria-controls="notificationTemplates">
              <span><i class="las la-bell"></i></span>
                <p>
                  {{translate('Notification Templates')}}
                </p>
                <small >
                  <i class="las la-angle-down"></i>
                </small>
            </a>
            <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.template.*'],'drop_down')}} " id="notificationTemplates">
              <ul class="sub-menu">
                <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake(["admin.template.list","admin.template.edit"])}}' href="{{route('admin.template.list')}}">
                      <span></span>
                        <p>
                            {{translate('Notification Template')}}
                        </p>
                    </a>
                </li>
                <li class="sub-menu-item">
                    <a class="sidebar-menu-link {{sidebar_awake('admin.template.global')}}" href="{{route('admin.template.global')}}">
                      <span></span>
                        <p>
                            {{translate('Global Template')}}
                        </p>
                    </a>
                </li>
              </ul>
            </div>
          </li>
      @endif


      @if(check_permission('view_gateway'))
          <li class="sidebar-menu-item">
            <a  class="sidebar-menu-link " data-bs-toggle="collapse" href="#notificationGateway" role="button"
              aria-expanded="false" aria-controls="notificationGateway">
              <span><i class="las la-cogs"></i></span>
                <p>
                  {{translate('Notification Gateway')}}
                </p>
                <small >
                  <i class="las la-angle-down"></i>
                </small>
            </a>
            <div class="side-menu-dropdown collapse {{sidebar_awake(['admin.mailGateway.*','admin.smsGateway.*'],'drop_down')}} " id="notificationGateway">
              <ul class="sub-menu">
                <li class="sub-menu-item">
                    <a class='sidebar-menu-link {{sidebar_awake("admin.mailGateway.*")}}' href='{{route("admin.mailGateway.list")}}'>
                      <span></span>
                        <p>
                          {{translate('Mail Gateway')}}
                        </p>
                    </a>
                </li>
                <li class="sub-menu-item">
                    <a class="sidebar-menu-link {{sidebar_awake('admin.smsGateway.*')}}" href="{{route('admin.smsGateway.list')}}">
                      <span></span>
                        <p>
                          {{translate('SMS Gateway')}}
                        </p>
                    </a>
                </li>
              </ul>
            </div>
          </li>
      @endif


      @if(check_permission('view_settings'))
        <li class="sidebar-menu-title">
            {{translate('Server Info')}}
        </li>
        <li class="sidebar-menu-item">
            <a class='sidebar-menu-link {{sidebar_awake("admin.setting.server.info")}}'  href='{{route("admin.setting.server.info")}}'>
              <span><i class="las la-server"></i></span>
              <p>
                {{translate('Server Info')}}
              </p>
            </a>
        </li>


        <li class="sidebar-menu-item">
          <a class='sidebar-menu-link {{sidebar_awake("admin.system.update.init")}}'  href='{{route("admin.system.update.init")}}'>
            <span><i class="las la-ellipsis-h"></i></span>
            <p>
              {{translate('System Update')}}
              <span data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('APP Version')}}"  class="i-badge danger">
                   {{translate('V')}}{{site_settings("app_version",1.1)}}
              </span>
            </p>
          </a>
      </li>


      @endif

    </ul>
  </div>
</div>
