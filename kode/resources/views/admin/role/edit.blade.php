@extends('admin.layouts.master')
@section('content')

<div class="i-card-md">
    <div class="card-body">
        <form action="{{route('admin.role.update')}}" class="add-listing-form" method="post">
            @csrf
            <input type="hidden" name="id" value="{{$role->id}}">
            <div class="row g-4">
                <div class="col-12">
                    <div class="form-inner">
                        <label for="name" >
                            {{translate('Name')}}  <span  class="text-danger">*</span>
                        </label>
                        <input name="name" placeholder="{{translate('Enter Role Name')}}" id="name" value="{{$role->name}}"  required   type="text">
                    </div>
                </div>
                <div class="col-12">
                    <label  class="pointer">
                        {{translate('Permissions')}} 
                        <input class="check-role  form-check-input me-1" id="checkAll" type="checkbox" value="all_role">
                    </label>
                </div>
                @php
                  $oldPermissions = (array)$role->permissions;
                @endphp
                @foreach (config('settings')['role_permissions'] as $key=>$permissions)
                    <div class="col-xl-6">
                        <div class="p-3 border rounded-1">                  
                            <h6> {{ucfirst(str_replace("_"," ",$key))}}</h6>   
                            <div class="row g-3 mt-10">
                                @foreach($permissions as $module)
                                    <div class="col-md-6">
                                        <div
                                            class="d-flex align-items-center justify-content-between gap-3 form-control p-2">
                                            <label  for="{{$module}}" class="mb-0">   
                                                {{
                                                    ucwords(str_replace("_" ,' ',$module))
                                                }} 
                                            </label>
                                            <div class="form-check form-switch">
                                            <input
                                            @if ($oldPermissions && isset($oldPermissions[$key]) && in_array($module,$oldPermissions[$key]))
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
                        {{translate("Update")}}
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

        checkebox_event(".module-permission",'.check-role');
        
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




