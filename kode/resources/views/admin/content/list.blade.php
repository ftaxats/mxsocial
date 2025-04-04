@extends('admin.layouts.master')
@section('content')
     @if(check_permission('create_content'))
        <div class="i-card-md">
            <div class="card-body">
                @include('partials.prompt_content',['content_route' => route("admin.content.store")])
            </div>
        </div>
    @endif

    <div class="i-card-md mt-4">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.content.bulk")}}' method="post">
                        @csrf
                        <input type="hidden" name="bulk_id" id="bulkid">
                        <input type="hidden" name="value" id="value">
                        <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('create_content') || check_permission('update_content') || check_permission('delete_content') )
                        <div class="col-md-6 d-flex justify-content-start gap-2">
                            @if(check_permission('update_content'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(check_permission('delete_content'))
                                            <li>
                                                <button data-type="delete"  class="dropdown-item bulk-action-modal">
                                                    {{translate("Delete")}}
                                                </button>
                                            </li>
                                        @endif
                                        @if(check_permission('update_content'))
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                    <input name="search" value="{{request()->input('search')}}" type="search" placeholder="{{translate('Search by name ')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route('admin.content.list')}}"  class="i-btn btn--sm danger">
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
                            @if(check_permission('update_content') || check_permission('delete_content'))
                                <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                            @endif#
                        </th>
                        <th scope="col">{{translate('Name')}}</th>
                        <th scope="col">{{translate('Status')}}</th>
                        <th scope="col">{{translate('Action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse ($contents as $content)
                            <tr>
                                <td data-label="#">
                                    @if( check_permission('update_content') || check_permission('delete_content'))

                                           <input  type="checkbox" value="{{$content->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$content->id}}" />

                                    @endif
                                    {{$loop->iteration}}
                                </td>
                                <td data-label='{{translate("name")}}'>
                                    {{$content->name}}
                                </td>
                                <td data-label='{{translate("Status")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission('update_content') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.content.update.status') }}"
                                            data-status="{{ $content->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$content->uid}}" {{$content->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$content->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$content->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Action")}}'>
                                    <div class="table-action">
                                        @if(check_permission('update_content') || check_permission('delete_content') )
                                            @if(check_permission('update_content'))
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}" href="javascript:void(0);" data-content ="{{$content}}" class="update fs-15 icon-btn warning"><i class="las la-pen"></i></a>
                                            @endif

                                            @if(check_permission('delete_content'))
                                                <a  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);"    data-href="{{route('admin.content.destroy',$content->id)}}" class="pointer delete-item icon-btn danger">
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
                                <td class="border-bottom-0" colspan="4">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="Paginations">
                {{ $contents->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modal.delete_modal')
    @include('modal.bulk_modal')

    <div class="modal fade" id="content-form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="content-form"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Add Content')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{route('admin.content.store')}}" id="contentForm" method="post" enctype="multipart/form-data">
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
                                    <label for="input-content" class="form-label" >
                                        {{translate('Content')}} <small class="text-danger">*</small>
                                    </label>
                                    <textarea placeholder='{{translate("Type Here...")}}' name="content" id="input-content" cols="30" rows="10"></textarea>
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

@push('script-include')
    @include('partials.ai_content_script');
@endpush

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";

        $(".select2").select2({
            placeholder:"{{translate('Select Item')}}",
        })

        $(".selectTemplate").select2({
            placeholder:"{{translate('Select Template')}}",
        })
        $(".sub_category_id").select2({
            placeholder:"{{translate('Select Sub Category')}}",
        })
        $(document).on('click','.create',function(e){
            e.preventDefault()
            $('.ai-section').fadeToggle(1000).toggleClass('d-none');;
        });
	})(jQuery);
</script>
@endpush





