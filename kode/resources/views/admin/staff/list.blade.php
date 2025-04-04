@extends('admin.layouts.master')

@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.staff.bulk")}}' method="post">
                        @csrf
                         <input type="hidden" name="bulk_id" id="bulkid">
                         <input type="hidden" name="value" id="value">
                         <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('create_staff') || check_permission('update_staff') || check_permission('delete_staff'))
                        <div class="col-md-5 d-flex justify-content-start gap-2">
                            @if(check_permission('update_staff') || check_permission('delete_staff'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>

                                    <ul class="dropdown-menu">
                                        @if(check_permission('delete_staff'))
                                            <li>
                                                <button data-message='{{translate("Are you sure you want to remove these record permanently?")}}' data-type ="{{request()->routeIs('admin.staff.recycle.list') ? 'force_delete' :'delete'}}"   class="dropdown-item bulk-action-modal">
                                                    {{translate("Delete")}}
                                                </button>
                                            </li>

                                            @if(request()->routeIs('admin.staff.recycle.list'))
                                                <li>
                                                    <button data-message='{{translate("Are you sure you want to restore these record ?")}}' data-type ="restore"  class="dropdown-item bulk-action-modal">
                                                        {{translate("Restore")}}
                                                    </button>
                                                </li>
                                            @endif
                                        @endif

                                        @if(check_permission('update_staff'))
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button"  name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            <div class="action d-flex justify-content-start gap-2">
                                @if(request()->routeIs('admin.staff.list'))
                                    @if(check_permission('create_staff'))
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#addStaff" class="i-btn btn--sm success">
                                            <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                        </button>
                                    @endif
                                    <a href="{{route('admin.staff.recycle.list')}}" class="i-btn btn--sm danger">
                                        <i class="las la-recycle me-1"></i>  {{translate('Recycle Bin')}}
                                    </a>
                                @else
                                    <a href="{{route('admin.staff.list')}}" class="i-btn btn--sm success">
                                        <i class="las la-arrow-left me-1"></i>  {{translate('Back')}}
                                    </a>
                                @endif
                            </div>

                        </div>
                    @endif

                    <div class="col-md-7 d-flex justify-content-md-end justify-content-start">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                      <input name="search" value="{{request()->input('search')}}" type="search" placeholder="{{translate('Search by Name or Username or Phone or Email or Role')}}">
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
                            <th scope="col">
                                @if(check_permission('update_staff') || check_permission('delete_staff'))
                                    <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                @endif
                                &nbsp;
                                {{translate('Profile Details')}}
                            </th>

                            <th scope="col"  >
                                {{translate('Role Type')}}
                            </th>

                            <th scope="col">
                                {{translate('Created By')}}
                            </th>

                            <th scope="col">
                                {{translate('Last Login')}}
                            </th>

                            <th scope="col">
                                {{translate('Status')}}
                            </th>

                            <th scope="col">
                                {{translate('Action')}}
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($staffs as $staff)
                            <tr>
                                <td data-label="{{translate('Name')}}">
                                    <div class="user-meta-info d-flex align-items-center gap-2">
                                        @if( check_permission('update_staff') || check_permission('delete_staff'))
                                            <input type="checkbox" value="{{$staff->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$staff->id}}" />
                                        @endif
                                        &nbsp;
                                        <img class="rounded-circle avatar-lg" src='{{imageURL(@$staff->file,"profile,admin",true)}}' alt="{{@$staff->file->name}}">
                                        <div>
                                            <strong>{{$staff->name}}</strong><br>
                                            <span>{{$staff->email}}</span><br>
                                            <span>{{$staff->phone ? $staff->phone : ""}}</span>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="{{translate('Role Type')}}">
                                    <span class="i-badge capsuled success">
                                        {{ $staff->role->name }}
                                    </span>
                                </td>

                                <td data-label="{{translate('Created By')}}">
                                    <span class="i-badge capsuled info">
                                        {{$staff->createdBy->username}}
                                    </span>
                                </td>

                                <td data-label="{{translate('Created By')}}">
                                    {{ $staff->last_login ? diff_for_humans($staff->last_login) : "-"}}
                                </td>

                                <td data-label="{{translate('Status')}}">
                                    <div class="form-check form-switch switch-center">
                                        <input  {{!check_permission('update_staff') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.staff.update.status') }}"
                                            data-model="Admin"
                                            data-status="{{ $staff->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$staff->uid}}" {{$staff->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$staff->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$staff->id}}"></label>
                                    </div>
                                </td>


                                <td data-label="{{translate('Action')}}">
                                    <div class="table-action">
                                        @if(check_permission('update_staff') ||  check_permission('delete_staff'))
                                            @if(check_permission('update_staff') && request()->routeIs('admin.staff.list'))
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Login')}}"   target="_blank" href="{{route('admin.staff.login', $staff->uid)}}" class="icon-btn success"><i class="las la-sign-in-alt"></i></a>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update Password')}}"  href="javascript:void(0);" data-uid ="{{$staff->uid}}" class="passwordUpdate   icon-btn warning"><i class="las la-key"></i></a>
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}"  href="javascript:void(0);" data-staff ="{{$staff}}" class="update fs-15 icon-btn info"><i class="las la-pen"></i></a>
                                            @endif

                                            @if(check_permission('delete_staff'))
                                                @if(request()->routeIs('admin.staff.recycle.list'))
                                                    <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Restore')}}"   data-href="{{route('admin.staff.restore',$staff->uid)}}" class="pointer restore-item icon-btn success">
                                                        <i class="las la-sync"></i>
                                                    </a>
                                                @endif

                                                @php

                                                  $destoryRoute = request()->routeIs('admin.staff.recycle.list')  ? route('admin.staff.permanent.destroy',$staff->uid) :route('admin.staff.destroy',$staff->uid);

                                                @endphp

                                                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}"   data-href="{{$destoryRoute}}" class="pointer delete-item icon-btn danger">
													<i class="las la-trash-alt"></i>
                                                </a>
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
                {{ $staffs->links() }}
            </div>
        </div>
    </div>
