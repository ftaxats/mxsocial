@extends('admin.layouts.master')
@section('content')

<div class="i-card-md">
    <div class="card-body">
        <form action="{{route('admin.role.store')}}" class="role-form" method="post">
            @csrf
            <div class="row g-4">
                <div class="col-12">
                    <div class="form-inner">
                        <label for="name" >
                            {{translate('Name')}}  <span  class="text-danger">*</span>
                        </label>
                        <input name="name" placeholder="{{translate('Enter Role Name')}}" id="name" value="{{old('name')}}" required   type="text">
                    </div>
                </div>
                <div class="col-12">
                    <label for="checkAll"  class="pointer">
                        {{translate('All Permissions')}} 
                    </label>
                    <input class="check-role  form-check-input me-1" id="checkAll" type="checkbox" value="all_role">
                </div>
                @foreach (config('settings')['role_permissions'] as $key=>$permissions)
                    <div class="col-xl-12">                     
                        <div class="p-3 border rounded-1">
                            <h6>{{ucfirst(str_replace("_"," ",$key))}}</h6>
                            <div class="row g-3 mt-10">
                                @foreach($permissions as $module)
                                    <div class="col-md-3">
                                        <div
                                            class="d-flex align-items-center justify-content-between gap-3 form-control p-2">
                                            <label  for="{{$module}}" class="mb-0">   
                                                {{
                                                    ucwords(str_replace("_" ,' ',$module))
                                                }} 
                                            </label>
                                            <div class="form-check form-switch">
                                                <input
                                                    @if (old('permissions') && isset(old('permissions')[$key][$module]))
                                                    checked
                                                    @endif
                                                    type="checkbox"
                                                    value="{{$module}}" name="permissions[{{$key}}][{{$module}}] "
                                                    class="module-permission form-check-input"
                                                    id="{{$module}}" />
                                                <label
                                                    class="form-check-label"
                                                    for="{{$module}}"></label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>                   
                    </div>
                @endforeach
                <div class="col-12">
                    <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                        {{translate("Submit")}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";

        $(document).on('click','.check-role' ,function(e){

            if($(this).is(':checked')){
                $(`.module-permission`).prop('checked', true);
            }
            else{
                $(`.module-permission`).prop('checked', false);
            }
        })

        $(document).on('click','.module-permission' ,function(e){
     
             checkebox_event(".module-permission",'.check-role');
        })

	})(jQuery);
    
</script>
@endpush




