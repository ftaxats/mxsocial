@extends('admin.layouts.master')
@section('content')
	<div class="i-card-md">
		<div class="card-body">
			<form action="{{route('admin.mailGateway.update')}}" class="add-listing-form" enctype="multipart/form-data" method="post">
				@csrf
				<input type="hidden" name="id" value="{{$gateway->id}}" >
				<div class="row">					
					@foreach ($gateway->credential as $k=>$v)
						@if($k != "from")
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="{{ $k }}">{{ ucfirst(str_replace('_',' ', $k)) }}
										<small class="text-danger">*</small>
									</label>
									@if($k == 'encryption')
										<select class="select2" name="credential[{{ $k }}]" id="{{ $k }}">
											<option {{$v ==  "SSL" ? "selected" :""}} value="SSL">  
												{{translate("Secure encryption (SSL)")}}
											</option>
											<option {{$v ==  "TLS" ? "selected" :""}}  value="TLS">  
												{{translate("Standard encryption (TLS)")}}
											</option>
										</select>
									@else
									<input type="text" name="credential[{{ $k }}]" value='{{ is_demo() ? "@@@" :$v}}'
									id="{{ $k }}">
									@endif
								</div>								
							</div> 
						@else
						@foreach($v  as $subKey => $subVal)
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="{{ $subKey }}">{{ k2t($subKey) }}
										<small class="text-danger">*</small>
									</label>

									<input type="text" name="credential[{{ $k }}][{{ $subKey }}]" value='{{ is_demo() ? "@@@" : $subVal }}'
									id="{{ $subKey }}">
								</div>
							</div>
						@endforeach
						@endif
					@endforeach
					<div class="col-12">
						<div class="d-flex align-items-center gap-3">
							<button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
								{{translate("Submit")}}
							</button>
							<a href="javascript:void(0)"   data-bs-toggle="modal" data-bs-target="#test" class="i-btn btn--info btn--md">
								{{translate("Test")}}
							</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('modal')
	<div class="modal fade" id="test" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="test" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" >
						{{translate('Test Gateway')}}
					</h5>
					<button class="close-btn" data-bs-dismiss="modal">
						<i class="las la-times"></i>
					</button>
				</div>
				<form action="{{route('admin.mailGateway.test')}}"  method="post" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<input type="hidden" name="id" value="{{$gateway->id}}" id="uid" class="form-control" >							
						<div class="form-inner">
							<label class="form-label" for="email">
								{{translate('Email')}} <small class="text-danger" >*</small>
									
							</label>
							<input required type="email" name="email" id="email" placeholder='{{translate("Enter Email")}}'>
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



