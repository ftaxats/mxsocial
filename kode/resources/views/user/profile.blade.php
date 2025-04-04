@extends('layouts.master')
@section('content')

@php
    $user = auth_user('web')->load(['runningSubscription','runningSubscription.package','affilateUser','affiliates']);
    $subscription = $user->runningSubscription;
    $package = @$user->runningSubscription?->package;
    $webhookAccess = @optional($user->runningSubscription->package->social_access)->webhook_access;
    $affiliateLogs = $user->affiliates;
@endphp

<div class="i-card mb-4">
    <div class="row g-4">
        <div class="col-xxl-9 col-xl-8 col-lg-8">
            <div class="d-flex align-items-start justify-content-start flex-sm-nowrap flex-wrap gap-lg-4 gap-3">
                <div class="avatar-100 profile-picture">
                    <img src="{{imageUrl(@$user->file,'profile,user',true) }}" class="rounded-50"
                        alt="{{@$user->file->name ?? 'profile.jpg'}}">
                </div>
                <div class="text-start">
                    <h4>
                        {{@$user->name}}
                    </h4>
                    <p class="fs-14"> {{translate('Joined On')}}
                        {{get_date_time($user->created_at,"F j, Y")}},{{get_date_time($user->created_at," g a")}}</p>
                    <div class="mt-4">
                        <div class="fs-18"><span class="text--dark fw-bold">{{translate('Email')}} :</span>
                            {{$user->email}} </div>
                        <div class="fs-18"><span class="text--dark fw-bold"> {{translate('Phone')}} :</span>
                            {{$user->phone}}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-4 col-lg-4">
            <div class="p-lg-4 p-3 bg-light radius-16 border">
                <h5 class="mb-2 fw-normal">
                    {{translate('Balance')}}
                </h5>
                <h3 class="fs-24"> {{num_format(number:$user->balance,calC:true)}} </h3>
                <div class="d-flex justify-content-start gap-3 mt-4">
                    <a href="{{route('user.withdraw.create')}}" class="i-btn btn--lg btn--primary capsuled">
                        {{translate('Withdraw')}}</a>
                    <a href="{{route('user.deposit.create')}}" class="i-btn btn--lg btn--primary capsuled">
                        {{ translate('Deposit')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="i-card">
    <div class="plan-detail">
        <div class="container-fluid px-0">
            <div>
                <ul class="nav nav-tabs style-2 mb-30 profile--tab" id="settingTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-profile" data-bs-toggle="tab"
                            data-bs-target="#tab-profile-pane" type="button" role="tab" aria-controls="tab-profile-pane"
                            aria-selected="true">
                            {{translate('Profile')}}
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-password" data-bs-toggle="tab"
                            data-bs-target="#tab-password-pane" type="button" role="tab"
                            aria-controls="tab-password-pane" aria-selected="false">
                            {{translate('Password')}}
                        </button>
                    </li>

                    @if(site_settings("affiliate_system") == App\Enums\StatusEnum::true->status())
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-affiliate" data-bs-toggle="tab"
                                data-bs-target="#tab-affiliate-pane" type="button" role="tab"
                                aria-controls="tab-affiliate-pane"
                                aria-selected="false">{{translate("Affiliate Configuration")}}</button>
                        </li>
                    @endif


                    @if($user->runningSubscription && $package)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-subscription" data-bs-toggle="tab"
                            data-bs-target="#tab-subscription-pane" type="button" role="tab"
                            aria-controls="tab-subscription-pane"
                            aria-selected="false">{{translate("Current Plan")}}</button>
                    </li>
                    @endif


                    @if($webhookAccess == App\Enums\StatusEnum::true->status())
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-webhook" data-bs-toggle="tab"
                            data-bs-target="#tab-webhook-pane" type="button" role="tab" aria-controls="tab-webhook-pane"
                            aria-selected="false">{{translate("Webhook Configuration")}}</button>
                    </li>
                    @endif
                </ul>
                <div class="tab-content" id="settingTabContent">
                <div class="tab-pane fade show active" id="tab-profile-pane" role="tabpanel"
                    aria-labelledby="tab-profile" tabindex="0">
                    <div class="pb-2">
                        <p>{{translate('Keep your profile information up-to-date to ensure seamless communication and a personalized experience. Update your details below')}}</p>
                    </div>

                    <form action="{{route('user.profile.update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="name">
                                        {{translate("Name")}} <span class="text--danger">*</span>
                                    </label>
                                    <input required type="text" name="name" value="{{$user->name}}" id="name" />
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="Username">{{translate("Username")}} <span class="text--danger">*</span></label>
                                    <input required type="text" name="username" value="{{$user->username}}"
                                        id="Username"/>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="email">{{translate("email")}}</label>
                                    <input type="text" name="email" value="{{$user->email}}" id="email" />
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="phone">{{translate("Phone")}}</label>
                                    <input type="text" value="{{$user->phone}}" name="phone" id="phone" />
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="country">{{translate('Country')}}</label>
                                    <select name="country_id" id="country">
                                        <option value="">{{translate('Select Country')}}</option>
                                        @foreach (get_countries() as $country )
                                        <option {{$user->country_id == $country->id ? "selected" :""}}
                                            value="{{$country->id}}">
                                            {{$country->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @foreach (['city','state','postal_code','address'] as $addressKey)
                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="{{$addressKey}}">
                                            {{translate(k2t($addressKey))}}
                                        </label>

                                        <input placeholder="{{translate('Enter ').$addressKey}}" type="text"
                                            value="{{@$user->address->$addressKey}}" name="address[{{$addressKey}}]"
                                            id="{{$addressKey}}">
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-12">
                                <div class="form-inner">
                                    <label for="image">{{translate("Image")}}</label>
                                    <div>
                                        <label for="image" class="feedback-file">
                                            <input hidden data-size="100x100" type="file" name="image" id="image"
                                                class="preview">
                                            <span><i class="bi bi-image"></i>
                                                {{translate("Select image")}}
                                            </span>
                                        </label>

                                        <div class="image-preview-section">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(site_settings('auto_subscription') == App\Enums\StatusEnum::true->status())
                                <div class="col-12">
                                    <div class="form-inner">
                                        <input id="auto_subscription" value="{{App\Enums\StatusEnum::true->status()}}"
                                            {{$user->auto_subscription ? "checked" :""}} class="form-check-input me-1"
                                            name="auto_subscription" type="checkbox">
                                        <label for="auto_subscription" class="form-check-label me-3">
                                            {{translate('Auto Subscription')}}
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <div class="col-lg-12">
                                <button class="i-btn btn--lg btn--primary capsuled" type="submit">
                                    {{translate('Update')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="tab-password-pane" role="tabpanel" aria-labelledby="tab-password"
                    tabindex="0">

                    <p class="pb-2">
                        {{translate('Enhance your account security by updating your password regularly. Enter your current password and choose a new one below.')}}
                    </p>
                    <form action="{{route('user.password.update')}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="current-password">
                                        {{translate("Current Password")}} <span class="text--danger">*</span>
                                    </label>
                                    <input placeholder="{{translate('current password')}}" type="password"
                                        name="current_password" id="current-password" />
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="new-password">
                                        {{translate("New Password")}} <span class="text--danger">*</span>
                                    </label>
                                    <input placeholder="{{translate('password')}}" name="password" type="password"
                                        id="new-password" />
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="confirm-password">
                                        {{translate("Confirm Password")}} <small class="text-danger">*</small>
                                    </label>
                                    <input placeholder="{{translate('Confirm password')}}" type="password"
                                        name="password_confirmation" id="confirm-password" />
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button class="i-btn btn--lg btn--primary capsuled" type="submit">
                                    {{translate('Update')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="tab-affiliate-pane" role="tabpanel" aria-labelledby="tab-affiliate"
                    tabindex="0">
                    <div class="row align-items-center gy-5">
                        <div class="col-lg-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="bg--linear-primary text-center mb-3">
                                        <div class="card-body p-3">
                                            <h3 class="fw-bold mt-1 mb-3 text-white fs-20">
                                                {{translate('Affiliate Setting')}}
                                            </h3>
                                            <p class="text-white opacity-75">
                                                {{translate('Configure your affiliate settings to optimize your referral program. ')}}
                                            </p>
                                        </div>
                                    </div>
                                    <ul class="subcription-list">

                                        <li>
                                            <span> {{translate('This Earning')}} </span>
                                            <span>
                                                {{num_format(number:$affiliateLogs->sum('commission_amount'),calC:true)}}
                                            </span>
                                        </li>
                                        <li>
                                            <span> {{translate('Total Referred')}} </span>
                                            <span>
                                                {{ $user->affilateUser->count()}}
                                            </span>
                                        </li>

                                    </ul>
                                    <form action="{{route('user.affiliate.update')}}" method="post"
                                        class="referral-form mt-5">
                                        @csrf
                                        <div class="form-inner">
                                            <label for="referral_code" class="form-label">
                                                {{ translate('Referral Code') }} <span class="text--danger">*</span>
                                            </label>

                                            <div class="input-with-btn">
                                                <input placeholder="{{translate('Referral Code')}}" name="referral_code"
                                                    value="{{$user->referral_code}}" type="text" id="referral_code">
                                                <button data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="{{translate('Generate Code')}}" type="button"
                                                    class="code-generate"
                                                    data-text="{{route('auth.register',['referral_code' => $user->referral_code])}}"><i
                                                        class="bi bi-arrow-repeat"></i></button>
                                            </div>
                                        </div>

                                        <div class="form-inner">
                                            <label for="ReferralURL" class="form-label">
                                                {{ translate('Referral URL') }}
                                            </label>

                                            <div class="input-with-btn">
                                                <input type="readonly"
                                                    value="{{route('auth.register',['referral_code' => $user->referral_code])}}" id="ReferralURL">
                                                <button data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="{{translate('Copy')}}" type="button"
                                                    class="copy-text"
                                                    data-text="{{route('auth.register',['referral_code' => $user->referral_code])}}"><i
                                                        class="bi bi-clipboard"></i></button>
                                            </div>

                                        </div>

                                        <button class="i-btn btn--lg btn--primary capsuled" type="submit">
                                            {{translate('Update')}}
                                            <span><i class="bi bi-arrow-up-right"></i></span>
                                        </button>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 ps-lg-5">
                            <div class="plan-upgrade">
                                <h4 class="mb-4 title">
                                    {{translate("How It Works")}}
                                </h4>
                                <div class="row g-3">
                                    <div class="col-xxl-4 col-lg-6">
                                        <div class="how-single">
                                            <div class="serail-no">{{translate('01')}}</div>
                                            <div class="icon">
                                                <i class="bi bi-envelope-paper"></i>
                                            </div>
                                            <div class="content">
                                                <h6>{{translate('Send Invitation')}}</h6>
                                                <p>
                                                    {{translate('Send your referral link to your friends and tell them how cool it is!')}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-lg-6">
                                        <div class="how-single">
                                            <div class="serail-no">
                                                {{translate('02')}}
                                            </div>
                                            <div class="icon">
                                                <i class="bi bi-envelope-paper"></i>
                                            </div>
                                            <div class="content">
                                                <h6>{{translate('Register')}}</h6>
                                                <p>
                                                    {{translate('Invite your friends to register using the referral link you have shared.')}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-lg-6">
                                        <div class="how-single">
                                            <div class="serail-no">
                                                {{translate('03')}}
                                            </div>
                                            <div class="icon">
                                                <i class="bi bi-envelope-paper"></i>
                                            </div>
                                            <div class="content">
                                                <h6>{{translate('Generate Commissions')}}</h6>
                                                <p>{{translate('Get commission for all the payments they make on their subscription plans.')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($package)
                    <div class="tab-pane fade" id="tab-subscription-pane" role="tabpanel" aria-labelledby="tab-subscription"
                        tabindex="0">
                        <div class="current-plan-card p-0">
                            <div class="row gy-4">
                                <div class="col-lg-8">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="bg--linear-primary text-center">
                                                <div class="card-body p-3">
                                                    <h6 class="text-white opacity-75 fw-normal fs-13">
                                                        {{translate('Current Plan')}}
                                                    </h6>
                                                    <h3 class="fw-bold mt-1 mb-3 text-white fs-22">
                                                        {{@$package->title}}
                                                    </h3>
                                                    <p class="text-white opacity-75">{{@$package->description}}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="current-info-single">
                                                <p>{{translate("Affiliate Commission")}}</p>
                                                <h5>
                                                    {{$package->affiliate_commission}}%
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="current-info-single">
                                                <p>{{translate("Expire date")}}</p>
                                                <h5>
                                                    {{@$subscription->expired_at ? get_date_time($subscription->expired_at): ucfirst(strtolower(App\Enums\PlanDuration::UNLIMITED->name))}}
                                                </h5>
                                            </div>
                                        </div>
                                        @foreach (plan_configuration(@$user->runningSubscription->package) as $configKey => $configVal)
                                            <div class="col-sm-6">
                                                <div class="current-info-single">
                                                    <p>{{ k2t($configKey) }}</p>
                                                    <h5>
                                                        @php
                                                          $value = $configVal;
                                                          if(is_bool($configVal)){
                                                            $value = $configVal
                                                                 ? "<i class='bi bi-check'></i>"
                                                                 : "<i class='bi bi-x'></i>";
                                                          }
                                                        @endphp
                                                        @php echo ($value) @endphp
                                                    </h5>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-lg-4 ps-lg-4">
                                    <div class="plan-upgrade">
                                        <h4 class="mb-4 title">
                                            <span>
                                                <img src="{{asset('assets/images/default/forward.png')}}" class="me-1"
                                                    alt="forward.png">
                                            </span>
                                            {{translate('Upgrade Your Plan')}}
                                        </h4>
                                        <div class="avatar-120 mb-3 mx-auto">
                                            <img src="{{asset('assets/images/default/upgrade.png')}}" alt="upgrade.png">
                                        </div>
                                        <p class="mb-4">
                                            {{translate('Updating your plan is a crucial step in ensuring that your goals and strategies
                                            remain relevant and effective in a dynamic environment. As circumstances change, whether due to
                                            shifts in the market, new technological advancements, or evolving personal or organizational
                                            priorities')}}
                                        </p>
                                        <a href="{{route('user.plan')}}"
                                            class="i-btn btn--primary btn--lg capsuled mx-auto">
                                            {{translate('Update Plan')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="tab-pane fade" id="tab-webhook-pane" role="tabpanel" aria-labelledby="tab-webhook"
                    tabindex="0">
                    <p class="pb-2">
                        {{translate('Set up and manage your webhooks to integrate with external services. Configure your webhook URLs and settings below.')}}
                    </p>
                    <form action="{{route('user.webhook.update')}}" method="post">
                        @csrf
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="webhook_api_key" class="form-label">
                                        {{ translate('API Key') }} <span class="text--danger">*</span>
                                    </label>

                                    <div class="input-with-btn">
                                        <input placeholder="{{translate('Webhook API key')}}" id="webhook_api_key"
                                            value="{{$user->webhook_api_key}}" name="webhook_api_key" type="text">
                                        <button data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="{{translate('Generate API key')}}" type="button"
                                            class="key-generate"><i class="bi bi-arrow-repeat"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="webhookURL" class="form-label">
                                        {{ translate('Webhook URL') }}
                                    </label>

                                    <div class="input-with-btn">
                                        <input readonly id="webhookURL"
                                            value="{{route('webhook',['uid' => $user->uid])}}" type="text">
                                        <button data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="{{translate('Copy')}}" type="button" class="copy-text"
                                            data-text="{{route('webhook',['uid' => $user->uid])}}"><i
                                                class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <button class="i-btn btn--lg btn--primary capsuled" type="submit">
                                    {{translate('Update')}}                           
                                </button>
                            </div>
                        </div>
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
(function($) {

    "use strict";
    $(".select2").select2({
        placeholder: "{{translate('Select Item')}}",
    })

    $("#country").select2({
        placeholder: "{{translate('Select Country')}}",
    })
})(jQuery);
</script>
@endpush
