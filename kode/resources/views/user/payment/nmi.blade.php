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
                    <form role="form" id="payment-form" method="{{$data->method}}" action="{{$data->url}}">
                        @csrf
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label">{{translate('Card Number')}}</label>
                                <div class="input-group">
                                    <input type="tel" class="form-control form--control" name="billing-cc-number" autocomplete="off" value="{{ old('billing-cc-number') }}" required autofocus/>
                                    <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label>{{translate('Expiration Date')}}</label>
                                <input type="tel" class="form-control form--control" name="billing-cc-exp" value="{{ old('billing-cc-exp') }}" placeholder="e.g. MM/YY" autocomplete="off" required/>
                            </div>

                            <div class="col-md-6">
                                <label>{{translate('CVC Code')}}</label>
                                <input type="tel" class="form-control form--control" name="billing-cc-cvv" value="{{ old('billing-cc-cvv') }}" autocomplete="off" required/>
                            </div>
                        </div>

                        <button type="submit" class="i-btn btn--lg btn--primary capsuled mt-4" id="btn-confirm">
                            {{translate("Pay Now")}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


