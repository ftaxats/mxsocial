@extends('layouts.master')
@push('styles')
<style nonce="{{ csp_nonce() }}">
    .payslip{
        border: 1px solid #eee;
        padding: 35px;
        border-radius: 12px;
        background: #f8f8f8;
        text-align: center;
    }

    @media (max-width: 768px){
        .payslip{
        padding: 20px;
    }
    }
    svg {
    width: 100px;
    display: block;
    margin: 35px auto 0;
    }

    .path {
    stroke-dasharray: 1000;
    stroke-dashoffset: 0;
        &.circle {
            -webkit-animation: dash 2s ease-in-out;
            animation: dash 2s ease-in-out;
        }
        &.line {
            stroke-dashoffset: 1000;
            -webkit-animation: dash 2s .5s ease-in-out forwards;
            animation: dash 2s .5s ease-in-out forwards;
        }
        &.check {
            stroke-dashoffset: -100;
            -webkit-animation: dash-check 2s .5s ease-in-out forwards;
            animation: dash-check 2s .35s ease-in-out forwards;
        }
    }

    p {
        text-align: center;
        font-size: 1.25em;
        &.success {
            color: #73AF55;
        }
        &.error {
            color: #D06079;
        }
    }


    @-webkit-keyframes dash {
        0% {
            stroke-dashoffset: 1000;
        }
        100% {
            stroke-dashoffset: 0;
        }
        }

        @keyframes dash {
        0% {
            stroke-dashoffset: 1000;
        }
        100% {
            stroke-dashoffset: 0;
        }
        }

        @-webkit-keyframes dash-check {
        0% {
            stroke-dashoffset: -100;
        }
        100% {
            stroke-dashoffset: 900;
        }
        }

        @keyframes dash-check {
        0% {
            stroke-dashoffset: -100;
        }
        100% {
            stroke-dashoffset: 900;
        }
    }
        .slip-list li{
            display:flex;
            align-items:center;
            justify-content: space-between;
            margin-bottom: 8px;
            background-color: #fff;
            padding: 10px 14px;
            border-radius: 5px;
            }
        .slip-list li span{
            display: inline-block;
        }
        .slip-list li span:first-child{
            color: var(--text-secondary);
            font-weight: 600;
        }
        .slip-list li span:last-child{
            font-weight: 400;
            color: var(--text-primary);
        }

</style>
@endpush

@section('content')
    <div class="container">
     <div class="row justify-content-center pt-110 pb-110">
         <div class="col-xl-9 col-lg-10 col-md-10">
             <div class="i-card-md ">
                 @php
                     $report = $response->log;
                 @endphp

                 <div class="card-body">
                     <div class="row justify-content-center align-items-center">
                         <div class="col-lg-7">
                             <div class="payslip">
                                 <div class="icon mb-60">
                                     @if($response->type  == 'SUCCESS')
                                         <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                              viewBox="0 0 130.2 130.2">
                                             <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6"
                                                     stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                             <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6"
                                                       stroke-linecap="round" stroke-miterlimit="10"
                                                       points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
                                         </svg>
                                         <p class="success">
                                             {{translate('Deposit Completed')}}!
                                         </p>
                                     @else
                                         <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                              viewBox="0 0 130.2 130.2">
                                             <circle class="path circle" fill="none" stroke="#D06079" stroke-width="6"
                                                     stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                             <line class="path line" fill="none" stroke="#D06079" stroke-width="6"
                                                   stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9"
                                                   x2="95.8" y2="92.3"/>
                                             <line class="path line" fill="none" stroke="#D06079" stroke-width="6"
                                                   stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38"
                                                   x2="34.4" y2="92.2"/>
                                         </svg>
                                         <p class="error"> {{translate('Payment Failed')}}!</p>

                                     @endif
                                 </div>
                                 <ul class="slip-list mb-5">
                                     <li>
                                         <span>{{translate('Date')}}</span>
                                         <span>{{ diff_for_humans($report->created_at)  }}</span>
                                     </li>

                                     <li>
                                         <span>{{translate('User')}}</span>
                                         <span> {{$report->user->name}} </span>
                                     </li>

                                     <li>
                                         <span>{{translate('Payment Method')}}</span>
                                         <span>   {{$report->method->name}} </span>
                                     </li>


                                     <li>
                                         <span>{{translate('TRX Number')}}</span>
                                         <span>   {{$report->trx_code}} </span>
                                     </li>


                                     <li>
                                         <span>{{translate('Receivable Amount')}}</span>
                                         <span>    {{num_format($report->amount,@$report->currency)}} </span>
                                     </li>


                                     <li>
                                         <span>{{translate('Payment Amount')}}</span>
                                         <span
                                             class="fw-bold">   {{num_format($report->final_amount,@$report->method->currency)}} </span>
                                     </li>


                                 </ul>
                                 <a href="{{ auth_user('web') ? route('user.home') : route('home')  }}"
                                    class="i-btn btn--lg btn--primary mx-auto"><i class="bi bi-house me-2"></i>
                                     {{translate('Back To Home')}}
                                 </a>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>

@endsection


