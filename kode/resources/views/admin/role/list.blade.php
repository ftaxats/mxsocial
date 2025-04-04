@extends('admin.layouts.master')

@section('content')

	<div class="i-card-md">
		<div class="card-body">
			<div class="search-action-area">
                <div class="row g-3">
					<form hidden id="bulkActionForm" action='{{route("admin.role.bulk")}}' method="post">
                        @csrf
                         <input type="hidden" name="bulk_id" id="bulkid">
                         <input type="hidden" name="value" id="value">
                         <input type="hidden" name="type" id="type">
                    </form>


					@if(check_permission('create_role') || check_permission('update_role') || check_permission('delete_role'))
                        <div class="col-xl-7 col-lg-5 col-md-6 d-flex justify-content-start gap-2">
                            @if(check_permission('update_role') || check_permission('delete_role'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>
                                    <ul class="dropdown-menu">
										
                                        @if(check_permission('delete_role'))
                                            <li>
                                                <button data-type ="delete"  class="dropdown-item bulk-action-modal">
                                                    {{translate("Delete")}}
                                                </button>
                                            </li>
                                        @endif
                                    
                                        @if(check_permission('update_role'))
                                            @foreach(App\Enums\StatusEnum::toArray() as $k => $v)
                                                <li>
                                                    <button type="button"  name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            @if(check_permission('create_role'))
								<div class="action">
									<a href="{{route('admin.role.create')}}" class="i-btn btn--sm success">
										<i class="las la-plus me-1"></i>  {{translate('Add New')}}
									</a>
								</div>
                            @endif
						</div>
					@endif
                    <div class="col-xl-5 col-lg-7 col-md-6 d-flex justify-content-md-end justify-content-start">
                        <div class="search-area">
                            <form action="{{route(Route::currentRouteName())}}" method="get">
                                <div class="form-inner">
                                      <input name="search" value="{{request()->input('search')}}" type="search" placeholder="{{translate('Search by name or createdby or updatedby')}}">
                                </div>
                                <button class="i-btn btn--sm info">
                                    <i class="las la-sliders-h"></i>
                                </button>
                                <a href="{{route('admin.role.list')}}"  class="i-btn btn--sm danger">
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
								@if(check_permission('update_role') || check_permission('delete_role'))
									<input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
								@endif
								&nbsp;
								{{translate('Name')}}
							</th>

							<th scope="col" >
								{{translate('Status')}}
							</th>

							<th scope="col">
								{{translate('Created By')}}
							</th>
							

							<th scope="col">
								{{translate('Options')}}
							</th>
						</tr>
					</thead>
					<tbody>
						@forelse($roles as $role)
							<tr> 
								<td data-label='{{translate("name")}}'>
									@if( check_permission('update_role') || check_permission('delete_role'))
										<input type="checkbox" value="{{$role->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$role->id}}" />
									@endif
									&nbsp;
									{{$role->name}}
								</td>
								<td data-label='{{translate("Status")}}'>
									<div class="form-check form-switch switch-center">
										<input {{!check_permission('update_role') ? "disabled" :"" }} type="checkbox" class="status-update form-check-input"
											data-column="status"
											data-route="{{ route('admin.role.update.status') }}"
											data-status="{{ $role->status == App\Enums\StatusEnum::true->status() ?  App\Enums\StatusEnum::false->status() : App\Enums\StatusEnum::true->status()}}"
											data-id="{{$role->uid}}" {{$role->status ==  App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
										id="status-switch-{{$role->uid}}" >
										<label class="form-check-label" for="status-switch-{{$role->uid}}"></label>
									</div>
								</td>
								<td data-label='{{translate("Created By")}}'>
									<span class="i-badge capsuled info">
										{{$role->createdBy->username}}
									</span>
								</td>
							
								<td data-label='{{translate("Action")}}'>
									<div class="table-action">
										@if(check_permission('update_role') ||  check_permission('delete_role'))
											@if(check_permission('update_role'))
												<a  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Update')}}"  href="{{route('admin.role.edit',$role->uid)}}" class="fs-15 icon-btn info"><i class="las la-pen"></i></a>
											@endif
											@if(check_permission('delete_role'))
												<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}" href="javascript:void(0);" data-href="{{route('admin.role.destroy',$role->uid)}}" class="pointer delete-item icon-btn danger">
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
								<td class="border-bottom-0" colspan="6">
									@include('admin.partials.not_found',['custom_message' => "No Roles Found!!"])
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
			<div class="Paginations">
				{{ $roles->links() }}
			</div>
		</div>
	</div>

@endsection

@section('modal')
	@include('modal.delete_modal')
	@include('modal.bulk_modal')
@endsection







