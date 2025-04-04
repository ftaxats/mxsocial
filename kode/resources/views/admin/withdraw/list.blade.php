@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
                    <form hidden id="bulkActionForm" action='{{route("admin.withdraw.bulk")}}' method="post">
                        @csrf
                            <input type="hidden" name="bulk_id" id="bulkid">
                            <input type="hidden" name="value" id="value">
                            <input type="hidden" name="type" id="type">
                    </form>
                    @if(check_permission('create_withdraw') || check_permission('update_withdraw') )
                        <div class="col-md-6 d-flex justify-content-start gap-2">
                            @if(check_permission('update_withdraw'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(check_permission('update_withdraw'))
                                        
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif
                            @if(check_permission('create_withdraw'))
                                <div class="action">
                                    <a href="{{route('admin.withdraw.create')}}" class="i-btn btn--sm success create">
                                        <i class="las la-plus me-1"></i>  {{translate('Add New')}}
                                    </a>
                                </div>
                            @endif
                            @if(check_permission('update_withdraw'))
                                <button type="button" data-bs-toggle="modal" data-bs-target="#withdraw-config" class="i-btn btn--sm danger create">
                                    <i class="las la-cogs me-1"></i>  {{translate('Configuration')}}
                                </button>
                            @endif
                        </div>
                    @endif
                    <div class="col-md-6 d-flex justify-content-end">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                    <input name="search" value='{{request()->input("search")}}' type="search" placeholder="{{translate('Search by name')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route('admin.withdraw.list')}}"  class="i-btn btn--sm danger">
                                    <i class="las la-sync"></i>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-container position-relative">
                @include('admin.partials.loader')
                
                @if($withdraws->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">
                                    @if(check_permission('update_withdraw'))
                                        <input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
                                    @endif#
                                </th>
                                <th scope="col">{{translate('Name')}}</th>
                                <th scope="col">{{translate('Charge')}}</th>
                                <th scope="col">{{translate('Limit')}}</th>
                                <th scope="col">{{translate('Status')}}</th>
                                <th scope="col">{{translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($withdraws as $withdraw)
                                <tr>
                                    <td data-label="#">
                                        @if(check_permission('update_withdraw'))
                                            <input type="checkbox" value="{{$withdraw->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$withdraw->id}}" />
                                        @endif
                                        {{$loop->iteration}}
                                    </td>
                                    <td data-label='{{translate("Name")}}'>

                                        <div class="user-meta-info d-flex align-items-center gap-2">

                                            <img class="rounded-circle avatar-sm" src="{{imageURL(@$withdraw->file,'withdraw_method',true)}}" alt="{{@$withdraw->file->name}}">

                                            <p>	 {{$withdraw->name}}</p>
                                        </div>
                                    </td>
                                    <td data-label="{{translate('Charge')}}">
                                        {{num_format($withdraw->fixed_charge,base_currency())}} + {{$withdraw->percent_charge}} %
                                    </td>
                                    <td data-label="{{translate('Limit')}}">{{truncate_price($withdraw->minimum_amount)}} - {{truncate_price($withdraw->maximum_amount)}} {{base_currency()->code}}  </td>
                                    <td data-label='{{translate("Status")}}'>
                                        <div class="form-check form-switch switch-center">
                                            <input {{!check_permission('update_withdraw') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
                                                data-column="status"
                                                data-route="{{ route('admin.withdraw.update.status') }}"
                                                data-status="{{ $withdraw->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
                                                data-id="{{$withdraw->uid}}" {{$withdraw->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
                                            id="status-switch-{{$withdraw->id}}" >
                                            <label class="form-check-label" for="status-switch-{{$withdraw->id}}"></label>
                                        </div>
                                    </td>
                                    <td data-label="{{translate('Action')}}">
                                        <div class="table-action">
                                            @if(check_permission('update_withdraw') || check_permission('delete_withdraw') )
                                                @if(check_permission('update_withdraw'))
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}" href="{{route('admin.withdraw.edit',$withdraw->uid)}}"  class="update icon-btn warning"><i class="las la-pen"></i></a>
                                                @endif
                                                @if(check_permission('delete_withdraw'))
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);" data-href="{{route('admin.withdraw.destroy',$withdraw->uid)}}" class="pointer delete-item icon-btn danger">
                                                        <i class="las la-trash-alt"></i></a>
                                                @endif
                                            @else
                                                --
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                               
                            @endforelse
                        </tbody>
                    </table>
                @else

                    @include('admin.partials.not_found',['custom_message'=>'No methods found'])

                @endif
            </div>
            <div class="Paginations">
                {{ $withdraws->links() }}
            </div>
        </div>
    </div>
@endsection
@section('modal')

    @include('modal.delete_modal')

    <div class="modal fade" id="withdraw-config" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="withdraw-config"   aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{translate('Set withdraw Config')}}
                    </h5>
                    <button class="close-btn" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.withdraw.configuration')}}" id="withdrawForm" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="max_pending_withdraw" class="form-label" >
                                        {{translate('Max pending withdraw')}} <small class="text-danger">*</small>
                                    </label>

                                    <input id="max_pending_withdraw" placeholder="{{translate('Enter number')}}" type="number" name="site_settings[max_pending_withdraw]" value="{{site_settings('max_pending_withdraw')}}">
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






