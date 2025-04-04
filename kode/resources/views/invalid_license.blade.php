@extends('layouts.error')
@section('content')

<div class="col-lg-5">
    <div class="error-content">
        <h1 class="mb-2 invalid-license-title">
            {{trans('default.invalid_license')}}
        </h1>
        <p>
           {{trans('default.invalid_license_note')}}
        </p>
        <div class="mt-lg-5 mt-4 d-flex align-items-center justify-content-center">
            <a href="{{route('invalid.purchase',['verification_view' => true])}}" class="i-btn btn--primary btn--lg capsuled">
               {{@translate('Verify License')}}
            </a>
        </div>
    </div>
</div>

<div class="col-lg-7">
    <div class="error-image">
        <img src="{{asset('assets/images/default/invalid-license.jpg')}}" alt="{{translate('Invalid license')}}" class="img-fluid">
    </div>
</div>
@endsection
