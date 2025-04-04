@extends('layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-8 col-md-10">
            <div class="i-card-md ">
                <div class="card-header">
                    <div class="d-flex justify-content-start align-items-center gap-4">
                        <div class="image avatar-md">
                            <img src='{{imageURL(@$log->method->file,"payment_method",true)}}' alt="{{@$log->method->file->name ?? @$log->method->name.'.jpg'}}" >
                        </div>
                        <h4 class="card-title">{{@$log->method->name}}</h4>
                    </div>
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
                                <li class="list-group-item ">
                                    <p>
                                        {{translate('You will get')}}:
                                    </p>

                                    <h6>{{num_format($log->amount,$log->currency)}}</h6>
                                </li>
                            </ul>

                            <button type="button" class="i-btn btn--lg btn--primary mt-4 capsuled" id="btn-confirm">
                                {{translate("Pay Now")}}
                            </button>

                            <form action="{{ route('ipn', [ $log->trx_code]) }}" method="POST" class="form">
                                @csrf
                                <script nonce="{{ csp_nonce() }}"
                                    src="//js.paystack.co/v1/inline.js"
                                    data-key="{{ $data->key }}"
                                    data-email="{{ $data->email }}"
                                    data-amount="{{$data->amount}}"
                                    data-currency="{{$data->currency}}"
                                    data-ref="{{ $data->ref }}"
                                    data-custom-button="btn-confirm">
                                </script>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


