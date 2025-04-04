@extends('admin.layouts.master')

@section('content')

	<div class="mb-20">
		<div class="profile-details-top">
			<div class="profile-info">
				<div class="image">
					<img src='{{imageURL(@$user->file,"profile,admin",true)}}' alt="{{@$user->file->name}}" />
				</div>
				<div class="designation">
					<a href="javascript: void(0);">
						<h4>{{@$user->name}}</h4>
					</a>
			     	<small>{{@$user->super_admin == App\Enums\StatusEnum::true->status() ? translate('SuperAdmin') : @$user->role->name }}</small>
				</div>
			</div>
		</div>
	</div>
	<div class="i-card-md">
		<div class="card--header">
			<h4 class="card-title">
				{{translate("Profile Info")}}
			</h4>
		</div>
		<div class="card-body">
			<ul class="nav nav-tabs style-1" role="tablist">
				<li class="nav-item " role="presentation">
					<a class="nav-link active" data-bs-toggle="tab" href="#profile-tab" aria-selected="false" role="tab" tabindex="-1">{{translate("Profile
						Info")}}</a>
				</li>
				<li class="nav-item" role="presentation">
					<a class="nav-link " data-bs-toggle="tab" href="#password-tab" aria-selected="true" role="tab">{{translate("Password")}}</a>
				</li>
			</ul>
			<div id="myTabContent" class="tab-content">
				<div class="tab-pane fade active show" id="profile-tab" role="tabpanel">
					<form action='{{route("admin.profile.update")}}' class="account-form" method="post" enctype="multipart/form-data">
						@csrf
						<div class="row">
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="username">{{translate("User Name")}}  <small class="text-danger">* </small>  </label>
									<input type="text"  placeholder="{{translate('Enter User Name')}}"
									id="username" name="username" value="{{$user->username}}" >
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="name">{{translate("Name")}}</label>
									<input type="text"
									placeholder="{{translate('Enter Name')}}"
									id="name" name="name" value="{{$user->name}}" >
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="email">
									{{translate('Email')}}
									    <small class="text-danger">* </small>
									</label>
									<input type="email" id="email" placeholder='{{translate("Enter your Email")}}'
									name="email"  value="{{$user->email}}"  >
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="phone">
									{{translate("Phone")}}  <small class="text-danger">   </small>
									</label>
									<input type="number" name="phone" value="{{$user->phone}}" placeholder="Phone" id="phone">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="image">
									   {{translate('Profile Image')}}
									</label>
									<input class="preview" data-size = "{{config('settings')['file_path']['profile']['admin']['size']}}" id="image" name="image" type="file">
									<div class="mt-2 image-preview-section">
                                    </div>
								</div>
							</div>
							<div class="col-lg-12">
								<button type="submit" class="i-btn btn--primary btn--lg">
									{{translate("Submit")}}
								</button>
							</div>
						</div>
					</form>
				</div>
				<div class="tab-pane fade " id="password-tab" role="tabpanel">
					<form action="{{route('admin.password.update')}}" class="account-form" method="post">
						@csrf
						<div class="row">
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="currentPassword">
										{{translate('Current Password')}} <small class="text-danger">*</small>
									</label>
									<input type="text"
									id="currentPassword" name="current_password" placeholder="{{translate('Enter Current Password')}}" >
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="password">
										{{translate('New Password')}} <small class="text-danger">*</small>
									</label>
									<input type="text" placeholder="{{translate('Enter New Password')}}"
									id="password" name="password">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-inner">
									<label for="password_confirmation">
										{{translate("Confirm Password")}} <small class="text-danger">*</small>
									</label>
									<input type="text" placeholder='{{translate("Confirm Password")}}'
									id="password_confirmation" name="password_confirmation" class="form-control">
								</div>
							</div>
							<div class="col-12">
								<button type="submit" class="i-btn btn--primary btn--lg">
									{{translate("Submit")}}
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

@endsection

