@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.page.bulk")}}' method="post">
                        @csrf
                         <input type="hidden" name="bulk_id" id="bulkid">
                         <input type="hidden" name="value" id="value">
                         <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('create_page') || check_permission('update_page') || check_permission('delete_page'))
                        <div class="col-md-6 d-flex justify-content-start gap-2">
                            @if(check_permission('update_page') || check_permission('delete_page'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(check_permission('delete_page'))
                                            <li>
                                                <button data-type="delete"  class="dropdown-item bulk-action-modal">
                                                    {{translate("Delete")}}
                                                </button>
                                            </li>
                                        @endif

                                        @if(check_permission('update_page'))
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            @if(check_permission('create_page'))
                                <div class="action">
                                    <a href="{{route('admin.page.create')}}" class="i-btn btn--sm success">
                                        <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                    <input name="search" value="{{request()->search}}" type="search" placeholder="{{translate('Search by title')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route('admin.page.list')}}"  class="i-btn btn--sm danger">
                                    <i class="las la-sync"></i>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-container  position-relative">
                @include('admin.partials.loader')
                <table >
                    <thead>
                        <tr>
                            <th scope="col">
                                @if(check_permission('update_page') || check_permission('delete_page'))
                                    <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                @endif#
                            </th>
                            <th scope="col">
                                {{translate('Title')}}
                            </th>
                            <th scope="col">
                                {{translate('Url')}}
                            </th>
                            <th scope="col">
                                {{translate('Created By')}}
                            </th>
                            <th scope="col">
                                {{translate('Status')}}
                            </th>
                            <th scope="col">
                                {{translate('Header Presence')}}
                            </th>
                            <th scope="col">
                                {{translate('Footer Presence')}}
                            </th>
                            <th scope="col">
                                {{translate('Options')}}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $page)
                            <tr>
                                <td data-label="#">
                                    @if(check_permission('create_page') || check_permission('update_page') || check_permission('delete_page'))
                                        <input type="checkbox" value="{{$page->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$page->id}}" />
                                    @endif
                                    {{$loop->iteration}}
                                </td>
                                <td data-label='{{translate("Title")}}'>
                                    {{($page->title)}}
                                </td>
                                <td data-label='{{translate("Url")}}'>
                                    <a class="text-decoration-underline text--primary" target="_blank" href="{{route('page',$page->slug)}}">
                                       {{limit_words(route('page',$page->slug),20)}}
                                    </a>
                               </td>
                                <td data-label='{{translate("Created By")}}'>
                                    <span class="i-badge capsuled info">
                                        {{$page->createdBy->name}}
                                    </span>
                                </td>
                                <td data-label='{{translate("Status")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission("update_page") ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.page.update.status') }}"
                                            data-status="{{ $page->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$page->uid}}" {{$page->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-{{$page->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$page->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Header visibility")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission("update_page") ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="show_in_header"
                                            data-route="{{ route('admin.page.update.status') }}"
                                            data-status="{{ $page->show_in_header == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$page->uid}}" {{$page->show_in_header ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-header-{{$page->id}}" >
                                        <label class="form-check-label" for="status-switch-header-{{$page->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Footer visibility")}}'>
                                    <div class="form-check form-switch switch-center">
                                        <input {{!check_permission("update_page") ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                            data-column="show_in_footer"
                                            data-route="{{ route('admin.page.update.status') }}"
                                            data-status="{{ $page->show_in_footer == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                            data-id="{{$page->uid}}" {{$page->show_in_footer ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                        id="status-switch-footer-{{$page->id}}" >
                                        <label class="form-check-label" for="status-switch-footer-{{$page->id}}"></label>
                                    </div>
                                </td>
                                <td data-label='{{translate("Options")}}'>
                                    <div class="table-action">
                                        @if(check_permission('update_page') || check_permission('delete_page') )
                                            @if(check_permission('update_page'))
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}" href="{{route('admin.page.edit',$page->uid)}}"  class="update icon-btn warning"><i class="las la-pen"></i></a>
                                            @endif

                                            @if(check_permission('delete_page'))
                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);"    data-href="{{route('admin.page.destroy',$page->id)}}" class="pointer delete-item icon-btn danger">
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
                            <tr class="border-bottom-0">
                                <td class="border-bottom-0" colspan="8">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="Paginations">
                    {{ $pages->links() }}
            </div>
        </div>
    </div>
@endsection
@section('modal')

    @include('modal.delete_modal')
    @include('modal.bulk_modal')

@endsection