@endsection
@section('modal')
    @include('modal.delete_modal')
    @include('modal.bulk_modal')
    <div class="modal fade" id="addStaff" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addStaff" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >
                        {{translate('Add Staff')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{route('admin.staff.store')}}" id="storeModalForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="name">
                                        {{translate('Name')}}
                                    </label>
                                    <input type="text" name="name" id="name"  placeholder="{{translate('Enter Name')}}"
                                        value="{{old('name')}}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="username">
                                        {{translate('Username')}}
                                            <small class="text-danger">*</small>
                                    </label>

                                    <input type="text" name="username" id="username" placeholder="{{translate('Enter User Name')}}"
                                        value="{{old('username')}}" required>
                                </div>

                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="email">
                                        {{translate('Email')}}
                                            <small class="text-danger">*</small>
                                    </label>

                                    <input type="text" name="email" id="email" placeholder="{{translate('Enter Email')}}"
                                        value="{{old('email')}}" required>
                                </div>

                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="phone">
                                        {{translate('Phone')}}

                                    </label>

                                    <input type="text" name="phone" id="phone"   placeholder="{{translate('Enter Phone')}}"
                                        value="{{old('phone')}}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label class="form-label" for="status">
                                        {{translate('Status')}}
                                            <small class="text-danger">*</small>
                                    </label>

                                    <select class="select2"  name="status" id="status">
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
                                    <label for="role_id">
                                        {{translate('Role')}}
                                    </label>

                                    <select class="from-select" name="role_id" id="role_id">
                                        @foreach($roles as $key=>$id)
                                            <option {{old('role_id') == $id ? "selected" :"" }} value="{{$id}}">
                                                {{$key}}
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
                                    <input id="image" name="image" type="file">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="password">
                                        {{translate('Password')}}
                                            <small class="text-danger">*({{translate('Minimum 5 Characters')}})</small>
                                    </label>

                                    <input placeholder="{{translate('Enter Password')}}" type="password" name="password" value="{{old('password')}}">
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

    <div class="modal fade" id="updateStaff" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateStaff" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >
                        {{translate('Update Staff')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{route('admin.staff.update')}}" id="updateModalForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" class="form-control">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="Name">
                                        {{translate('Name')}}
                                    </label>
                                    <input type="text" name="name" id="Name" placeholder="{{translate('Enter Name')}}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="userName">
                                        {{translate('User Name')}}
                                            <small class="text-danger">*</small>
                                    </label>
                                    <input type="text" name="username" id="userName" placeholder="{{translate('Enter User Name')}}" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="Email">
                                        {{translate('Email')}}
                                            <small class="text-danger">*</small>
                                    </label>
                                    <input type="text" name="email" id="Email" placeholder="{{translate('Enter Email')}}" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="phoneNumber">
                                        {{translate('Phone')}}
                                            <small class="text-danger">*</small>
                                    </label>
                                    <input type="text" name="phone" id="phoneNumber"  placeholder="{{translate('Enter Phone')}}" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="roleId">
                                        {{translate('Role')}}
                                    </label>
                                    <select class="form-select" name="role_id" id="roleId" >
                                        @foreach($roles as $key=>$id)
                                            <option  value="{{$id}}">
                                                {{$key}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="pro-image">
                                        {{translate('Profile Image')}}
                                    </label>
                                    <input id="pro-image" name="image" type="file">
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

    <div class="modal fade" id="updatePassword" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updatePassword" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >
                        {{translate('Update Password')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{route('admin.staff.update.password')}}" id="updatePasswordForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="uid" id="uid" class="form-control">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label class="form-label" for="password">
                                        {{translate('Password')}} <span class="text-danger" >*</span>
                                    </label>
                                    <input required type="text" name="password" id="password"   placeholder='{{translate("Enter Password")}}'>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label class="form-label" for="password_confirmation">
                                        {{translate('Confirm Password')}}
                                            <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="password_confirmation" name="password_confirmation"   placeholder='{{translate("Enter Confrim Password")}}' required>
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
			dropdownParent: $("#addStaff"),
		})

        $(document).on('click','.update',function(e){
            e.preventDefault()
            var staff = JSON.parse($(this).attr('data-staff'))
            var modal = $('#updateStaff')
            modal.find('input[name="name"]').val(staff.name)
            modal.find('input[name="username"]').val(staff.username)
            modal.find('input[name="id"]').val(staff.id)
            modal.find('input[name="email"]').val(staff.email)
            modal.find('input[name="phone"]').val(staff.phone)
            modal.find(`select[name="role_id"] option[value="${staff.role_id}"]`).prop("selected", true);
            modal.modal('show')
        })

        $(document).on('click','.passwordUpdate',function(e){
            e.preventDefault()
            var uid = ($(this).attr('data-uid'))
            var modal = $('#updatePassword')
            modal.find('input[name="uid"]').val(uid)
            modal.modal('show')
        })

	})(jQuery);
</script>
@endpush





