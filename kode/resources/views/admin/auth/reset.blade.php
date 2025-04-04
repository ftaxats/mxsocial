@extends('admin.layouts.auth')
@section('main_content')

<div class="row form-area justify-content-center align-items-stretch g-0">
    <div class="col-lg-6">
        <div class="form-wrapper auth">
        <div class="background-circles"></div>
            <div class="row mb-25 gy-5">
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
                        {{translate('Reset your password and regain access to your account')}}
                    </p>
                </div>
            </div>
            <form action="{{route('admin.password.update.request')}}" class="login-right-form" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-inner">
                            <label class="form-label" for="password-input">
                                {{translate("Password")}} <span class="text-danger" >*</span>
                            </label>
                            <input required  type="password"  name="password" placeholder='{{translate("Enter password")}}' id="password-input">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-inner">
                            <label class="form-label" for="confrim-password-input">
                                {{translate("Confirm Password")}} <span class="text-danger" >*</span>
                            </label>
                            <input required type="password" name="password_confirmation"  placeholder='{{translate("Enter Confirm password")}}' id="confrim-password-input">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-inner d-flex justify-content-center align-items-center">
                            <a class="forget-pass"  href="{{route('admin.login')}}">{{translate("Login")}}?</a>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="i-btn btn--primary btn--lg pill w-100">{{translate("Submit")}}</button>
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

