@php use Illuminate\Support\Arr; @endphp
@extends('layouts.master')
@push('style-include')
    <link nonce="{{ csp_nonce() }}" href="{{asset('assets/global/css/datepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')

@php

        $user             = auth_user('web')->load(['runningSubscription','runningSubscription.package']);
        $subscription     = $user->runningSubscription;
        $remainingToken   = $subscription ? $subscription->remaining_word_balance : 0;
        $remainingProfile = $subscription ? $subscription->total_profile : 0;
        $remainingPost    = $subscription ? $subscription->remaining_post_balance : 0;
        $accessPlatforms         = (array) ($subscription ? @$subscription->package->social_access->platform_access : []);
        $platforms = get_platform()
                        ->whereIn('id', $accessPlatforms )
                        ->where("status",App\Enums\StatusEnum::true->status())
                        ->where("is_integrated",App\Enums\StatusEnum::true->status());

        $subscriptionDetails = collect([
            'remaining_word'    => $remainingToken,
            'remaining_profile' => $remainingProfile,
            'remaining_post'    => $remainingPost,
            'total_patforms'   => count($accessPlatforms)])->mapWithKeys(fn($value,$key) :array =>  [k2t($key) => $value])->toArray();
        if( $remainingToken == App\Enums\PlanDuration::value('UNLIMITED')) unset($subscriptionDetails['remaining_word']);
        if( $remainingPost == App\Enums\PlanDuration::value('UNLIMITED')) unset($subscriptionDetails['remaining_profile']);
@endphp



<div id="overlay" class="overlay"></div>
<button id="right-sidebar-btn" class="right-sidebar-btn fs-20">
    <i class="bi bi-activity"></i>
</button>

