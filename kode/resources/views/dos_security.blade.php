@extends('layouts.master')
@section('content')
   <div class="recapture">
    <div class="container">
      <div class="recapture-wrapper">
        <div class="recapture-content">
          <p>{{trans("default.verify_yourself")}}</p>
          <div
            class="d-flex align-items-center justify-content-between gap-3">

                <a id='genarate-captcha' class="align-middle justify-content-center cursor-pointer">
                    <img class="captcha-default d-inline me-2 cursor-pointer  " src="{{ route('captcha.genarate',1) }}" id="default-captcha" alt="image">
                <button class="recapture-reload">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      version="1.1"
                      xmlns:xlink="http://www.w3.org/1999/xlink"
                      x="0"
                      y="0"
                      viewBox="0 0 489.533 489.533">
                      <g>
                        <path
                          d="M268.175 488.161c98.2-11 176.9-89.5 188.1-187.7 14.7-128.4-85.1-237.7-210.2-239.1v-57.6c0-3.2-4-4.9-6.7-2.9l-118.6 87.1c-2 1.5-2 4.4 0 5.9l118.6 87.1c2.7 2 6.7.2 6.7-2.9v-57.5c87.9 1.4 158.3 76.2 152.3 165.6-5.1 76.9-67.8 139.3-144.7 144.2-81.5 5.2-150.8-53-163.2-130-2.3-14.3-14.8-24.7-29.2-24.7-17.9 0-31.9 15.9-29.1 33.6 17.4 109.7 118.7 192 236 178.9z"
                          opacity="1"
                          data-original="#000000"></path>
                      </g>
                    </svg>
                  </button>
                </a>
          </div>

          <form action="{{route('dos.security.verify')}}" class="recapture-form"  method="post">
            @csrf
              <input type="text" name="captcha" id="captcha"  placeholder="{{translate('Enter captcha code')}}">
                <button type="submit" class="i-btn btn--primary btn--lg capsuled">
                    {{translate("Verify")}}
                </button>
          </form>
        </div>
      </div>
    </div>

        <div class="recapture-bg">
            <img src="{{asset('assets/images/default/security.jpg')}}" alt="{{translate('Security image')}}">
        </div>
  </div>


@endsection



