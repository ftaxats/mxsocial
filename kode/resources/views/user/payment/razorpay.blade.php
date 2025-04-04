@extends('layouts.master')
@section('content')

<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-10">
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
                <div class="row align-items-center">
                    <div class="col-12">
                        <ul class="payment-details list-group">
                            <li class="list-group-item">
                                <p>
                                    {{translate('You have to pay')}}:
                                </p>
                               <h6>{{num_format($log->final_amount,$log->method->currency)}}  </h6>

                            </li>
                            <li class="list-group-item">
                                <p>
                                    {{translate('You will get')}}:
                                </p>

                                <h6>{{num_format($log->amount,$log->currency)}}</h6>
                            </li>
                        </ul>


                        <form action="{{$data->url}}" method="{{$data->method}}" class="form">
                            @csrf
                            <script nonce="{{ csp_nonce() }}" nonce="{{ csp_nonce() }}" src="{{$data->checkout_js}}"
                                    @foreach($data->val as $key => $value )
                                        data-{{$key}}="{{$value}}"
                                @endforeach >
                            </script>
                            <input type="hidden" custom="{{$data->custom}}" name="hidden">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('script-push')

<script nonce="{{ csp_nonce() }}">
    "use strict";
    $(document).ready(function () {

        $('input[type="submit"]').addClass("i-btn btn--lg btn--primary mt-4");
    })
</script>

@endpush
