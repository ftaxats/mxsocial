@extends('layouts.master')
@section('content')

@php
    $authContent     =  get_content("content_authentication_section")->first();
@endphp

<section class="auth">
    <div class="container-fluid px-0">
        <div class="auth-wrapper">
          <div class="row g-0">
              @include("user.partials.auth_slider")

              <div class="col-xl-7 col-lg-7 order-lg-1 order-0">
                <div class="auth-right">
                  <div class="auth-content">
                    <a href="{{route('home')}}" class="site-log text-center mb-4 d-inline-block">
                        <img src="{{imageURL(@site_logo('user_site_logo')->file,'user_site_logo',true)}}" alt="{{@site_logo('user_site_logo')->file->name}}">
                    </a>
                    <h2>

                        {{Arr::get($meta_data,'title',translate("Verify your account"))}}
                    </h2>

                    <p>
                        {{@$authContent->value->description }}
                    </p>

                    <form action="{{route($route)}}" class="auth-form otp-form" method="post" id="otpForm">

                            @if(session()->has("user_identification") && \Carbon\Carbon::now()  <  session()->get("otp_expire_at"))
                                <div class="otp-expired-message">
                                    {{translate("Your OTP will expire at")}} {{get_date_time(session()->get("otp_expire_at"))}}
                                </div>
                            @endif

                            @csrf

                            <input hidden type="text" name="otp_code" id="otpCode">

                            <div class="otp-field">
                                <input type="text" maxlength="1" />
                                <input type="text" maxlength="1" />
                                <input type="text" maxlength="1" />
                                <input type="text" maxlength="1" />
                                <input type="text" maxlength="1" />
                                <input type="text" maxlength="1" />
                            </div>

                          <div>
                              <button class="i-btn btn--auth btn--lg capsuled w-100" type="submit">
                                    {{translate("Verify")}}
                              </button>
                              @if(session()->has("user_identification") &&  \Carbon\Carbon::now()  >  session()->get("otp_expire_at"))
                                  <a href="{{route('auth.otp.resend')}}"
                                      class="i-btn btn--primary btn--lg capsuled w-100 mt-3"
                                      type="submit">
                                      {{translate("Resend Otp")}}
                                  </a>
                              @endif
                          </div>
                    </form>
                  </div>


                  <div class="glass-bg"></div>
                </div>
              </div>
          </div>
        </div>
    </div>
</section>

@endsection
