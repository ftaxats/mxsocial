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
                            <ul class="list-group text-center">
                                <li class="list-group-item d-flex justify-content-between primary-bg ">
                                   {{translate('You have to pay')}}:
                                   <strong>{{num_format($log->final_amount,$log->method->currency,2)}}  </strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between primary-bg ">
                                   {{translate('You will get')}}:

                                    <strong>{{num_format($log->amount,$log->currency,2)}}</strong>
                                </li>
                            </ul>
                            <button type="button" class="i-btn btn--lg btn--primary capsuled mt-4" id="btn-confirm"
                            onClick="pay()">{{translate('Pay Now')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection


@push('script-include')
   <script nonce="{{ csp_nonce() }}" src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
   "use strict";
    var btn = document.querySelector("#btn-confirm");
    btn.setAttribute("type", "button");
    const API_publicKey = "{{$data->API_publicKey ?? ''}}";

    function pay() {
        var x = getpaidSetup({
            PBFPubKey: API_publicKey,
            customer_email: "{{$data->customer_email ?? 'example@example.com'}}",
            amount: "{{ $data->amount ?? '0' }}",
            customer_phone: "{{ $data->customer_phone ?? '0123' }}",
            currency: "{{ $data->currency ?? 'USD' }}",
            txref: "{{ $data->txref ?? '' }}",
            onclose: function () {
            },
            callback: function (response) {
                let txref = response.tx.txRef;
                let status = response.tx.status;
                window.location = '{{ url('ipn') }}/' + txref + '/' + status;
            }
        });
    }
</script>
@endpush








