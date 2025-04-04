@extends('layouts.error')
@section('content')

<div class="col-12">

    <div>

        <div class="row justify-content-center pb-4" >
            <div class="col-lg-6">
                <div class="error-image ">
                    <img src="{{asset('assets/images/default/access-denied.png')}}" alt="{{translate('Invalid license')}}" class="img-fluid">
                </div>

            </div>
        </div>
    
        <div class="error-content px-4">
            <h1 class="mb-3 fs-3 invalid-license-title">
                {{
                    translate('Access Denied')
                }}
            </h1>
            <p class="fs-5 text-muted">

               
                 {{
                    translate('Your input contains potentially harmful or invalid data that cannot be processed. Please review your submission and try again with valid information.')
                 }}

            </p>
            <div class="mt-4 d-flex align-items-center justify-content-center">
                  @php
  

                        $redirectUrl = route('home');
                        try {
                            $previousUrl = url()->previous();
                            $redirectUrl = (request()->headers->get('referer') && Str::startsWith($previousUrl, url('/')))
                                ? $previousUrl
                                : route('home');
                        } catch (\Throwable $th) {
            
                        }
 
        
                  @endphp
                <a href="{{  $redirectUrl }}" class="i-btn btn--primary btn--lg capsuled">
                   {{translate('Back')}}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
