@extends('admin.layouts.master')
@section('content')
<div class="row g-4 mb-4">

    @if(request()->routeIs("admin.user.show"))
        <div class="col-xl-3">
            <div class="i-card-md h-440 mb-4">
                <div class="card--header">
                    <h4 class="card-title">
                        {{ translate('User Information') }}
                    </h4>
                </div>
                <div class="card-body">

                    <div class="d-flex flex-column align-items-center justify-content-start border--bottom mb-4 gap-2 bg--light rounded-3 gap-3 p-3">
                        <div class="user-profile-image bg--light">
                            <img src="{{ imageURL($user->file,'profile,user',true) }}" alt="profile.jpg">
                        </div>
                        <div class="text-center">
                            <h6 class="mb-1">
                                {{$user->name}}
                            </h6>
                            <p class="mb-0"> {{$user->email}}</p>
                        </div>
                    </div>

                    <ul class="admin-info-list">

                        <li><span>{{ translate('Balance') }} : </span>
                            <span class="i-badge-solid info"> {{num_format($user->balance,base_currency())}} @if(session('currency') && base_currency()->code != session('currency')?->code) -
                                {{num_format(
                                    number : $user->balance,
                                    calC   : true
                                )}} @endif</span>
                        </li>

                        @if($user->affiliates->count() > 0)
                            <li><span>{{ translate('Affiliate Earnings') }} :</span>
                                @php
                                      $earnings =  $user->affiliates->sum("commission_amount");
                                @endphp
                                <a href="{{route('admin.affiliate.report.list',['user' => $user->username])}}">

                                    <span class="i-badge-solid success"> {{num_format($earnings,base_currency())}} @if(session('currency') && base_currency()->code != session('currency')?->code) -
                                        {{num_format(
                                            number :$earnings,
                                            calC   : true
                                        )}} @endif
                                    </span>
                                </a>
                            </li>
                        @endif

                        @if($user->referral)
                            <li><span>{{ translate('Refferd By') }} :</span>
                                <a href="{{route('admin.user.show',$user->referral->uid)}}">{{ $user->referral?->name }}
                               </a>
                            </li>
                        @endif

                        <li><span>{{ translate('Name') }} :</span> {{ $user->name }}</li>
                        <li><span>{{ translate('Username') }} :</span> {{ $user->user_name ?? '--' }}</li>
                        <li><span>{{ translate('Phone') }} :</span> {{ $user->phone }}</li>
                        <li><span>{{ translate('Email') }} :</span> {{ $user->email }}</li>
                        <li><span>{{ translate('Country') }} :</span> {{ @$user->country->name  }}</li>

                    </ul>

                    <a href="{{route('admin.user.edit',$user->uid)}}" class="i-btn btn--md btn--primary w-100 update-profile" ><i class="bi bi-person-gear fs-18 me-3"></i>
                            {{translate("Update Profile")}}
                    </a>
                </div>
            </div>

            <div class="subscription-card i-card-sm">
                <div class="d-flex justify-content-between mb-40 ">

                    @if(!$user->runningSubscription)
                        <div class="text-center w-100">
                                <h5 class="text-white">
                                    {{translate("No subscription")}}
                                </h5>
                        </div>
                    @else

                        @php
                            $package  =  optional($user->runningSubscription)->package ;
                            $duration =  ucfirst(t2k(Arr::get(array_flip(App\Enums\PlanDuration::toArray()),$package->duration , 'Pending')));
                        @endphp

                        <div class="content text-start">
                            <span>
                                {{ $duration }}
                            </span>
                            <h5>
                                {{$package?->title}}
                            </h5>
                            <p>{{translate("Commision")}} - {{($package->affiliate_commission)}}%  </p>
                            <p>{{translate("Earning")}} -  {{@num_format(
                                number : $package->total_subscription_income??0,
                                calC   : true
                            )}}</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-envelope-paper"></i>
                        </div>

                    @endif

                </div>

                <a  href="javascript:void(0)"  class="i-btn btn--md btn--white mx-auto d-block plan-upgrade">
                        <i class="bi bi-arrow-repeat me-3"></i>
                        {{translate("Update Subscription")}}
                </a>

            </div>
        </div>

        <div class="col-xl-9">
            <div class="row row-cols-xxl-3 row-cols-xl-3 row-cols-lg-4 row-cols-md-2 row-cols-sm-2 row-cols-1 g-3 mb-4">

                @php
                    $cards =  [
                                [
                                    "title"  => translate("Total Subscription"),
                                    "class"  => 'col',
                                    "total"  => $user->subscriptions->count(),
                                    "icon"   => '<i class="las la-subscript"></i>',
                                    "bg"     => 'primary',
                                    "url"    => route('admin.subscription.report.list',['user' => $user->username])
                                ],
                                [
                                    "title"  => translate("Total Tickets"),
                                    "class"  => 'col',
                                    "total"  => $user->tickets->count(),
                                    "icon"   => '<i class="las la-sms"></i>',
                                    "bg"     => 'info',
                                    "url"    => route('admin.ticket.list',['user' => $user->username])
                                ],
                                [
                                    "title"  => translate("Deposit logs"),
                                    "class"  => 'col',
                                    "total"  => $user->paymentLogs->count(),
                                    "icon"   => '<i class="las la-hryvnia"></i>',
                                    "bg"     => 'success',
                                    "url"    => route('admin.deposit.report.list',['user' => $user->username])
                                ],
                                [
                                    "title"  => translate("Withdraw logs"),
                                    "class"  => 'col',
                                    "total"  => $user->withdraws->count(),
                                    "icon"   => '<i class="las la-hryvnia"></i>',
                                    "bg"     => 'warning',
                                    "url"    => route('admin.withdraw.report.list',['user' => $user->username])
                                ],
                                [
                                    "title"  => translate("Credit logs"),
                                    "class"  => 'col',
                                    "total"  => $user->creditLogs->count(),
                                    "icon"   => '<i class="las la-bars"></i>',
                                    "bg"     => 'danger',
                                    "url"    => route('admin.credit.report.list',['user' => $user->username])
                                ],
                                [
                                    "title"  => translate("Transaction logs"),
                                    "class"  => 'col',
                                    "total"  => $user->transactions->count(),
                                    "icon"   => '<i class="las la-bars"></i>',
                                    "bg"     => 'danger',
                                    "url"    => route('admin.transaction.report.list',['user' => $user->username])
                                ]
                            ];
                @endphp

                @include("admin.partials.report_card")


            </div>


            <div class="row">
                <div class="col-lg-12">
                    <div class="i-card-md mb-4">
                        <div class="card--header text-end">
                            <h4 class="card-title">
                                 {{ translate('Social post statistics (Current Year)')}}
                            </h4>
                       </div>
                        <div class="card-body">
                            <div id="postReport"></div>
                        </div>
                    </div>
                    <div class="i-card-md">
                        <div class="card-body">
                            <div class="d-flex gap-2">
                                <button type="button"   class="i-btn btn--md success deposit-balance flex-grow-1">
                                    <i class="las la-plus me-1"></i>  {{translate('Deposit')}}
                                </button>
                                <button type="button"   class="i-btn btn--md danger withdraw-balance flex-grow-1">
                                    <i class="las la-minus me-1"></i>  {{translate('Withdraw')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif




    @if(request()->routeIs("admin.user.edit"))
        <div class="col-12">
            <div class="i-card-md">
                <div class="card--header">
                    <h4 class="card-title">{{ translate('Profile Update') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.user.update')}}"  method="post" enctype="multipart/form-data">
                        @csrf
                            <input type="hidden" value="{{$user->id}}" name="id" id="id" class="form-control" >
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="name">
                                            {{translate('Name')}} <span class="text-danger">*</span>
                                        </label>
                                        <input required type="text" name="name" value="{{$user->name}}" id="name"  placeholder="{{translate('Enter Name')}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="username">
                                            {{translate('Username')}}
                                            <small class="text-danger">*</small>
                                        </label>
                                        <input type="text" value="{{$user->username}}" name="username" id="username" placeholder="{{translate('Enter Username')}}" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="email">
                                            {{translate('Email')}}
                                            <small class="text-danger">*</small>
                                        </label>
                                        <input type="email"  value="{{$user->email}}" name="email" id="email"  placeholder="{{translate('Enter Email')}}" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="phone">
                                            {{translate('Phone')}}
                                        </label>
                                        <input type="text"  value="{{$user->phone}}" name="phone" id="phone"  placeholder="{{translate('Enter Phone')}}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="country">
                                            {{translate('Country')}}
                                        </label>
                                        <select name="country_id" id="country">
                                            <option value="">
                                                {{translate('Select Country')}}
                                            </option>
                                            @foreach ($countries as $country )
                                                <option {{$user->country_id == $country->id ? "selected" :""}} value="{{$country->id}}">
                                                    {{$country->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @foreach (['city','state','postal_code','address'] as $address_key )
                                        <div class="col-lg-6">
                                            <div class="form-inner">
                                                <label for="{{$address_key}}">
                                                        {{k2t($address_key)}}
                                                </label>
                                                <input placeholder=" {{k2t($address_key)}} " id="{{$address_key}}" name="address[{{$address_key}}]" value="{{@$user->address->$address_key}}" type="text">
                                            </div>
                                        </div>
                                @endforeach

                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="image">
                                            {{translate('Profile Image')}}
                                        </label>
                                        <input data-size = "{{config('settings')['file_path']['profile']['user']['size']}}" id="image" name="image" type="file" class="preview">
                                        <div class="mt-2 payment-preview image-preview-section">
                                            <img src="{{imageURL($user->file,'profile,user',true) }}" alt="{{@$user->file->name}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="password">
                                            {{translate('Password')}}
                                        </label>
                                        <input  type="text" name="password" id="password"   placeholder="{{translate('Enter Password')}}">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="password_confirmation">
                                            {{translate('Confirm Password')}}

                                        </label>
                                        <input type="text" id="password_confirmation" name="password_confirmation"   placeholder="{{translate('Enter Confrim Password')}}" >
                                    </div>
                                </div>

                                <div class="col-xl-7 col-lg-12">
                                    <div class="form-inner">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <input id="email_verified" value="{{App\Enums\StatusEnum::true->status()}}" {{$user->email_verified_at ? "checked" :""}} class="form-check-input me-1" name="email_verified" type="checkbox">
                                                <label for="email_verified" class="form-check-label me-3">
                                                    {{translate('Email Verified')}}
                                                </label>
                                            </div>
                                            <div class="col-sm-4">
                                                <input id="auto_subscription" value="{{App\Enums\StatusEnum::true->status()}}" {{$user->auto_subscription ? "checked" :""}} class="form-check-input me-1" name="auto_subscription" type="checkbox"   >
                                                <label for="auto_subscription" class="form-check-label me-3">
                                                    {{translate('Auto Subscription')}}
                                                </label>
                                            </div>
                                            <div class="col-sm-4">
                                                <input id="is_kyc_verified" value="{{App\Enums\StatusEnum::true->status()}}" {{$user->is_kyc_verified ? "checked" :""}} class="form-check-input me-1" name="is_kyc_verified" type="checkbox"   >
                                                <label for="is_kyc_verified" class="form-check-label me-3">
                                                    {{translate('KYC Verified')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                                        {{translate("Submit")}}
                                    </button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection


@section('modal')
    @include('admin.partials.modal.balance_update');
    @include('admin.partials.modal.plan_update');
@endsection

@if(!request()->routeIs("admin.user.edit"))
    @push('script-include')
        <script  src="{{asset('assets/global/js/apexcharts.js')}}"></script>
    @endpush
@endif

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";

        $(".select2").select2({
            placeholder:"{{translate('Select Item')}}",
            dropdownParent: $("#planUpdate")
        })

        $("#country").select2({
            placeholder:"{{translate('Select Country')}}",
        })

        $(".select-method").select2({
			dropdownParent: $("#balanceModal")
		})

        var modal = $('#balanceModal')

        $(document).on('click','.withdraw-balance',function(e){

            e.preventDefault()
            $('.modal-title').html("Withdraw Balance");
            $('#type').val("{{App\Enums\BalanceTransferType::WITHDRAW->value}}");
            $('.deposit-method').addClass('d-none');
            $('.withdraw-method').removeClass('d-none');
            modal.modal('show')
        });

        $(document).on('click','.deposit-balance',function(e){
            e.preventDefault()
            $('.modal-title').html("Deposit Balance")
            $('#type').val("{{App\Enums\BalanceTransferType::DEPOSIT->value}}");
            $('.deposit-method').removeClass('d-none');
            $('.withdraw-method').addClass('d-none');
            $('#balanceModal').modal('show')
        })

        $(document).on('click','.plan-upgrade',function(e){
            e.preventDefault()
            $('#planUpdate').modal('show')
        })


        @if(!request()->routeIs("admin.user.edit"))
            var options = {
                chart: {
                    nonce:"{{ csp_nonce() }}",
                    height: 300,
                    type: "line",
                    toolbar: {
                       show: false
                    }

                },
            dataLabels: {
                enabled: false,
            },
            colors: ['var(--color-info)','var(--color-primary)','var(--color-success)' ,"var(--color-danger)"],
            series: [
                {
                name: "{{ translate('Total Post') }}",
                data: @json(array_column($graph_data , 'total')),
                },
                {
                name: "{{ translate('Success Post') }}",
                data: @json(array_column($graph_data , 'success')),
                },
                {
                name: "{{ translate('Pending Post') }}",
                data: @json(array_column($graph_data , 'pending')),
                },
                {
                name: "{{ translate('Schedule Post') }}",
                data: @json(array_column($graph_data , 'schedule')),
                },
                {
                name: "{{ translate('Failed Post') }}",
                data: @json(array_column($graph_data , 'failed')),
                }

            ],
            xaxis: {
                categories: @json(array_keys($graph_data)),
            },

            tooltip: {
                    shared: false,
                    intersect: true,

                },
            markers: {
                size: 6,
            },
            stroke: {
                width: [4, 4],
            },
            legend: {
                horizontalAlign: "left",
                offsetX: 40,
            },
            };

            var chart = new ApexCharts(document.querySelector("#postReport"), options);
            chart.render();
        @endif


	})(jQuery);
</script>
@endpush
