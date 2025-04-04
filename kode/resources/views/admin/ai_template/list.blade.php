@extends('admin.layouts.master')
@section('content')

    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.ai.template.bulk")}}' method="post">
                        @csrf
                        <input type="hidden" name="bulk_id" id="bulkid">
                        <input type="hidden" name="value" id="value">
                        <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('create_ai_template') || check_permission('update_ai_template'))
                        <div class="col-md-6 d-flex justify-content-start gap-2">
                            @if(check_permission('update_ai_template'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>
                                    <ul class="dropdown-menu">

                                        @if(check_permission('update_ai_template'))

                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif
                            @if(check_permission('create_ai_template'))
                                <div class="action d-flex justify-content-start gap-2">
                                    <a href="{{route('admin.ai.template.create')}}"    class="i-btn btn--sm success">
                                        <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="filter-wrapper">
                            <button class="i-btn btn--primary btn--sm filter-btn" type="button">
                                <i class="las la-filter"></i>
                            </button>
                            <div class="filter-dropdown">
                                <form action="{{route(Route::currentRouteName())}}" method="get">
                                   @if(request()->routeIs("admin.ai.template.default"))
                                     <input hidden name="default" value="{{App\Enums\StatusEnum::true->status()}}"  type="text">
                                   @endif
                                    <div class="form-inner">
                                        <select name="status" id="status" class="select2">
                                            <option value="">
                                                {{translate('Select status')}}
                                            </option>
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                               <option  {{request()->input('status') ==   $v ? 'selected' :""}} value="{{$v}}"> {{translate($k)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inner">
                                        <select name="category" id="categoryFilter" class="select2">
                                            <option value="">
                                                {{translate('Select Category')}}
                                            </option>
                                            @foreach($categories as $category)
                                               <option  {{$category->slug ==   request()->input('category') ? 'selected' :""}} value="{{$category->slug}}"> {{$category->title}}
                                              </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inner">
                                        <select  name="subCategory" id="sub_category_id" class="sub_category_id" >
                                            <option value="">
                                                {{translate("Select One")}}
                                            </option>
                                            @foreach($subCategories as $subCategory)
                                                <option  {{$subCategory->slug ==   request()->input('subCategory') ? 'selected' :""}}
                                                    value="{{$subCategory->slug}}"> {{$subCategory->title}}
                                               </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inner">
                                        <select name="user" id="user" class="select2">
                                            <option value="">
                                                {{translate('Select User')}}
                                            </option>

                                            @foreach(system_users() as $user)
                                               <option  {{Arr::get($user,"username",null) ==   request()->input('user') ? 'selected' :""}} value="{{Arr::get($user,"username",null)}}"> {{Arr::get($user,"name",null)}}
                                              </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inner">
                                        <input name="search" value='{{request()->input("search")}}' type="search" placeholder="{{translate('Search by  title')}}">
                                    </div>
                                    <button class="i-btn btn--md info w-100">
                                        <i class="las la-sliders-h"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="ms-3">
                            <a href="{{route('admin.ai.template.list')}}"  class="i-btn btn--sm danger">
                                <i class="las la-sync"></i>
                            </a>
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
                                @if( check_permission('update_ai_template') || check_permission('delete_ai_template'))
                                    <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                @endif#
                            </th>
                            <th scope="col">
                                {{translate('Name')}}
                            </th>
                            <th scope="col">
                                {{translate('Total Word Generated')}}
                            </th>
                            <th scope="col">
                                {{translate('Category')}}
                            </th>
                            <th scope="col">
                                {{translate('Sub Category')}}
                            </th>
                            <th scope="col">
                                {{translate('Status')}}
                            </th>
                            <th scope="col">
                                {{translate('Default')}}
                            </th>
                            <th scope="col">
                                {{translate('Options')}}
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td data-label="#">
                                    @if(check_permission('create_ai_template') || check_permission('update_ai_template') || check_permission('delete_ai_template'))
                                        <input type="checkbox" value="{{$template->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$template->id}}" />
                                    @endif
                                    {{$loop->iteration}}
                                </td>
                                <td data-label='{{translate("Title")}}'>
                                    <div class="user-meta-info d-flex align-items-center gap-2">
                                        <i class="@php echo ($template->icon) @endphp " ></i>
                                        <p>	 {{$template->name}}</p>
                                    </div>
                                </td>
                                <td  data-label='{{translate("No of word")}}'>
                                    <span class="ms-5 i-badge capsuled success">
                                        {{$template->templateUsages->sum("total_words")}}
                                    </span>
                                </td>
                                <td data-label='{{translate("Category")}}'>
                                    {{@($template->category->title)}}
                                </td>
                                <td data-label='{{translate("Category")}}'>
                                    @php
                                       $subCategory = "-";
                                       if($template->subCategory && $template->subCategory->parent_id ==   $template->category?->id ){
                                           $subCategory = $template->subCategory->title ;
                                       }
                                    @endphp
                                    {{@$subCategory}}
                                </td>

                                <td data-label='{{translate("Status")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_ai_template') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.ai.template.update.status') }}"
                                            data-status="{{ $template->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$template->uid}}" {{$template->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$template->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$template->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Default")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_ai_template') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="is_default"
                                            data-route="{{ route('admin.ai.template.update.status') }}"
                                            data-status="{{ $template->is_default == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$template->uid}}" {{$template->is_default ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-default-{{$template->id}}" >
                                        <label class="form-check-label" for="status-switch-default-{{$template->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Options")}}'>
                                    <div class="table-action">
                                        @if(check_permission('update_ai_template') || check_permission('delete_ai_template') )
                                            @if(check_permission('update_ai_template') )
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Update')}}"  href="{{route('admin.ai.template.edit',$template->uid)}}"  class="update icon-btn warning"><i class="las la-pen"></i></a>
                                            @endif

                                            @if(check_permission('delete_ai_template') && $template->is_default == App\Enums\StatusEnum::false->status() )
                                                <a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Delete')}}"  href="javascript:void(0);" data-href="{{route('admin.ai.template.destroy',$template->uid)}}" class="pointer delete-item icon-btn danger">
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
                                <td class="border-bottom-0" colspan="9">
                                    @include('admin.partials.not_found',['custom_message' => "No Templates found!!"])
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="Paginations">
                {{ $templates->links() }}
            </div>
        </div>
    </div>

@endsection
@section('modal')
    @include('modal.delete_modal')
@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
        "use strict";
        $(".select2").select2({})
        $(".sub_category_id").select2({
            placeholder:"{{translate('Select Sub Category')}}",
        })
	})(jQuery);
</script>
@endpush





