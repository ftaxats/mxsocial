@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <ul class="list-group">
                <li
                    class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-3 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Email Notification') }}</h6>
                        <p>
                            <small>{{ translate('When enabled, this module sends necessary emails to users. If disabled, no emails will be sent. Prior to disabling, ensure there are no pending emails.') }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('email_notifications') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='email_notifications'
                                data-status='{{ site_settings('email_notifications') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}"
                                id="email-notification">
                            <label class="form-check-label" for="email-notification"></label>
                        </div>
                    </div>
                </li>
                <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-3 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('SMS Notification') }}</h6>
                        <p>
                            <small>{{ translate('When enabled, this module sends necessary emails to users. If disabled, no emails will be sent. Prior to disabling, ensure there are no pending emails.') }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('sms_notification') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='sms_notification'
                                data-status='{{ site_settings('sms_notification') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}"
                                id="sms-notification">

                            <label class="form-check-label" for="sms-notification"></label>
                        </div>
                    </div>
                </li>
                <li
                    class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Database Notifications') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate('Enable this module for notifications on database events (e.g., New Ticket Generation, New Messages) to users, and administrators.') }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('database_notifications') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='database_notifications'
                                data-status='{{ site_settings('database_notifications') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}"
                                id="database_notifications">
                            <label class="form-check-label" for="database_notifications"></label>
                        </div>
                    </div>
                </li>
                <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Strong Password') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate('Activating this module enhances password security through robust validation. Your commitment to enabling this feature strengthens our overall system integrity.') }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('strong_password') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='strong_password'
                                data-status='{{ site_settings('strong_password') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}"
                                id="strong_password">
                            <label class="form-check-label" for="strong_password"></label>
                        </div>
                    </div>
               </li>

                <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Force SSL') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("Enabling this feature mandates the use of HTTPS for your site.") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('force_ssl') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='force_ssl'
                                data-status='{{ site_settings('force_ssl') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}" id="force_ssl">
                            <label class="form-check-label" for="force_ssl"></label>
                        </div>
                    </div>
                </li>


                <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Maintenance Mode') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("Enabling this feature initiates the site maintenance mode, ensuring a smooth transition to maintenance status for necessary updates and improvements") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('maintenance_mode') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='maintenance_mode'
                                data-status='{{ site_settings('maintenance_mode') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}" id="maintenance_mode">
                            <label class="form-check-label" for="maintenance_mode"></label>
                        </div>
                    </div>
                </li>

                <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('KYC Verification') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("Activating this feature enables the user KYC verification module, enhancing security and regulatory compliance for a more robust and reliable system") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('kyc_verification') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='kyc_verification'
                                data-status='{{ site_settings('kyc_verification') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}" id="kyc_verification">
                            <label class="form-check-label" for="kyc_verification"></label>
                        </div>
                    </div>
                </li>
                <li
                    class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Cookie Activation') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("Enable or disable the use of cookies for user sessions and tracking purposes.") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('cookie') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update" data-key='cookie'
                                data-status='{{ site_settings('cookie') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}" id="cookie">
                            <label class="form-check-label" for="cookie"></label>
                        </div>
                    </div>
                </li>
                <li
                    class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('App Debug') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("Enabling this module activates system debugging mode, aiding in troubleshooting by providing detailed error messages to identify code issues.") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input {{ env('app_debug') || env('APP_DEBUG') ? 'checked' : '' }} type="checkbox"
                                class="form-check-input status-update" data-key='app_debug'
                                data-status='{{ env("APP_DEBUG") ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}" id="app_debug">
                            <label class="form-check-label" for="app_debug"></label>
                        </div>
                    </div>
                </li>
                <li
                    class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('User Registration') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("Enabling the module activates the User Register Module, indicating their interdependency for proper functioning.") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('registration') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='registration'
                                data-status='{{ site_settings('registration') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}"
                                id="user_register">
                            <label class="form-check-label" for="user_register"></label>
                        </div>
                    </div>
                </li>
                <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Social Auth') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("It allows users to sign in or register using their social media accounts") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{  site_settings('social_login') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='social_login'
                                data-status='{{ site_settings('social_login') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}"
                                id="social_login">
                            <label class="form-check-label" for="social_login"></label>
                        </div>
                    </div>
               </li>
               <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Max Login Attempt Validation') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("Enabling this feature implements maximum login attempts validation, enhancing security by preventing unauthorized access through controlled login attempts for user accounts") }}
                            </small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{  site_settings('login_attempt_validation') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='login_attempt_validation'
                                data-status='{{ site_settings('login_attempt_validation') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}"
                                id="login_attempt_validation">
                            <label class="form-check-label" for="login_attempt_validation"></label>
                        </div>
                    </div>
               </li>
                <li
                    class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Email Verification') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("When enabled, this module prompts users to verify their email addresses during registration by clicking a link or entering a code sent to their email.") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('email_verification') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='email_verification'
                                data-status='{{ site_settings('email_verification') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}"
                                id="email_verification">
                            <label class="form-check-label" for="email_verification"></label>
                        </div>
                    </div>
                </li>
                <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('SEO Configuration') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("Enabling this feature activates SEO functionalities, optimizing online visibility and enhancing search engine performance for improved digital presence and accessibility") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('site_seo') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='site_seo'
                                data-status='{{ site_settings('site_seo') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}" id="site_seo">
                            <label class="form-check-label" for="site_seo"></label>
                        </div>
                    </div>
                </li>

                <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ translate('Frontend Preloader') }}</h6>
                        <p class="mb-0">
                            <small>{{ translate("Enabling this feature activates SEO functionalities, optimizing online visibility and enhancing search engine performance for improved digital presence and accessibility") }}</small>
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-switch form-switch-md" dir="ltr">
                            <input
                                {{ site_settings('frontend_preloader') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                type="checkbox" class="form-check-input status-update"
                                data-key='frontend_preloader'
                                data-status='{{ site_settings('frontend_preloader') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}" id="frontend_preloader">
                            <label class="form-check-label" for="frontend_preloader"></label>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

@endsection