<div class="row g-4 mb-4">
    <div class="col">
        <div class="row g-4">
            <div class="col-xl-6">
                <div class="i-card h-550">
                    <h4 class="card--title mb-4">
                         {{translate('Connected Social Accounts')}}
                    </h4>
                    <div class="row g-3">
                       @forelse(Arr::get($data['account_report'] ,'accounts_by_platform',[]) as $platform)
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="i-card no-border p-0 border position-relative bg--light">
                                    <div class="shape-one">
                                        <svg width="65" height="65" viewBox="0 0 65 65" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M52.3006 64.8958L64.4805 64.9922L64.9908 0.510364L0.508992 1.7845e-05L0.412593 12.1799L35.5193 12.4578C45.016 12.533 52.6536 20.2924 52.5784 29.789L52.3006 64.8958Z"
                                                fill="white" />
                                        </svg>
                                    </div>
                                    <div class="shape-two">
                                        <svg width="65" height="65" viewBox="0 0 65 65" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M52.3006 64.8958L64.4805 64.9922L64.9908 0.510364L0.508992 1.7845e-05L0.412593 12.1799L35.5193 12.4578C45.016 12.533 52.6536 20.2924 52.5784 29.789L52.3006 64.8958Z"
                                                fill="white" />
                                        </svg>
                                    </div>
                                    <span class="icon-image position-absolute top-0 end-0">
                                        <img src="{{imageUrl(@$platform->file,'platform',true)}}"
                                            alt="{{@$platform->name.'.jpg'}}" />
                                    </span>
                                    <div class="p-3">
                                        <h5 class="card--title-sm">
                                            {{$platform->name}}
                                        </h5>
                                    </div>
                                    <div class="p-3 border-top">
                                        <p class="card--title-sm mb-1">
                                            {{$platform->accounts_count}}
                                        </p>
                                        <p class="mb-3 fs-14">
                                              {{translate('Total Posts')}}
                                        </p>
                                        <a href="{{route('user.social.account.create',['platform' => $platform->slug])}}" class="i-btn btn--sm btn--outline capsuled">
                                             <i class="bi bi-plus-lg"></i>
                                              {{translate('Create Account')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty

                             <div class="col-12">
                                  @include('admin.partials.not_found')
                             </div>

                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="i-card h-100">
                    <ul class="social-account-list-2 mb-2">

                        <li>
                            <a href="{{route('user.home')}}" class="{{!request()->input('platform') ? 'active' :''}}">
                                 {{translate('ALL')}}
                            </a>
                        </li>

                        @forelse ($platforms as $platform )
                            <li>
                                <a class="{{$platform->slug == request()->input('platform') ? 'active' :''}}" href="{{route('user.home',['platform' => $platform->slug])}}">
                                    {{$platform->name}}
                                </a>
                            </li>
                         @empty

                         @endforelse
                    </ul>

                    <div id="postReport"></div>

                </div>
            </div>

            <div class="col-12">
                <div class="i-card h-100">
                    <div class="row align-items-center g-2 mb-4">
                        <div class="col-md-9">
                            <h4 class="card--title">
                               {{translate('Overview')}}
                            </h4>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-sm-6">
                            <div class="i-card border p-0">
                                <div class="p-3">
                                    <div class="icon text--primary mb-30">
                                        <i class="bi bi-graph-up fs-30"></i>
                                    </div>
                                    <div class="content">
                                        <p class="card--title-sm mb-1">
                                            {{translate("Total Post")}}
                                        </p>
                                        <h6>
                                            {{Arr::get($data,'total_post',0)}}
                                        </h6>
                                    </div>
                                </div>
                                <div class="footer border-top d-flex justify-content-between">

                                     <a class="text--success" href="{{route('user.social.post.list')}}">
                                          {{translate('View All')}}
                                     </a>

                                    <p class="mb-0 fs-14"> {{translate('This year')}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-sm-6">
                            <div class="i-card border p-0">
                                <div class="p-3">
                                    <div class="icon text--primary mb-30">
                                        <i class="bi bi-calendar-event fs-30"></i>
                                    </div>

                                    <div class="content">
                                        <p class="card--title-sm mb-1">{{translate("Pending Post")}}</p>
                                        <h6>{{Arr::get($data,'pending_post',0)}}</h6>
                                    </div>
                                </div>
                                <div class="footer border-top d-flex justify-content-between">
                                    <a class="text--success" href="{{route('user.social.post.list',['status' =>App\Enums\PostStatus::PENDING->value ])}}">
                                        {{translate('View All')}}
                                   </a>
                                    <p class="mb-0 fs-14">{{translate('This year')}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-sm-6">
                            <div class="i-card border p-0">
                                <div class="p-3">
                                    <div class="icon text--primary mb-30">
                                        <i class="bi bi-clock fs-30"></i>
                                    </div>
                                    <div class="content">
                                        <p class="card--title-sm mb-1">{{translate("Schedule Post")}}</p>
                                        <h6>{{Arr::get($data,'schedule_post',0)}}</h6>
                                    </div>
                                </div>
                                <div class="footer border-top d-flex justify-content-between flex-wrap">
                                    <a class="text--success" href="{{route('user.social.post.list',['status' =>App\Enums\PostStatus::SCHEDULE->value ])}}">
                                        {{translate('View All')}}
                                   </a>
                                    <p class="mb-0 fs-14">{{translate('This year')}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-sm-6">
                            <div class="i-card border p-0">
                                <div class="p-3">
                                    <div class="icon text--primary mb-30">
                                        <i class="bi bi-check-circle fs-30"></i>
                                    </div>
                                    <div class="content">
                                        <p class="card--title-sm mb-1">{{translate("Success Post")}}</p>
                                        <h6>{{Arr::get($data,'success_post',0)}}</h6>
                                    </div>
                                </div>
                                <div class="footer border-top d-flex justify-content-between flex-wrap">
                                    <a class="text--success" href="{{route('user.social.post.list',['status' =>App\Enums\PostStatus::SUCCESS->value ])}}">
                                        {{translate('View All')}}
                                    </a>
                                    <p class="mb-0 fs-14"> {{translate('This year')}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-sm-6">
                            <div class="i-card border p-0">
                                <div class="p-3">
                                    <div class="icon text--primary mb-30">
                                        <i class="bi bi-x-circle fs-30"></i>
                                    </div>
                                    <div class="content">
                                        <p class="card--title-sm mb-1">{{translate("Failed Post")}}</p>
                                        <h6>{{Arr::get($data,'failed_post',0)}}</h6>
                                    </div>
                                </div>
                                <div class="footer border-top d-flex justify-content-between flex-wrap">
                                    <a class="text--success" href="{{route('user.social.post.list',['status' =>App\Enums\PostStatus::FAILED->value ])}}">
                                        {{translate('View All')}}
                                   </a>
                                    <p class="mb-0 fs-14"> {{translate('This year')}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-sm-6">
                            <div class="i-card border p-0">
                                <div class="p-3">
                                    <div class="icon text--primary mb-30">
                                        <i class="bi bi-person-circle fs-30"></i>
                                    </div>
                                    <div class="content">
                                        <p class="card--title-sm mb-1">{{translate("Total Account")}}</p>
                                        <h6>{{Arr::get($data['account_report'],'total_account',0)}}</h6>
                                    </div>
                                </div>
                                <div class="footer border-top d-flex justify-content-between flex-wrap">
                                    <a class="text--success" href="{{route('user.social.account.list')}}">
                                        {{translate('View All')}}
                                   </a>
                                    <p class="mb-0 fs-14"> {{translate('This year')}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-sm-6">
                            <div class="i-card border p-0">
                                <div class="p-3">
                                    <div class="icon text--primary mb-30">
                                        <i class="bi bi-person-check fs-30"></i>
                                    </div>
                                    <div class="content">
                                        <p class="card--title-sm mb-1">{{translate("Active Account")}}</p>
                                        <h6>{{Arr::get($data['account_report'],'active_account',0)}}</h6>
                                    </div>
                                </div>
                                <div class="footer border-top d-flex justify-content-between flex-wrap">
                                    <a class="text--success" href="{{route('user.social.account.list',['status' =>  App\Enums\StatusEnum::true->status()])}}">
                                        {{translate('View All')}}
                                   </a>
                                    <p class="mb-0 fs-14"> {{translate('This year')}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-sm-6">
                            <div class="i-card border p-0">
                                <div class="p-3">
                                    <div class="icon text--primary mb-30">
                                        <i class="bi bi-person-x fs-30"></i>
                                    </div>
                                    <div class="content">
                                        <p class="card--title-sm mb-1">{{translate("Inactive Account")}}</p>
                                        <h6>{{Arr::get($data['account_report'],'inactive_account',0)}}</h6>
                                    </div>
                                </div>
                                <div class="footer border-top d-flex justify-content-between flex-wrap">
                                    <a class="text--success" href="{{route('user.social.account.list',['status' =>  App\Enums\StatusEnum::false->status()])}}">
                                        {{translate('View All')}}
                                   </a>
                                    <p class="mb-0 fs-14">
                                         {{translate('This year')}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="i-card-md card-height-100">
                    <div class="card-header">
                        <h4 class="card--title">
                            {{translate("Latest Transaction Log")}}
                        </h4>
                    </div>

                    <div class="card-body px-0">
                        <div class="table-accordion">
                            @php
                            $reports = Arr::get($data,'latest_transactiions',null);
                            @endphp
                            @if($reports && $reports->count() > 0)
                            <div class="accordion" id="wordReports">
                                @forelse(Arr::get($data,'latest_transactiions',[]) as $report)
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <div class="accordion-button collapsed" role="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{$report->id}}" aria-expanded="false"
                                            aria-controls="collapse{{$report->id}}">
                                            <div class="row align-items-center w-100 gy-4 gx-sm-3 gx-0">
                                                <div class="col-lg-3 col-sm-4 col-12">
                                                    <div class="table-accordion-header transfer-by">
                                                        <span class="icon-btn icon-btn-sm primary circle">
                                                            <i class="bi bi-file-text"></i>
                                                        </span>
                                                        <div>
                                                            <h6>
                                                                {{translate("Trx Code")}}
                                                            </h6>
                                                            <p> {{$report->trx_code}}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-sm-4 col-6 text-lg-center text-sm-center text-start">
                                                    <div class="table-accordion-header">
                                                        <h6>
                                                            {{translate("Date")}}
                                                        </h6>
                                                        <p>
                                                            {{ get_date_time($report->created_at) }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-sm-end text-end">
                                                    <div class="table-accordion-header">
                                                        <h6>
                                                            {{translate("Balance")}}
                                                        </h6>

                                                        <p
                                                            class='text--{{$report->trx_type == App\Models\Transaction::$PLUS ? "success" :"danger" }}'>
                                                            <i class='bi bi-{{$report->trx_type == App\Models\Transaction::$PLUS ? "plus" :"dash" }}'></i>
                                                            {{num_format($report->amount,$report->currency)}}
                                                        </p>

                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-start">
                                                    <div class="table-accordion-header">
                                                        <h6>{{translate("Post Balance")}}</h6>
                                                        <p>
                                                            {{@num_format(
                                                                number : $report->post_balance??0,
                                                                calC   : true
                                                            )}}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-lg-end text-md-center text-end">
                                                    <div class="table-accordion-header">
                                                        <h6>{{translate("Remark")}}</h6>
                                                        <p>
                                                            {{k2t($report->remarks)}}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="collapse{{$report->id}}" class="accordion-collapse collapse"
                                        data-bs-parent="#wordReports">
                                        <div class="accordion-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <h6 class="title">
                                                        {{translate("Report Information")}}
                                                    </h6>
                                                    <p class="value">
                                                        {{$report->details}}
                                                    </p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                @endforelse
                            </div>
                            @else
                                @include('admin.partials.not_found',['custom_message' => "No Reports found!!"])
                            @endif

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="i-card-md card-height-100">
                    <div class="card-header">
                        <h4 class="card--title">
                            {{translate("Latest Subscription Log")}}
                        </h4>

                    </div>

                    <div class="card-body px-0">
                        <div class="table-accordion">
                            @php
                            $reports = Arr::get($data,'subscription_log',null);
                            @endphp

                            @if($reports && $reports->count() > 0)
                            <div class="accordion" id="wordReports-2">
                                @forelse($reports as $report)
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <div class="accordion-button collapsed" role="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{$report->id}}" aria-expanded="false"
                                            aria-controls="collapse{{$report->id}}">
                                            <div class="row align-items-center w-100 gy-4 gx-sm-3 gx-0">
                                                <div class="col-lg-2 col-sm-4 col-12">
                                                    <div class="table-accordion-header transfer-by">
                                                        <span class="icon-btn icon-btn-sm primary circle">
                                                            <i class="bi bi-file-text"></i>
                                                        </span>
                                                        <div>
                                                            <h6>
                                                                {{translate("TRX Code")}}
                                                            </h6>
                                                            <p> {{$report->trx_code}}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-sm-center text-start">
                                                    <div class="table-accordion-header">
                                                        <h6>
                                                            {{translate("Expired In")}}
                                                        </h6>
                                                        <p>
                                                            @if($report->expired_at)
                                                            {{ get_date_time($report->expired_at,'d M, Y') }}
                                                            @else
                                                            --
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-sm-end text-end">
                                                    <div class="table-accordion-header">
                                                        <h6>
                                                            {{translate("Package")}}
                                                        </h6>
                                                        <p>
                                                            {{@$report->package->title}}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-lg-center text-start">
                                                    <div class="table-accordion-header">
                                                        <h6>
                                                            {{translate("Status")}}
                                                        </h6>
                                                        @php echo (subscription_status($report->status)) @endphp
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-sm-center text-end">
                                                    <div class="table-accordion-header">
                                                        <h6>{{translate("Payment Amount")}}</h6>
                                                        <p>
                                                            {{@num_format(
                                                        number : $report->payment_amount??0,
                                                        calC   : true
                                                    )}}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-sm-4 col-6 text-sm-end text-start">
                                                    <div class="table-accordion-header">
                                                        <h6>{{translate("Date")}}</h6>
                                                        <p>
                                                            @if($report->created_at)
                                                            {{ get_date_time($report->created_at) }}
                                                            @else
                                                            --
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="collapse{{$report->id}}" class="accordion-collapse collapse"
                                        data-bs-parent="#wordReports-2">
                                        <div class="accordion-body">
                                            <ul class="list-group list-group-flush">
                                                @php
                                                $informations = [
                                                    "AI_word_balance"          => $report->word_balance,
                                                    "remaining_word_balance"   => $report->remaining_word_balance,
                                                    "carried_word_balance"     => $report->carried_word_balance,
                                                    "total_social_profile"     => $report->total_profile,
                                                    "carried_profile_balance"  => $report->carried_profile,
                                                    "social_post_balance"      => $report->post_balance,
                                                    "remaining_post_balance"   => $report->remaining_post_balance,
                                                    "carried_post_balance"     => $report->carried_post_balance,
                                                ];
                                                @endphp

                                                @foreach ($informations as $key => $val)
                                                    <li class="list-group-item">
                                                        <h6 class="title">
                                                            {{k2t($key)}}
                                                        </h6>
                                                        <p class="value">
                                                            {{$val == App\Enums\PlanDuration::UNLIMITED->value ? App\Enums\PlanDuration::UNLIMITED->name : $val }}
                                                        </p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                @endforelse
                            </div>
                            @else
                            @include('admin.partials.not_found',['custom_message' => "No Reports found!!"])
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-auto right-side-col" >
        <div class="i-card mb-4 sidebar-post">
            <h4 class="card--title mb-20">{{translate('Latest Post')}}</h4>
            @php
                $latestPost = Arr::get($data,'latest_post',collect([]));
            @endphp

            @if( $latestPost->count() > 0)

                <div class="swiper latest-post-slider">

                    <div class="swiper-wrapper">
                        @foreach ($latestPost as $post )
                            <div class="swiper-slide">
                                <div>

                                    @if($post->file->count() > 0)
                                        <div class="latest-post-banner mb-3">
                                        
                                                @php
                                                    $fileURL = $post->file->count() > 0
                                                                    ? imageURL($post->file->first(),"post",true)
                                                                    : get_default_img();
                                                @endphp

                                                @if(!isValidVideoUrl($fileURL))
                                                    <img src="{{ $fileURL}}" class="radius-8 mb-3" alt="post.jpg">
                                                @else
                                                    <video  width="150" controls>
                                                        <source src="{{$fileURL}}">
                                                    </video>
                                                @endif
                                        
                                        </div>
                                    @endif

                                    @if($post->content)
                                        <h6 class="latest-post-title mb-1">
                                            {{$post->content}}
                                        </h6>
                                    @endif
                                    @if($post->link)
                                        <a target="_blank" href="{{$post->link}}">
                                            {{translate('Link')}}
                                        </a>
                                    @endif
                                    <div class="d-flex mb-1">
                                        @if(@$post->account->account_information->link)
                                            <a  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Account name')}}"  target="_blank" href="{{@$post->account->account_information->link}}">
                                                #{{ @$post->account->account_information->name}}
                                            </a>
                                        @else
                                            {{ @$post->account->account_information->name}}
                                        @endif

                                        @if(@$post->account->platform)
                                        <a href="{{route('user.social.account.list',['platform' => $platform->slug])}}"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Platform')}}">
                                                #{{@$post->account->platform->name}}
                                        </a>
                                        @endif
                                    </div>
                                    <div class="date mb-3">
                                        <span class="fs-14 text--light">{{get_date_time($post->created_at,"F j, Y")}}</span> <span class="fs-12 text--light">{{get_date_time($post->created_at,"g a")}}</span>
                                    </div>
                                    <a href="{{route('user.social.post.show',['uid' => $post->uid])}}" class="i-btn btn--primary btn--lg capsuled w-100">
                                        {{translate('View Post')}}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="latest-post-pagination"></div>
            @else
                <div>
                    @include('admin.partials.not_found')
                </div>
            @endif
        </div>

        <div class="i-card upgrade-card mb-4">
            @if($subscription &&   $subscription->package)
                <h4 class="card--title text-white">
                     {{$subscription->package->title}}
                </h4>
                <p>
                    {{$subscription->package->description}}
                </p>
            @endif
            <a href="{{route('user.plan')}}" class="i-btn btn--md btn--white capsuled mx-auto">
                @if($subscription)
                    {{translate('Upgrade Now')}}
                @else
                   {{translate('Subscribe Now')}}
                @endif
            </a>
        </div>

        <div class="i-card mb-4">
            <div class="card-header mb-20">
                <h4 class="card--title">
                    {{ translate("Latest Activity ") }}
                </h4>
            </div>

            @php
                $activities =  Arr::get($data,'latest_activities',collect([]));
            @endphp

            <div class="card-body">
                <ul class="share-card" data-simplebar>
                    @forelse ($activities as $activitiy)
                        <li class="mb-3 fs-15"><span class="me-1 text--primary"><i class="bi bi-card-text"></i></span>
                            {{ $activitiy->details}}
                        </li>
                    @empty
                        <li class="mb-3 fs-15">
                            {{ translate('No activities found!!') }}
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="i-card">
            <div class="card-header mb-20">
                <h4 class="card--title ">
                    {{translate("Subscription Specification")}}
                </h4>
            </div>

            <div class="card-body">
                <div id="subscriptionChart" class="subscription-chart">
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('script-include')
    <script src="{{asset('assets/global/js/apexcharts.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/moment.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/daterangepicker.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/datepicker/init.js')}}"></script>
@endpush

@push('script-push')

<script nonce="{{ csp_nonce() }}">
  "use strict";
    var subscriptionValues = @json(array_values($subscriptionDetails));
    var subscriptionLabel = @json(array_keys($subscriptionDetails));

    var options = {
        series: subscriptionValues,
        chart: {
            type: "donut",
            width: "100%",
            nonce:"{{ csp_nonce() }}",
        },
        colors: [
            "var(--color-primary)",
            "var(--color-secondary)",
            "var(--color-warning)",
            "var(--color-info)",
            "var(--color-danger)"
        ],
        labels: subscriptionLabel,
        plotOptions: {
            pie: {
                startAngle: -90,
                endAngle: 270
            }
        },
        dataLabels: {
            enabled: false
        },

        legend: {
             fontSize: '12px',
             position: 'bottom'
        },
    };

    var chart = new ApexCharts(document.querySelector("#subscriptionChart"), options);
    chart.render();

    var monthlyLabel = @json(array_keys($data['monthly_post_graph']));
    var accountValues = [];
    var totalPost     = @json(array_values($data['monthly_post_graph']));
    var pendigPost    = @json(array_values($data['monthly_pending_post']));
    var schedulePost  = @json(array_values($data['monthly_schedule_post']));
    var successPost   = @json(array_values($data['monthly_success_post']));
    var failedPost    = @json(array_values($data['monthly_failed_post']));

    var monthlyLabel = @json(array_keys($data['monthly_post_graph']));

    var options = {
        chart: {
            height: 410,
            type: "line",
            nonce:"{{ csp_nonce() }}",
            toolbar: {
                       show: false
                    }
        },
        dataLabels: {
            enabled: false,
        },
        colors: [
            "var(--color-primary)",
            "var(--color-secondary)",
            "var(--color-warning)",
            "var(--color-info)",
            "var(--color-danger)"
        ],
        series: [{
                name: "{{ translate('Total Post') }}",
                data: totalPost,
            },
            {
                name: "{{ translate('Pending Post') }}",
                data: pendigPost,
            },
            {
                name: "{{ translate('Success Post') }}",
                data: successPost,
            },
            {
                name: "{{ translate('Schedule Post') }}",
                data: schedulePost,
            },
            {
                name: "{{ translate('Failed Post') }}",
                data: failedPost,
            },
        ],
        xaxis: {
            categories: monthlyLabel,
        },

        tooltip: {
            shared: false,
            intersect: true,
            y: {
                formatter: function(value, {
                    series,
                    seriesIndex,
                    dataPointIndex,
                    w
                }) {
                    return parseInt(value);
                }
            }

        },
        markers: {
            size: 6,
        },
        stroke: {
            width: [4, 4],
        },
        legend: {
            horizontalAlign: "center",
            offsetY: 5,
        },
    };

    var chart = new ApexCharts(document.querySelector("#postReport"), options);
    chart.render();

    var swiper = new Swiper(".latest-post-slider", {
        pagination: {
            el: ".latest-post-pagination",
            clickable: true,
        },
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
    });

    $(".select2").select2({

    });
</script>
@endpush
