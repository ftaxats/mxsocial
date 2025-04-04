
@extends('admin.layouts.auth')
@section('main_content')
    <div class="row form-area justify-content-center align-items-stretch g-0">
        <div class="col-lg-6">
            <div class="form-wrapper auth">
            <div class="background-circles"></div>
                <div class="row gy-4">
                    <div class="col-md-12 text-center">
                        <a href="{{route('admin.home')}}" class="site-logo">
                            <img src="{{imageURL(@site_logo('site_logo')->file,'site_logo',true)}}" class="mx-auto" alt="{{@site_logo('site_logo')->file->name ?? 'site-logo.jpg'}}">
                        </a>
                    </div>
                    <div class="col-md-12 text-center">
                        <h4>
                            {{@translate($title)}}
                        </h4>
                        <p>
                            {{translate('Welcome back! Please login to your account.')}}
                        </p>
                    </div>
                </div>
                <form action="{{route('admin.authenticate')}}" class="login-right-form" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-inner">
                                <label for="username" class="form-label">
                                    {{translate("Username/Email")}} <span class="text-danger" >*</span>
                                </label>
                                <input type="text" name="login" required value="admin"  id="username" placeholder='{{translate("Enter Username or email")}}'>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-inner password-inner">
                                <label class="form-label" for="password">
                                    {{translate("Password")}} <span class="text-danger" >*</span>
                                </label>
                                <input required  type="password" value="123123" name="password" class="form-control pe-5 password" placeholder="Enter password" id="password">
                                <i id="toggle-password"  class="bi bi-eye-fill lh-1"></i>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-inner d-flex justify-content-center align-items-center">
                                <div class="checkbox-wrapper">
                                    <input  type="checkbox" name="remember_me"  id="auth-remember-check">
                                    <label for="auth-remember-check">
                                        {{translate("Remember me")}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="i-btn btn--primary btn--lg pill w-100">{{translate("Sign In")}}</button>
                        </div>
                        <div class="col-12 text-center mt-3">
                            <a class="forget-pass"  href="{{route('admin.password.request')}}">{{translate("Forgot password")}}?</a>
                        </div>
                    </div>
                </form>
                <div class="auth-footer">
                    {{site_settings("copy_right_text")}}
                </div>
            </div>
        </div>
    </div>

@endsection
@push('script-push')
<script nonce="{{ csp_nonce() }}">
    'use strict'
    $(document).on('click','#toggle-password',function(e){
        e.preventDefault()
        var passwordInput = $("#password");
        var passwordFieldType = passwordInput.attr('type');
        if (passwordFieldType == 'password') {
        passwordInput.attr('type', 'text');
           $("#toggle-password").removeClass('bi bi-eye-fill').addClass('bi bi-eye-slash-fill');
        } else {
        passwordInput.attr('type', 'password');
          $("#toggle-password").removeClass('bi bi-eye-slash-fill').addClass('bi bi-eye-fill');
        }
   });
</script>

@endpush
