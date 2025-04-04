@extends('layouts.error')
@section('content')

<div class="col-lg-5">
    <div class="error-content">
        <h1>500</h1>
        <p>{{@translate('Server Error')}}</p>

        <div class="mt-lg-5 mt-4 d-flex align-items-center justify-content-center">
            <a href="{{route('home')}}" class="i-btn btn--primary btn--lg capsuled">
               {{@translate('Back To Home Page')}}
            </a>
        </div>
    </div>
</div>
<div class="col-lg-7">
    <div class="error-image">
        <img src="{{asset('assets/images/default/500.png')}}" alt="500.png" class="img-fluid">
    </div>
</div>

@endsection
