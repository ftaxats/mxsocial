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
                        <a href="{{ route('home') }}" class="site-log text-center mb-4 d-inline-block">

                          <img src="{{imageURL(@site_logo('user_site_logo')->file,'user_site_logo',true)}}" alt="{{@site_logo('user_site_logo')->file->name}}">

                        </a>
                        <h2>
                            {{Arr::get($meta_data,'title',translate("Verify your account"))}}
                        </h2>

                        <p>
                            {{@$authContent->value->description }}
                        </p>
                        <form class="auth-form" action="{{route('auth.password.email')}}" method="POST" id="login-form">
                          @csrf

                            <div class="auth-input">
                                <label for="email">Email</label>
                                <input required type="text" id="email" name="email"  placeholder="{{translate('Enter email')}}" />
                                <span class="auth-input-icon">
                                    <i class="bi bi-envelope"></i>
                                </span>
                            </div>

                            <div>
                                <button type="submit"    class="i-btn btn--auth btn--lg capsuled w-100">
                                    {{trans("Submit")}}
                                </button>
                            </div>
                        </form>

                        <div class="have-account">
                            <p>
                                {{translate("Already Have An Account")}} ?
                                <a href="{{route('auth.login')}}">
                                    {{translate("Login")}}
                                </a>
                            </p>
                        </div>

                  </div>

                  <div class="glass-bg"></div>
                </div>
            </div>

        </div>
      </div>
    </div>
</section>

@endsection



