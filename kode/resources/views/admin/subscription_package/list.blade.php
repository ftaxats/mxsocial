@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        @php
           $earings =  0;
        @endphp
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action="{{route('admin.subscription.package.bulk')}}" method="post">
                        @csrf
                        <input type="hidden" name="bulk_id" id="bulkid">
                        <input type="hidden" name="value" id="value">
                        <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('create_package') || check_permission('update_package') )
                        <div class="col-md-6 d-flex justify-content-start gap-2">
                            @if(check_permission('update_package'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(check_permission('update_package'))
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            @if(check_permission('create_package'))
                                <div class="action">
                                    <a href="{{route('admin.subscription.package.create')}}"  class="i-btn btn--sm success">
                                        <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                    </a>
                                </div>
                            @endif

                            @if(check_permission('update_package'))
                                <button type="button" data-bs-toggle="modal" data-bs-target="#subscription-config" class="i-btn btn--sm danger create">
                                    <i class="las la-cogs me-1"></i>  {{translate('Configuration')}}
                                </button>
                            @endif
                        </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                    <input name="search" value="{{request()->search}}" type="search" placeholder="{{translate('Search by title')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route(Route::currentRouteName())}}"  class="i-btn btn--sm danger">
                                    <i class="las la-sync"></i>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-container position-relative">
                @include('admin.partials.loader')
                <table >
                    <thead>
                        <tr> 
                            <th scope="col">
                                @if(check_permission('create_package') || check_permission('update_package') || check_permission('delete_package'))
                                    <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                @endif
                                &nbsp;
                                {{translate('Title')}}
                            </th>
                            <th scope="col">
                                {{translate('Price')}}
                            </th>
                            <th scope="col">
                                {{translate('Duration')}}
                            </th>
                            <th scope="col">
                                {{translate('Affiliate Commission')}}
                            </th>
                            <th scope="col">
                                {{translate('Subscriptions - Earnings')}}
                            </th>
                            <th scope="col">
                                {{translate('Status')}}
                            </th>
                            <th scope="col">
                                {{translate('Feature')}}
                            </th>
                            <th scope="col">
                                {{translate('Recommended')}}
                            </th>
                            <th scope="col">
                                {{translate('Options')}}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr> 
                                <td data-label="{{translate('Title')}}">
                                    @if(check_permission('update_package'))
                                        <input type="checkbox" value="{{$package->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$package->id}}"/>
                                    @endif
                                    &nbsp;
                                    {{$package->title}}
                                    @if($package->is_free ==  App\Enums\StatusEnum::true->status())
                                        <span class="i-badge capsuled success">
                                            <i class="las la-star"></i>  {{translate('Free')}}
                                        </span>
                                    @endif
                                </td>
                                <td data-label="{{translate('Price')}}">
                                    @if($package->discount_price > 0)
                                        <del>
                                            {{num_format($package->price,base_currency())}}
                                        </del>/
                                        {{num_format($package->discount_price,base_currency())}}  
                                    @else
                                        {{num_format($package->price,base_currency())}}
                                    @endif
                                </td>
                                <td data-label="{{translate('Duration')}}">
                                     @php  echo (plan_duration($package->duration))  @endphp
                                </td>
                                <td data-label="{{translate('Affiliate Commission')}}">
                                      {{($package->affiliate_commission)}}%  
                                </td>
                                <td data-label="{{translate('Subscriptions - Earnings')}}">
                                    <span>
                                        <a class="i-badge capsuled success" href="{{route('admin.subscription.report.list',['package' => $package->slug])}}">
                                          {{translate("Total Subscription")}} {{$package->subscriptions_count}}
                                        </a>
                                    </span>
                                      -
                                      {{@num_format(
                                        number : $package->total_subscription_income??0,
                                        calC   : true
                                      )}}
                                </td>
                                <td data-label='{{translate("Status")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_package') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.subscription.package.update.status') }}"
                                            data-status="{{ $package->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$package->uid}}" {{$package->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-status-{{$package->id}}" >
                                        <label class="form-check-label" for="status-switch-status-{{$package->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Feature")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_package') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="is_feature"
                                            data-route="{{ route('admin.subscription.package.update.status') }}"
                                            data-status="{{ $package->is_feature == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$package->uid}}" {{$package->is_feature ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-feature-{{$package->id}}">
                                        <label class="form-check-label" for="status-switch-feature-{{$package->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Recommended")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_package') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="is_recommended"
                                            data-route="{{ route('admin.subscription.package.update.status') }}"
                                            data-status="{{ $package->is_recommended == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$package->uid}}" {{$package->is_recommended ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-recommended-{{$package->id}}" >
                                        <label class="form-check-label" for="status-switch-recommended-{{$package->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Options")}}'>
                                    <div class="table-action">
                                        @if(check_permission('update_package') || check_permission('delete_package') )
                                            @if(check_permission('update_package') )
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Edit')}}" href="{{route('admin.subscription.package.edit',$package->uid)}}"  class="update icon-btn warning"><i class="las la-pen"></i></a>
                                            @endif
                                            @if(check_permission('delete_package') && $package->is_free !=  App\Enums\StatusEnum::true->status())
                                              
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}"  href="javascript:void(0);"    data-href="{{route('admin.subscription.package.destroy',$package->id)}}" class="pointer delete-item icon-btn danger">
                                                     <i class="las la-trash-alt"></i>
                                                </a>
                                            @endif
                                        @else
                                            --
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @php
                               $earings += $package->total_subscription_income;
                            @endphp
                              
                            @empty
                            <tr>
                                <td class="border-bottom-0" colspan="10">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-center fw-semibold">
                @if( 0 < $earings)
                 {{translate("Total Earnings")}} <span class="i-badge-solid primary capsuled ms-2"> {{@num_format(
                    number : $earings??0,
                    calC   : true
                  )}} </span>
                @endif
             </div>
        </div>
    </div>
@endsection
@section('modal')

@include('modal.delete_modal')

<div class="modal fade" id="subscription-config" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="subscription-config"   aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{translate('Set Configuration')}}
                </h5>
                <button class="close-btn" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{route('admin.subscription.package.configuration')}}" id="configForm" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                              <div class="form-inner d-flex gap-3">
                                <input {{ site_settings('subscription_carry_forword') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }} 
                                data-key='subscription_carry_forword'   data-status='{{ site_settings('subscription_carry_forword') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                                data-route="{{ route('admin.setting.update.status') }}" class="form-check-input status-update" id="subscription_carry_forword" type="checkbox">
                                <label for="subscription_carry_forword" class="form-check-label" >
                                    <b class="text--primary"> {{translate("Balance Carry Forward")}} : </b>
                                    {{translate("Remaining balance from active package(only for active) will be added to next package balance. This service is applicable for same package !!")}}
                                </label>
                              </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-inner d-flex gap-3">
                              <input {{ site_settings('auto_subscription') == App\Enums\StatusEnum::true->status() ? 'checked' : '' }} 
                              data-key='auto_subscription'   data-status='{{ site_settings('auto_subscription') == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status() }}'
                              data-route="{{ route('admin.setting.update.status') }}" class="form-check-input status-update" id="auto_subscription" type="checkbox">
                              <label for="auto_subscription" class="form-check-label" >
                                  <b class="text--primary"> {{translate("Allow user to configure Auto Subscription")}} : </b>
                                  {{translate("if enable, user can configure auto subscription settings!!")}}
                              </label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="auto_subscription_package"> 
                                {{translate('Auto Subscription Package')}} <small class="text-danger">*</small>
                            </label>
                            <select id="auto_subscription_package" required name="site_settings[auto_subscription_package]" class="modal-select2" >
                                <option value="">
                                    {{translate("Select Package")}}
                                </option>
                                @foreach( $packages as $package)
                                    @if($package->is_free !=  App\Enums\StatusEnum::true->status())
                                        <option {{ site_settings("auto_subscription_package") == $package->id ? 'selected' :""}}  value="{{$package->id}}">
                                            {{$package->title}}
                                        </option>
                                    @endif
                                @endforeach 
                            </select>
                       </div>
                       <div class="col-lg-6">
                            <label for="signup_bonus"> 
                                {{translate('Sign up package')}} <small class="text-danger">*</small>
                            </label>
                            <select id="signup_bonus" required name="site_settings[signup_bonus]" class="modal-select2" >
                                <option value="">
                                    {{translate("Select Package")}}
                                </option>
                                @foreach( $packages as $package)
                                    <option {{ site_settings("signup_bonus") == $package->id ? 'selected' :""}}  value="{{$package->id}}">
                                        {{$package->title}}
                                    </option>
                                @endforeach 
                            </select>
                       </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="i-btn btn--md ripple-dark" data-anim="ripple" data-bs-dismiss="modal">
                        {{translate("Close")}}
                    </button>
                    <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                        {{translate("Submit")}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";
        
            $(".modal-select2").select2({
			   placeholder:"{{translate('Select Item')}}",

               dropdownParent: $("#subscription-config")
	     	})
         
	})(jQuery);
</script>
@endpush