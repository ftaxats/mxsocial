@extends('admin.layouts.master')
@section('content')
	<div class="i-card-md">
		<div class="card-body">
			<form action="{{route('admin.smsGateway.update')}}" class="add-listing-form" enctype="multipart/form-data" method="post">
				@csrf
				<input type="hidden" name="id" value="{{$gateway->id}}" >
				<div class="row">
					@foreach ($gateway->credential as $k=>$v)
						<div class="col-lg-6">
							<div class="form-inner">
								<label for="{{ $k }}">{{ k2t($k) }}
									<small class="text-danger">*</small>
								</label>

								<input type="text" name="credential[{{ $k }}]" value="{{is_demo() ? '@@@' :$v}}"
								id="{{ $k }}">
							</div>
						</div>
					@endforeach
					<div class="col-12">
						<button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
							{{translate("Submit")}}
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection


