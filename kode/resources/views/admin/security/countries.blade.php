@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.security.country.bulk")}}' method="post">
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
                                </ul>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
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
                                    <input name="search" value="{{request()->input('search')}}" type="search" placeholder="{{translate('Search name or code')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route('admin.security.country.list')}}"  class="i-btn btn--sm danger">
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
                            @if(check_permission('update_security'))
                                <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                            @endif#
                        </th>
                        <th scope="col">{{translate('Name')}}</th>
                        <th scope="col">{{translate('Phone Code')}}</th>
                        <th scope="col">{{translate('Total Ip')}}</th>
                        <th class="text-start" scope="col">{{translate('Is Blocked')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse ($countries as $country)
                            <tr>
                                <td data-label="#">
                                    @if( check_permission('update_security'))
                                           <input  type="checkbox" value="{{$country->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$country->id}}" />
                                    @endif
                                    {{$loop->iteration}}
                                </td>
                                <td data-label='{{translate("name")}}'>
                                    <div class="user-meta-info d-flex align-items-center gap-2"><p>{{$country->name}}</p>
                                            <span class="i-badge capsuled info">
                                                {{$country->code}}
                                            </span>
                                    </div>
                                </td>
                                <td data-label='{{translate("Phone Code")}}'>
                                    {{$country->phone_code}}
                                </td>
                                <td data-label='{{translate("Total Ip")}}'>
                                    <a href="{{route('admin.security.ip.list',['country_id' => $country->id])}}">
                                        <span class="i-badge capsuled success">
                                           {{translate("No of ip")}} {{$country->ip_count}}
                                        </span>
                                    </a>
                                </td>
                                <td data-label='{{translate("Blocked")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_security') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="is_blocked"
                                            data-route="{{ route('admin.security.country.update.status') }}"
                                            data-status="{{ $country->is_blocked == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$country->id}}" {{$country->is_blocked ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$country->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$country->id}}"></label>
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
                {{ $countries->links() }}
            </div>
        </div>
    </div>
  
@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){

        "use strict";
        $(".select2").select2({})

	})(jQuery);
</script>
@endpush

