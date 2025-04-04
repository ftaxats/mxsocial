@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.currency.bulk")}}' method="post">
                        @csrf
                        <input type="hidden" name="bulk_id" id="bulkid">
                        <input type="hidden" name="value" id="value">
                        <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('create_currency') || check_permission('update_currency') )
                        <div class="col-md-6 d-flex justify-content-start gap-2">
                            @if(check_permission('update_currency'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(check_permission('update_currency'))
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            @if(check_permission('create_currency'))
                                <button type="button" data-bs-toggle="modal" data-bs-target="#currency-form" class="i-btn btn--sm success create">
                                    <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                </button>
                            @endif
                            @if(check_permission('update_currency'))
                                <button type="button" data-bs-toggle="modal" data-bs-target="#currency-config" class="i-btn btn--sm danger create">
                                    <i class="las la-cogs me-1"></i>  {{translate('Configuration')}}
                                </button>
                            @endif
                        </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                    <input name="search" value="{{request()->input('search')}}" type="search" placeholder="{{translate('Search name or code')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route('admin.currency.list')}}"  class="i-btn btn--sm danger">
                                    <i class="las la-sync"></i>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-container position-relative">
                @include('admin.partials.loader')
                <table>
                    <thead>
                        <tr>
                            <th scope="col">
                                @if(check_permission('update_currency') || check_permission('delete_currency'))
                                    <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                @endif#
                            </th>
                            <th scope="col">{{translate('Name')}}</th>
                            <th scope="col">{{translate('Symbol')}}</th>
                            <th scope="col">{{translate('Code')}}</th>
                            <th scope="col">{{translate('Exchange Rate (1 '.base_currency()->code.' = ?)')}}</th>
                            <th scope="col">{{translate('Status')}}</th>
                            <th scope="col">{{translate('Action')}}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($currencies as $currency)
                            <tr>
                                <td data-label="#">
                                    @if(check_permission('update_currency') || check_permission('delete_currency'))
                                        <input  type="checkbox" value="{{$currency->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$currency->id}}" />
                                    @endif
                                    {{$loop->iteration}}
                                </td>
                                <td data-label='{{translate("name")}}'>
                                    <div class="user-meta-info d-flex align-items-center gap-2"><p>{{$currency->name}}</p>
                                        @if($currency->default == App\Enums\StatusEnum::true->status())
                                            <span class="i-badge capsuled success">
                                                <i class="las la-star"></i>  {{translate('Default')}}
                                            </span>
									    @endif
                                    </div>
                                </td>
                                <td data-label="{{translate('symbol')}}">{{$currency->symbol}}</td>
                                <td data-label="{{translate('code')}}">{{$currency->code}}</td>
                                <td data-label='{{translate("exchange rate")}}' >
                                    {{translate('1 '.base_currency()->code.' = ')}} {{num_format($currency->exchange_rate,$currency)}}
                                </td>
                                <td data-label='{{translate("Status")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_currency') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.currency.update.status') }}"
                                            data-status="{{ $currency->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$currency->uid}}" {{$currency->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$currency->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$currency->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Action")}}'>
                                    <div class="table-action">
                                        @if(check_permission('update_currency') || check_permission('delete_currency') )
                                            @if(check_permission('update_currency'))
                                            @if($currency->default != App\Enums\StatusEnum::true->status())
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Default')}}" href="{{route('admin.currency.make.default',$currency->uid)}}" class="icon-btn info">
                                                    <i class="las la-star"></i>
                                                </a>
                                            @endif
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}"  href="javascript:void(0);" data-currency ="{{$currency}}" class="update fs-15 icon-btn warning"><i class="las la-pen"></i></a>
                                            @endif

                                            @if(check_permission('delete_currency'))
                                                @if($currency->default != App\Enums\StatusEnum::true->status() && $currency->code != "USD" && ($currency->code != @session('currency')->code))
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);"  data-href="{{route('admin.currency.destroy',$currency->id)}}" class="pointer delete-item icon-btn danger">
                                                        <i class="las la-trash-alt"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        @else
                                            --
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="border-bottom-0" colspan="7">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="Paginations">
                {{ $currencies->links() }}
            </div>
        </div>
    </div>

@endsection
@section('modal')
    @include('modal.delete_modal')
    @include('modal.bulk_modal')
    <div class="modal fade" id="currency-form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="currency-form"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Add Currency')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.currency.store')}}" id="currenncyForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input disabled  hidden name="id" type="text">
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="name" class="form-label" >
                                        {{translate('Name')}} <small class="text-danger">*</small>
                                    </label>
                                    <input required type="text" placeholder="{{translate('Name')}}" id="name" name="name" value="{{old('name')}}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="code" class="form-label" >
                                        {{translate('Code')}} <small class="text-danger">*</small>
                                    </label>
                                    <input required type="text" placeholder="{{translate('Code')}}" id="code" name="code" value="{{old('code')}}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="symbol" class="form-label" >
                                        {{translate('Symbol')}} <small class="text-danger">*</small>
                                    </label>
                                    <input required type="text" placeholder="{{translate('Symbol')}}" id="symbol" name="symbol" value="{{old('symbol')}}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="exchange_rate" class="form-label" >
                                        {{translate('Exchange Rate')}} <small class="text-danger">*</small>
                                    </label>
                                    <input type="number" placeholder='{{translate("Exchange Rate")}}' min="0" step="any" id="exchange_rate"  class="form-control"
                                       name="exchange_rate"
                                       value="{{ old('exchange_rate')}}"
                                       required>
                                </div>
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
    <div class="modal fade" id="currency-config" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="currency-config"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Set Currency Formats')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.currency.config')}}" id="currenncyConfigForm" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="Alignment" class="form-label" >
                                        {{translate('Currency Alignment')}} <small class="text-danger">*</small>
                                    </label>
                                    <select class="select2" name="site_settings[currency_alignment]" id="Alignment">
                                        @foreach (Arr::get(config('settings'),'currency_alignment' ,[]) as $k => $v)
                                            <option  {{site_settings('currency_alignment') == $v ? "selected" :""  }} value="{{$v}}">
                                                 {{$k}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="num_decimal" class="form-label" >
                                        {{translate('No of Decimals')}} <small class="text-danger">*</small>
                                    </label>
                                    <input id="num_decimal" placeholder='{{translate("Enter number")}}' type="number" name="site_settings[num_of_decimal]" value="{{site_settings('num_of_decimal')}}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="decimal_separator" class="form-label" >
                                        {{translate('Decimal Separator')}} <small class="text-danger">*</small>
                                    </label>
                                    <input id="decimal_separator" placeholder='{{translate("Decimal separator")}}' type="text" name="site_settings[decimal_separator]" value="{{site_settings('decimal_separator')}}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="thousands_separator" class="form-label" >
                                        {{translate('Thousands Separator')}} <small class="text-danger">*</small>
                                    </label>
                                    <input id="thousands_separator" placeholder='{{translate("Thousands Separator")}}' type="text" name="site_settings[thousands_separator]" value="{{site_settings('thousands_separator')}}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="price_format" class="form-label" >
                                        {{translate('Price Format')}} <small class="text-danger">*</small>
                                    </label>
                                    <select class="select2 price-format" name="site_settings[price_format]" id="price_format">
                                        @foreach (Arr::get(config('settings'),'price_format' ,[]) as $k => $v)
                                            <option  {{site_settings("price_format") == $v ? "selected" :""  }} value="{{$v}}">
                                                {{ucfirst(str_replace("_"," ",$k))}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12 @if(site_settings('price_format') !=  1)d-none @endif truncate-section">
                                <div class="form-inner">
                                    <label for="truncate_after" class="form-label" >
                                        {{translate('Truncate After')}} <small class="text-danger">* ({{translate("Must be greater than 1000")}})</small>
                                    </label>
                                    <input id="truncate_after" placeholder='{{translate("Enter number")}}' min="1000" type="number" name="site_settings[truncate_after]" value="{{site_settings('truncate_after')}}">
                                </div>
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
        $(document).on('change','.price-format',function(e){
            if($(this).val() == 1){
                $('.truncate-section').removeClass('d-none');
            }else{
                $('.truncate-section').addClass('d-none');
            }
        });
        $(document).on('click','.create',function(e){
            e.preventDefault()
            var modal = $('#currency-form');
            var form = modal.find('form');
            modal.find('input[name="id"]').attr('disabled',true)
            modal.find('.modal-title').html("{{translate('Add Currency')}}")
            modal.find('#currenncyForm').attr('action','{{route("admin.currency.store")}}')
            form[0].reset();
        });
        $(document).on('click','.update',function(e){
            e.preventDefault()
            var currency = JSON.parse($(this).attr('data-currency'))
            var modal = $('#currency-form')
            modal.find('#currenncyForm').attr('action','{{route("admin.currency.update")}}')
            modal.find('.modal-title').html("{{translate('Update Currency')}}")
            modal.find('input[name="name"]').val(currency.name)
            modal.find('input[name="code"]').val(currency.code)
            modal.find('input[name="id"]').attr('disabled',false)
            modal.find('input[name="id"]').val(currency.id)
            modal.find('input[name="symbol"]').val(currency.symbol)
            modal.find('input[name="exchange_rate"]').val(currency.exchange_rate)
            modal.modal('show')
        })

        $(".select2").select2({
			dropdownParent: $("#currency-config"),
		})
	})(jQuery);
</script>
@endpush





