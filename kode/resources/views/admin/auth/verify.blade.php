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
                        {{translate('Verify your code to secure your account and proceed')}}
                    </p>
                </div>
            </div>
            <form action="{{route('admin.password.verify.code')}}" class="login-right-form" method="post">
                @csrf

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-inner">
                            <label for="code" class="form-label">
                                {{translate("Code")}} <span class="text-danger" >*</span>
                            </label>
                            <input   type="text" id="code" name="code" placeholder="{{ translate('Enter verification code')}}">
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-inner d-flex justify-content-center align-items-center">
                            <a class="forget-pass"  href="{{route('admin.login')}}">{{translate("Login")}}?</a>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="i-btn btn--primary pill btn--lg w-100">{{translate("Submit")}}</button>
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



