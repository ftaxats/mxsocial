@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.paymentMethod.bulk")}}' method="post">
                        @csrf
                        <input type="hidden" name="bulk_id" id="bulkid">
                        <input type="hidden" name="value" id="value">
                        <input type="hidden" name="type" id="type">
                    </form>
                    @if( check_permission('update_method') || check_permission('create_method') )
                    <div class="col-md-6 d-flex justify-content-start gap-2">
                        @if(check_permission('update_method'))
                            <div class="i-dropdown bulk-action mx-0 d-none">
                                <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="las la-cogs fs-15"></i>
                                </button>
                                  <ul class="dropdown-menu">
                                        @if(check_permission('update_method'))
                                        
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                            
                                        @endif
                                  </ul>
                            </div>
                         @endif
                   
                        @if(check_permission('create_method') && request()->route('type') == 'manual')
                            <a href="{{route('admin.paymentMethod.create','manual')}}"  class="i-btn btn--sm success create">
                                <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                            </a>
                        @endif
                    </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName(),request()->route('type'))}}" method="get">
                                <div class="form-inner">
                                    <input name="search" value="{{request()->input('search')}}" type="search" placeholder="{{translate('Search by name')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route(Route::currentRouteName(),request()->route('type'))}}"  class="i-btn btn--sm danger">
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
                                @if(check_permission('update_method'))
                                    <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                @endif#
                            </th>
                            <th scope="col">
                                {{translate('Name')}}
                            </th>
                            <th scope="col">{{translate('Charge')}}</th>
                            <th scope="col">{{translate('Limit')}}</th>
                            <th scope="col">
                                {{translate('Updated By')}}
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
                        @forelse($methods as $method)
                        <tr>
                            <td data-label="#">
                                @if(check_permission('create_method') || check_permission('update_method') || check_permission('delete_method'))
                                    <input type="checkbox" value="{{$method->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$method->id}}" />
                                @endif
                                {{$loop->iteration}}
                            </td>
                            <td data-label='{{translate("Name")}}'>
                                <div class="user-meta-info d-flex align-items-center gap-2">
                                    <img class="rounded-circle avatar-sm" src='{{imageURL(@$method->file,"payment_method",true)}}' alt="{{@$method->file->name}}">
                                    <p>	 {{$method->name}}</p>
                                </div>
                            </td>
                            <td data-label="{{translate('Charge')}}">
                                {{num_format($method->fixed_charge,base_currency())}} + {{$method->percentage_charge}} %
                            </td>
                            <td data-label="{{translate('Limit')}}">{{truncate_price($method->minimum_amount,0)}} - {{truncate_price($method->maximum_amount,0)}} {{$method->currency->code}}  </td>
                            <td data-label='{{translate("Updated By")}}'>
                                <span class="i-badge capsuled info">
                                    {{$method->updatedBy->name}}
                                </span>
                            </td>
                            <td data-label='{{translate("Status")}}'>
                                <div class="form-check form-switch switch-center">
                                    <input {{!check_permission('update_method') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                        data-column="status"
                                        data-route="{{ route('admin.paymentMethod.update.status') }}"
                                        data-status="{{ $method->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                        data-id="{{$method->uid}}" {{$method->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                    id="status-switch-{{$method->id}}" >
                                    <label class="form-check-label" for="status-switch-{{$method->id}}"></label>
                                </div>
                            </td>
                            <td data-label='{{translate("Options")}}'>
                                <div class="table-action">

                                    @if(check_permission('update_method') || check_permission('delete_method'))

                                      @if(check_permission('update_method'))

                                        <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}" href="{{route('admin.paymentMethod.edit',['uid' => $method->uid , 'type' => request()->route('type')])}}" class="icon-btn warning"><i class="las la-pen"></i></a>
                                      @endif
                                      @if(check_permission('delete_method') && request()->route('type') == 'manual')
                                         <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);" data-href="{{route('admin.paymentMethod.destroy',$method->id)}}" class="pointer delete-item icon-btn danger">
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
                                <td class="border-bottom-0" colspan="7">
                                    @include('admin.partials.not_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="Paginations">
                {{ $methods->links() }}
            </div>
        </div>
    </div>
  
@endsection

@section('modal')
    @include('modal.delete_modal')
    @include('modal.bulk_modal')
@endsection









