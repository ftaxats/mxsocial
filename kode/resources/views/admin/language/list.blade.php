@extends('admin.layouts.master')
@section('content')
	<div class="i-card-md">
		<div class="card-body">
            <div class="search-action-area">
                <div class="row g-3">
					<form hidden id="bulkActionForm" action='{{route("admin.language.bulk")}}' method="post">
						@csrf
						<input type="hidden" name="bulk_id" id="bulkid">
						<input type="hidden" name="value" id="value">
						<input type="hidden" name="type" id="type">
					</form>
                    @if(check_permission('create_language') || check_permission('update_language') )
                        <div class="col-md-6 d-flex justify-content-start gap-2">
                            @if(check_permission('update_language') || check_permission('delete_language'))
                                <div class="i-dropdown bulk-action mx-0 d-none">
                                    <button class="dropdown-toggle bulk-danger" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-cogs fs-15"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(check_permission('update_language'))
											@foreach(App\Enums\StatusEnum::toArray() as $k => $v)
												<li>
													<button type="button" name="bulk_status" data-type ="status" value="{{$v}}" class="dropdown-item bulk-action-btn" > {{translate($k)}}</button>
												</li>
											@endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif
							@if(check_permission('create_language'))
								<div class="action">
									<button type="button" data-bs-toggle="modal" data-bs-target="#addLanguage" class="i-btn btn--sm success">
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
									<input name="search" value="{{request()->input('search')}}" type="search" placeholder="{{translate('Search by name or code')}}">
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
			<div class="table-container">
				<table>
					<thead>
						<tr>
							<th scope="col">
								@if(check_permission('update_language'))
									<input class="check-all  form-check-input me-1" id="checkAll" type="checkbox">
								@endif#
							</th>
							<th scope="col">
								{{translate('Language')}}
							</th>
							<th scope="col">
								{{translate('Code')}}
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
						@forelse ( $languages as $language)
							    <tr>
									<td data-label="#">
										@if(check_permission('create_language') || check_permission('update_language') || check_permission('delete_language'))
											<input type="checkbox" value="{{$language->id}}" name="ids[]" class="data-checkbox form-check-input" id="{{$language->id}}" />
										@endif
										{{$loop->iteration}}
									</td>
									<td data-label='{{translate("Language")}}'>
										<div class="user-meta-info d-flex align-items-center gap-2">
											<img class="rounded-circle avatar-sm" src="{{asset('assets/images/global/flags/'.strtoupper($language->code ).'.png') }}" alt="{{$language->code.'.jpg'}}">
											<p>{{$language->name}} </p>

											@if($language->is_default == App\Enums\StatusEnum::true->status())
												<span class="i-badge capsuled success">
													<i class="las la-star"></i>  {{translate('Default')}}
												</span>
											@endif
										</div>
									</td>

									<td data-label='{{translate("Code")}}'>
										{{$language->code}}
									</td>
									<td data-label='{{translate("Status")}}'>
										<div class="form-check form-switch switch-center">
											<input {{!check_permission('update_language') ? "disabled" :"" }}   type="checkbox" class="status-update form-check-input"
												data-column="status"
												data-route="{{ route('admin.language.update.status') }}"
												data-model="Language"
												data-status="{{ $language->status == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status():App\Enums\StatusEnum::true->status()}}"
												data-id="{{$language->uid}}" {{$language->status == App\Enums\StatusEnum::true->status() ? 'checked' : ''}}
											id="status-switch-{{$language->id}}" >
											<label class="form-check-label" for="status-switch-{{$language->id}}"></label>
										</div>
									</td>

									<td data-label='{{translate("Options")}}'>
										<div class="table-action">
											@if(check_permission('update_language') ||  check_permission('translate_language') || check_permission('delete_language') )
												@if(check_permission('update_language') && $language->is_default != App\Enums\StatusEnum::true->status())
													<a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Make Default')}}" href="{{route('admin.language.make.default',$language->uid)}}" class="icon-btn info">
														<i class="las la-star"></i>
													</a>
												@endif
												@if(check_permission('translate_language'))
													<a data-bs-toggle="tooltip" data-bs-placement="top"    data-bs-title="{{translate('Translate')}}" href="{{route('admin.language.translate',$language->code)}}" class="pointer icon-btn success"><i class="las la-language"></i></a>
												@endif
												@if(check_permission('delete_language') && $language->code !='en' && $language->is_default != App\Enums\StatusEnum::true->status() && session()->get('locale') != $language->code   )
														<a data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-title="{{translate('Delete')}}" href="javascript:void(0);" data-href="{{route('admin.language.destroy',$language->uid)}}" class="pointer delete-item icon-btn danger ">
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
				{{ $languages->links() }}
			</div>
		</div>
	</div>
@endsection

@section('modal')
<div class="modal fade modal-md" id="addLanguage" tabindex="-1" aria-labelledby="addLanguage" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		    <div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" >
						{{translate('Add New Language')}}
					</h5>
					<button class="close-btn" data-bs-dismiss="modal">
						<i class="las la-times"></i>
					</button>
				</div>
				<form action="{{route('admin.language.store')}}" method="post" class="add-listing-form">
					@csrf
					<div class="modal-body">
						<div class="form-inner">
							<label for="name">{{translate('Name')}}  <small class="text-danger" >*</small></label>
							<select required class="select2" name="name" id="name" >
								@foreach ($countryCodes as $codes)
									<option value="{{$codes['name']}}//{{$codes['isoAlpha2']}}">
									{{$codes['name']}}
									</option>
							    @endforeach
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
@include('modal.bulk_modal')

@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";
		$(".select2").select2({
			placeholder:"{{translate('Select Country')}}",
			dropdownParent: $("#addLanguage"),
		})
	})(jQuery);
</script>
@endpush

