@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.menu.bulk")}}' method="post">
                        @csrf
                         <input type="hidden" name="bulk_id" id="bulkid">
                         <input type="hidden" name="value" id="value">
                         <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('create_menu') || check_permission('update_menu') )
                        <div class="col-md-6 d-flex justify-content-start gap-2">
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
                            @if(check_permission('create_menu'))
                                    <div class="action">
                                        <button type="button"   data-bs-toggle="modal" data-bs-target="#addMenu" class="i-btn btn--sm success">
                                            <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                        </button>
                                    </div>
                            @endif
                        </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                    <input name="search" value="{{request()->search}}" type="search" placeholder="{{translate('Search by name')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route('admin.menu.list')}}"  class="i-btn btn--sm danger">
                                    <i class="las la-sync"></i>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">
                                @if(check_permission('update_menu'))
                                    <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                @endif#
                            </th>
                            <th scope="col">
                                {{translate('Name')}}
                            </th>
                            <th scope="col">
                                {{translate('URL')}}
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
                        @forelse($menus as $menu)
                            <tr>
                                <td data-label="#">
                                    @if( check_permission('update_menu') || check_permission('delete_menu'))
                                        <input type="checkbox" value="{{$menu->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$menu->id}}" />
                                    @endif
                                    {{$loop->iteration}}
                                </td>
                                <td data-label='{{translate("Name")}}'>
                                    {{ ($menu->name)}}
                                </td>
                                <td data-label='{{translate("URL")}}'>
                                     <a class="text-decoration-underline text--primary" target="_blank" href="{{url($menu->url)}}">
                                        {{limit_words(url($menu->url),20)}}
                                     </a>
                                </td>
                                <td data-label='{{translate("Status")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_menu') || $menu->is_default == App\Enums\StatusEnum::true->status() ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.menu.update.status') }}"
                                            data-status="{{ $menu->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$menu->uid}}" {{$menu->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$menu->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$menu->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Action")}}'>
                                    <div class="table-action">
                                        @if(check_permission('update_menu') ||  check_permission('delete_menu'))
                                            @if(check_permission('update_menu'))


                                                <a data-id="{{$menu->id}}" data-meta_title="{{$menu->meta_title}}" data-meta_description="{{$menu->meta_description}}" data-meta_keywords="{{json_encode($menu->meta_keywords)}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('SEO Setting')}}" href="javascript:void(0)"  class="fs-15 icon-btn info seo-settings"><i class="las la-cog"></i></a>

                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}" href="{{route('admin.menu.edit',$menu->uid)}}"  class="fs-15 icon-btn warning"><i class="las la-pen"></i></a>
                                            @endif
                                            @if(check_permission('delete_menu') && $menu->is_default == App\Enums\StatusEnum::false->status())
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);"    data-href="{{route('admin.menu.destroy',$menu->id)}}" class="pointer delete-item icon-btn danger">
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
                                <td class="border-bottom-0" colspan="5">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="Paginations">
                {{ $menus->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="addMenu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addMenu" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >
                        {{translate('Add Menu')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.menu.store')}}" id="storeModalForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label class="form-label" for="name">
                                        {{translate('Name')}}
                                        <small class="text-danger">*</small>
                                    </label>
                                    <input type="text" name="name" id="name"  placeholder='{{translate("Enter name")}}'
                                        value='{{old("name")}}' required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="url">
                                        {{translate('URL')}} <small class="text-danger">*</small>
                                    </label>
                                    <input type="text" required name="url" id="url"  placeholder='{{translate("Enter URL")}}'
                                        value='{{old("url")}}'>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="serial_id">
                                        {{translate('Serial Id')}} <small class="text-danger">*</small>
                                    </label>
                                    <input type="number" min="0" required name="serial_id" id="serial_id"  placeholder='{{translate("Enter Serial Id")}}'
                                        value='{{old("serial_id") ? old("serial_id") :$serialId }}'>
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



    <div class="modal fade" id="seoSettings" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="seoSettings" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Update SEO Configuration')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.menu.seo.update')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <input type="hidden" name="id" id="menuId">
                        <div class="form-inner">
                            <label class="form-label" for="metaTitle">
                                {{translate('Meta Title')}}
                            </label>
                            <input type="text" name="meta_title" id="metaTitle"  placeholder='{{translate("Enter Title")}}'>
                        </div>
                        <div class="form-inner">
                            <label class="form-label" for="meta_description">
                                {{translate('Meta Description')}}
                            </label>
                            <textarea  placeholder='{{translate("Enter Description")}}' id="meta_description"  name="meta_description"  cols="30" rows="5"></textarea>
                        </div>
                        <div class="form-inner">
                            <label for="meta_keywords">
                                {{translate('Meta Keywords')}}
                            </label>

                            <select name="meta_keywords[]" multiple id="meta_keywords"  class="selectMeta" >

                            </select>
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


    @include('modal.delete_modal')
@endsection



@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";


            $(".selectMeta").select2({
                placeholder:"{{translate('Enter Keywords')}}",
                tags: true,
                tokenSeparators: [',']
	     	})


             $(document).on('click','.seo-settings',function(e){

                    e.preventDefault()
                    var modal = $('#seoSettings')

                    var id = $(this).attr('data-id');
                    var meta_title = $(this).attr('data-meta_title');
                    var meta_description = $(this).attr('data-meta_description');

                    var cleanContent = DOMPurify.sanitize(meta_description);
                    var meta_keywords = $(this).attr('data-meta_keywords');
                    var keywordsArray = meta_keywords ? JSON.parse(meta_keywords) : [];


                    var selectMeta = modal.find('select[name="meta_keywords[]"]');
                    selectMeta.empty();

                    if(keywordsArray  && keywordsArray.length > 0){

                        keywordsArray.forEach(function (keyword) {
                          selectMeta.append(new Option(keyword, keyword, true, true));
                        });

                    }

                    selectMeta.select2({
                        placeholder: "{{translate('Enter Keywords')}}",
                        tags: true,
                        tokenSeparators: [','],
                        dropdownParent: modal
                    });


                    modal.find('input[name="id"]').val(id)
                    modal.find('input[name="meta_title"]').val(meta_title)
                    modal.find('textarea[name="meta_description"]').html(cleanContent)



                    modal.modal('show')
              })
	})(jQuery);
</script>
@endpush





