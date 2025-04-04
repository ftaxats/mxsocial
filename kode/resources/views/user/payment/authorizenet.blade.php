@extends('layouts.master')
@section('content')

   @php
       $user = $log->user;
   @endphp

    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-10">
            <div class="i-card-md ">
                <div class="card-header">

                    <div class="image avatar-md">
                        <img src='{{imageURL(@$log->method->file,"payment_method",true)}}' alt="{{@$log->method->file->name ?? @$log->method->name.'.jpg'}}" >
                    </div>
                    <h4 class="card-title">
                         {{@$log->method->name}}
                    </h4>
                </div>

                <div class="card-body">
                    <div class="card-wrapper mb-3"></div>
                    <form role="form" id="payment-form" method="{{$data->method}}" action="{{$data->url}}">
                        @csrf
                        <input type="hidden" value="{{$data->track}}" name="track">
                        <div class="row gx-4 gy-3">
                            <div class="col-md-6">
                                <label class="form-label">{{translate('Name on Card')}}</label>
                                <div class="input-group  contact-form-group">
                                    <input type="text" class="form-control form--control" name="name" value="{{ old('name') }}" required autocomplete="off" autofocus/>
                                    <span class="input-group-text"><i class="fa fa-font"></i></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{translate('Card Number')}}</label>
                                <div class="input-group contact-form-group">
                                    <input type="tel" class="form-control form--control" name="cardNumber" autocomplete="off" value="{{ old('cardNumber') }}" required autofocus/>
                                    <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label>{{translate('Expiration Date')}}</label>
                                <input type="tel" class="form-control form--control" name="cardExpiry" value="{{ old('cardExpiry') }}" autocomplete="off" required/>
                            </div>

                            <div class="col-md-6">
                                <label>{{translate('CVC Code')}}</label>
                                <input type="tel" class="form-control form--control" name="cardCVC" value="{{ old('cardCVC') }}" autocomplete="off" required/>
                            </div>
                        </div>

                        <div class="row mt-2"></div>
                        <button type="submit" class="i-btn btn--lg btn--primary capsuled mt-4" id="btn-confirm">
                            {{translate("Pay Now")}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('script-push')

<script nonce="{{ csp_nonce() }}" src="{{ asset('assets/global/js/card.js') }}"></script>

<script nonce="{{ csp_nonce() }}">
    (function ($) {
        "use strict";
        var card = new Card({
            form: '#payment-form',
            container: '.card-wrapper',
            formSelectors: {
                numberInput: 'input[name="cardNumber"]',
                expiryInput: 'input[name="cardExpiry"]',
                cvcInput: 'input[name="cardCVC"]',
                nameInput: 'input[name="name"]'
            }
        });
    })(jQuery);
</script>


@endpush
