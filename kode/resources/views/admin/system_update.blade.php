@extends('admin.layouts.master')
@section('content')

	<div class="container-fluid px-0">

        <div class="i-card-md mt-3">
            <div class="card--header">
                <h4 class="card-title">
                     {{trans('default.system_update_title')}}
                </h4>
            </div>
            <div class="card-body">
                <ul class="update-list">
                     @php  echo (trans('default.update_note')) @endphp
                </ul>
            </div>
        </div>
        <div class="i-card-md mt-3">
            <div class="card--header">
                <h4 class="card-title">
                      {{translate("Update Application")}}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{route('admin.system.update')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="d-flex align-items-center flex-md-nowrap flex-wrap gap-3 mb-4">
                                <div class="version">
                                    <span>
                                        {{translate("Current Version")}}
                                    </span>
                                    <h4>{{translate('V')}}{{site_settings("app_version",1.1)}}</h4>
                                    <p>
                                        {{get_date_time(site_settings("system_installed_at",\Carbon\Carbon::now()))}}
                                    </p>
                                </div>

                            </div>
                            <div class="mt-4 mb-4">
                                <label  for="image" class="feedback-file">
                                    <input name="updateFile" hidden accept=".zip" type="file" id="image" >
                                    <span><i class="bi bi-file-zip"></i>
                                        {{translate("Upload Zip file")}}
                                    </span>
                                </label>
                            
                            </div>
                            <button class="i-btn btn--lg btn--primary update-btn" type="submit">
                                {{translate("Update Now")}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
	</div>

@endsection
