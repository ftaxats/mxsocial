@extends('layouts.master')
@section('content')

    <div class="row pt-110">
        <div class="col-xl-8 col-lg-8 col-md-10 mx-auto">
            <div class="box secbg">
                <div class="box-body text-center">
                    <div id="paypal-button-container"></div>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('script-push')

<script nonce="{{ csp_nonce() }}" src="https://www.paypal.com/sdk/js?client-id={{$data->cleint_id}}">
</script>
<script nonce="{{ csp_nonce() }}">
    "use strict";
        paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [
                    {
                        description: "{{ $data->description }}",
                        custom_id: "{{$data->custom_id}}",
                        amount: {
                            currency_code: "{{$data->currency}}",
                            value: "{{$data->amount}}",
                            breakdown: {
                                item_total: {
                                    currency_code: "{{$data->currency}}",
                                    value: "{{$data->amount}}"
                                }
                            }
                        }
                    }
                ]
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (details) {
                var trx = "{{$data->custom_id}}";
                window.location = '{{ url('ipn')}}/' + trx + '/' + details.id

            });
        }
    }).render('#paypal-button-container');
</script>
@endpush
