@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.security.ip.bulk")}}' method="post">
                        @csrf
                        <input type="hidden" name="bulk_id" id="bulkid">
                        <input type="hidden" name="value" id="value">
                        <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('update_security') )
                        <div class="col-md-6 d-flex justify-content-start gap-2">
                            <div class="i-dropdown bulk-action mx-0 d-none">
                                <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="las la-cogs fs-15"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                        <li>
                                            <button type="button" name="bulk_status" data-type ="is_blocked" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{ $v == 1 ? 'Block' :"Unblock" }}</button>
                                        </li>
                                    @endforeach

                                    <li>
                                        <button data-type="delete"  class="dropdown-item bulk-action-modal">
                                            {{translate("Delete")}}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#ip-form" class="i-btn btn--sm success create">
                                <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                            </button>
                        </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="filter-wrapper">
                            <button class="i-btn btn--primary btn--sm filter-btn" type="button">
                                <i class="las la-filter"></i>
                            </button>
                            <div class="filter-dropdown">
                                <form action="{{route(Route::currentRouteName())}}" method="get">
                                    <div class="form-inner">
                                        <select name="is_blocked" id="is_blocked" class="select2">
                                            <option value="">
                                                {{translate('Select status')}}
                                            </option>
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                            <option  {{request()->input('is_blocked') ==   $v ? 'selected' :""}} value="{{$v}}">  {{ $v == 1 ? 'Blocked' :"Unblock" }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inner">
                                        <select name="country_id" id="country_id" class="select2">
                                            <option value="">
                                                {{translate('Select country')}}
                                            </option>
                                            @foreach($countries as $country)
                                            <option  {{$country->id ==   request()->input('country_id') ? 'selected' :""}} value="{{$country->id}}">
                                                {{$country->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inner">
                                        <input name="ip_address" value="{{request()->input('ip_address')}}" type="search" placeholder="{{translate('Searh by IP')}}">
                                    </div>
                                    <button class="i-btn btn--md info w-100">
                                        <i class="las la-sliders-h"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="ms-3">
                            <a href="{{route('admin.security.ip.list')}}"  class="i-btn btn--sm danger">
                                <i class="las la-sync"></i>
                            </a>
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
                            @if(check_permission('update_security'))
                                <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                            @endif#
                        </th>
                        <th scope="col">{{translate('Ip')}}</th>
                        <th scope="col">{{translate('Country')}}</th>
                        <th  scope="col">{{translate('Is Blocked')}}</th>
                        <th scope="col">{{translate('Action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse ($ip_lists as $ip)
                            <tr>
                                <td data-label="#">
                                    @if( check_permission('update_security'))

                                        <input  type="checkbox" value="{{$ip->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$ip->id}}" />

                                    @endif
                                    {{$loop->iteration}}
                                </td>
                                <td data-label='{{translate("Ip")}}'>
                                     {{$ip->ip_address}}
                                </td>

                                <td data-label='{{translate("Country")}}'>
                                    <span class="i-badge capsuled success">
                                        {{$ip->country->name}}
                                    </span>
                               </td>

                                <td data-label='{{translate("Blocked")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_security') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="is_blocked"
                                            data-route="{{ route('admin.security.ip.update.status') }}"
                                            data-status="{{ $ip->is_blocked == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$ip->id}}" {{$ip->is_blocked ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$ip->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$ip->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Action")}}'>
                                    <div class="table-action">
                                        @if(check_permission('update_security'))
                                           <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}" href="javascript:void(0);" data-ip = "{{$ip}}"  class="fs-15 icon-btn warning update"><i class="las la-pen"></i></a>

                                            <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);"    data-href="{{route('admin.security.ip.destroy',$ip->id)}}" class="pointer delete-item icon-btn danger">
                                                <i class="las la-trash-alt"></i>
                                            </a>
                                        @else
                                               --
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="border-bottom-0" colspan="5">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="Paginations">
                {{ $ip_lists->links() }}
            </div>
        </div>
    </div>

@endsection
@section('modal')

    @include('modal.delete_modal')

    @include('modal.bulk_modal')

    <div class="modal fade" id="ip-form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="ip-form"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Add Ip')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{route('admin.security.ip.store')}}" id="ipForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">


                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="ipAddress" class="form-label" >
                                        {{translate('Ip address')}} <small class="text-danger">*</small>
                                    </label>
                                    <input required type="text" placeholder="{{translate('Ip')}}" id="ipAddress" name="ip_address" value="{{old('ip_address')}}">

                                </div>
                            </div>


                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="country" class="form-label" >
                                        {{translate('Country')}} <small class="text-danger">*</small>
                                    </label>

                                    <select name="country_id" id="country">
                                        <option value="">
                                            {{translate('Select country')}}
                                        </option>
                                        @foreach ($countries as $country )
                                            <option value="{{$country->id}}">
                                                 {{$country->name}}
                                            </option>
                                        @endforeach
                                    </select>

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


    <div class="modal fade" id="ip-form-update" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="ip-form-update"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Update Ip')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{route('admin.security.ip.update')}}"  method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <input hidden type="text" name="id">

                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="updateCountry" class="form-label" >
                                        {{translate('Country')}} <small class="text-danger">*</small>
                                    </label>

                                    <select name="country_id" id="updateCountry">

                                        @foreach ($countries as $country )
                                            <option value="{{$country->id}}">
                                                 {{$country->name}}
                                            </option>
                                        @endforeach
                                    </select>

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

        $(".select2").select2({})

        $("#country").select2({
			dropdownParent: $("#ip-form"),
		})
        $("#updateCountry").select2({
			dropdownParent: $("#ip-form-update"),
		})


        $(document).on('click','.update',function(e){

            e.preventDefault()

            var ip = JSON.parse($(this).attr('data-ip'))
            var modal = $('#ip-form-update')
            modal.find('input[name="id"]').val(ip.id)
            $('#updateCountry').val(`${ip.country_id}`);
            $('#updateCountry').trigger('change');
            modal.modal('show')
        })


	})(jQuery);
</script>
@endpush

