@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action="{{route('admin.user.bulk')}}" method="post">
                        @csrf
                         <input type="hidden" name="bulk_id" id="bulkid">
                         <input type="hidden" name="value" id="value">
                         <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('create_user') || check_permission('update_user') )
                        <div class="col-md-5 d-flex justify-content-start gap-2">
                            @if(check_permission('update_menu'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(check_permission('update_menu'))
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif
                            @if(check_permission('create_user'))
                                <div class="action">
                                    <button type="button"   data-bs-toggle="modal" data-bs-target="#addUser" class="i-btn btn--sm success">
                                        <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="col-md-7 d-flex justify-content-md-end justify-content-start">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                    <select name="country" id="filter_country" class="filter-country">
                                        <option value="">
                                            {{translate('Select Country')}}
                                        </option>
                                        @foreach($countries as $country)
                                           <option  {{$country->name ==   request()->input('country') ? 'selected' :""}} value="{{$country->name}}"> {{$country->name}}
                                          </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inner  ">
                                      <input name="search" value="{{request()->search}}" type="search" placeholder="{{translate('Search by name,email,phone')}}">
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
                <table>
                    <thead>
                        <tr>
                            <th>
                                @if(check_permission('update_user'))
                                   <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                @endif#
                            </th>
                            <th scope="col">
                                {{translate('Name')}}
                            </th>
                            <th scope="col"  >
                                {{translate('Email - Phone')}}
                            </th>
                            <th scope="col"  >
                                {{translate('Country')}}
                            </th>
                            <th scope="col">
                                {{translate('Balance')}}
                            </th>
                            <th scope="col">
                                {{translate('Created By')}}
                            </th>
                            <th scope="col">
                                {{translate('Status')}}
                            </th>
                            <th scope="col">
                                {{translate('Options')}}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users  as $user)

                            <tr>
                                <td data-label="#">
                                    @if( check_permission('update_user') )
                                        <input type="checkbox" value="{{$user->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$user->id}}" />
                                    @endif
                                    {{$loop->iteration}}
                                </td>
                                <td data-label="{{translate('Name')}}">
                                    <div class="user-meta-info d-flex align-items-center gap-2">
                                        <img class="rounded-circle avatar-sm"  src='{{imageURL($user->file,"profile,user",true) }}' alt="{{@$user->file->name}}">
                                        <p>	{{ $user->name ?? "-"}}</p>
                                        @if($user->runningSubscription)
                                            <small data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Running Plan')}}" class="i-badge success">{{@$user->runningSubscription?->package->title}}</small>
                                        @endif
                                    </div>
                                </td>
                                <td data-label='{{translate("Email - Phone")}}'>
                                    <div class="d-block">
                                        {{$user->email}}
                                    </div>
                                    <span class="i-badge info">{{$user->phone}}</span>
                                </td>
                                <td  data-label="{{translate('Country')}}">
                                    {{$user->country->name}}
                                </td>
                                <td data-label="{{translate('Balance')}}">
                                    <span class="i-badge-solid primary"> {{num_format($user->balance,base_currency())}} @if(session('currency') && base_currency()->code != session('currency')?->code) -
                                        {{num_format(
                                            number : $user->balance,
                                            calC   : true
                                    )}} @endif</span>
                                </td>
                                <td data-label="{{translate('Created By')}}">
                                    <span class="i-badge capsuled success">
                                        {{$user->createdBy->name}}
                                    </span>
                                </td>
                                <td data-label="{{translate('Status')}}">
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_user') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.user.update.status') }}"
                                            data-model="Admin"
                                            data-status="{{ $user->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$user->uid}}" {{$user->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$user->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$user->id}}"></label>
                                    </div>
                                </td>
                                <td data-label="{{translate('Options')}}">
                                    <div class="table-action">
                                        @if(check_permission('update_user') ||  check_permission('delete_user'))
                                            @if(check_permission('update_user'))

                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Login')}}" target="_blank" href="{{route('admin.user.login', $user->uid)}}" class="icon-btn success">
                                                    <i class="las la-sign-in-alt"></i>
                                                </a>
                                                <a   href="{{route('admin.user.show', $user->uid)}}"   data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Show')}}" class="icon-btn info">
                                                    <i class="las la-eye"></i>
                                                </a>

                                            @endif

                                            @if(check_permission('delete_user'))
                                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" data-href="{{route('admin.user.destroy',$user->uid)}}" class="delete-item icon-btn danger">
                                                    <i class="las la-trash-alt"></i></a>
                                            @endif
                                        @else
                                          --
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="border-bottom-0" colspan="8">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="Paginations">
                    {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modal.delete_modal')

    <div class="modal fade" id="addUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addUser" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Add User')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.user.store')}}" id="storeModalForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label  for="Name">
                                        {{translate('Name')}} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="Name"  required  placeholder="{{translate('Enter Name')}}"
                                        value="{{old('name')}}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="username">
                                        {{translate('Username')}}
                                            <small class="text-danger">*</small>
                                    </label>
                                    <input type="text" name="username" id="username"  placeholder="{{translate('Enter User Name')}}"
                                        value="{{old('username')}}" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="email">
                                        {{translate('Email')}}
                                            <small class="text-danger">*</small>
                                    </label>

                                    <input type="email" name="email" id="email"   placeholder="{{translate('Enter Email')}}"
                                        value="{{old('email')}}" required>
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
                                            <option {{old('country_id') == $country->id ? "selected" :""}} value="{{$country->id}}">
                                                 {{$country->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="phone">
                                        {{translate('Phone')}}
                                    </label>
                                    <input type="text" name="phone" id="phone" placeholder="{{translate('Enter Phone')}}"
                                        value="{{old('phone')}}" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="status">
                                        {{translate('Status')}}
                                            <small class="text-danger">*</small>
                                    </label>
                                    <select class="select2" name="status" id="status">
                                        @foreach(App\Enums\StatusEnum::toArray() as $status=>$value)
                                            <option {{old('status') == $value ? "selected" :"" }} value="{{$value}}">
                                                {{$status}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="image">
                                        {{translate('Profile Image')}}
                                    </label>
                                    <input data-size = "{{config('settings')['file_path']['profile']['user']['size']}}" id="image" name="image" type="file" class="preview" >
                                    <div class="mt-2 image-preview-section">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-inner">
                                    <label for="password">
                                        {{translate('Password')}}
                                            <small class="text-danger">*({{translate('Minimum 6 Characters')}})</small>
                                    </label>
                                    <input placeholder="{{translate('Enter Password')}}" type="text" id="password"  name="password" value="{{old('password')}}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-inner">
                                    <label for="password_confirmation">
                                        {{translate('Confirm Password')}}
                                            <small class="text-danger">*({{translate('Minimum 6 Characters')}})</small>
                                    </label>
                                    <input placeholder="{{translate('Enter Confirm Password')}}" type="text" id="password_confirmation"  name="password_confirmation" value="{{old('password_confirmation')}}">
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
        $(".select2").select2({
			placeholder:"{{translate('Select Status')}}",
			dropdownParent: $("#addUser"),
		})
        $("#country").select2({
			placeholder:"{{translate('Select Country')}}",
			dropdownParent: $("#addUser"),
		})
        $(".filter-country").select2({
			placeholder:"{{translate('Select Country')}}",

		})
	})(jQuery);
</script>
@endpush





