@extends('layouts.master')
@section('content')
        @php
            $user = auth_user('web')->load(['runningSubscription','runningSubscription.package']);
            $subscription = $user->runningSubscription;
            $currentPlan = $subscription && $subscription->package ? $subscription->package: null;
        @endphp

        <div class="i-card-md">
            <div class="card-body">
                <div class="mb-5 d-flex justify-content-center">
                    <div class="nav plan-tab" role="tablist">
                        @foreach (App\Enums\PlanDuration::toArray() as  $key => $value)
                            <button class="nav-link {{$loop->index == 0 ? 'active' :''}}" id="{{$key}}-tab" data-bs-toggle="pill" data-bs-target="#{{$key}}"
                                type="button" role="tab" aria-controls="{{$key}}" aria-selected="true">
                                {{$key}}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="tab-content" id="tab-plans">
                    @foreach (App\Enums\PlanDuration::toArray() as  $key => $value)
                        @php
                            $purchasePlans = $plans->where('duration',$value);
                        @endphp

                        <div class="tab-pane fade  {{$loop->index == 0 ? 'show active' :''}}" id="{{$key}}" role="tabpanel" aria-labelledby="{{$key}}-tab"
                            tabindex="0">
                            <div class="row gy-4 gx-3">
                                @forelse ($purchasePlans as  $plan)
                                    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6">
                                        <div class="plan-detail-card">
                                            <div class="icon">
                                                <i class="{{$plan->icon}}"></i>
                                            </div>
                                                @if($plan->is_recommended ==  App\Enums\StatusEnum::true->status())
                                                <div class="recomend">
                                                    <span>{{translate('Recommended')}}</span>
                                                </div>
                                            @endif
                                            <div class="plan-detail-top">
                                                <span>{{ $plan->title}}</span>
                                                <p>{{ $plan->description}}</p>
                                                <div class="price">
                                                    <h4>
                                                        @if($plan->discount_price > 0)
                                                            <del>
                                                                {{num_format( number : $plan->price,
                                                                    calC:true)}}
                                                            </del>
                                                            {{num_format( number : $plan->discount_price,
                                                                calC:true)}}
                                                        @else
                                                            {{num_format( number : $plan->price,
                                                                calC:true)}}
                                                        @endif
                                                        <span>/{{$key}}</span>
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="plan-detail-body">
                                                <h5 class="mb-4">{{translate("What’s included")}}</h5>
                                                <ul>
                                                    @foreach (plan_configuration( $plan) as $configKey => $configVal )
                                                        <li>
                                                            <span>
                                                                @if(is_bool($configVal) &&  !$configVal)
                                                                  <i class="bi bi-x-circle-fill text-danger"></i>
                                                                @else
                                                                   <i class="bi bi-check-circle-fill"></i>
                                                                @endif
                                                            </span>
                                                            {{!is_bool($configVal) ? $configVal : "" }} {{k2t($configKey)}}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <a href="javascript:void(0)" @if(@$currentPlan->id == $plan->id) data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Current running plan')}}" data-plan={{$plan}}  @endif  data-href="{{route("user.plan.purchase",$plan->slug)}}"
                                                class="i-btn btn--primary btn--lg capsuled text-uppercase mx-auto w-100 subscribe-plan">
                                                {{
                                                    @$currentPlan->id == $plan->id ?  translate("Running") : translate("Subscribe")
                                                }}
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        @include("frontend.partials.not_found")
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
@endsection

@section('modal')
    @include('modal.plan_subscribe')
@endsection
