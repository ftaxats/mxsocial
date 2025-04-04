@extends('admin.layouts.master')
@push('style-include')
  <link  nonce="{{ csp_nonce() }}" rel="stylesheet" href="{{asset('assets/global/css/summnernote.css')}}">
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="i-card-md">
                <div class="card--header">
                    <h4 class="card-title">
                        {{translate('Edit Template')}}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.template.update')}}" class="add-listing-form" enctype="multipart/form-data" method="post">
                        @csrf                
                        <div class="form-inner">
                            <label for="name">
                                {{translate('Name')}} <small class="text-danger">*</small>
                            </label>              
                            <input required type="text"  id="name" name="name" value="{{$template->name}}" >
                            <input hidden type="text" id="id" name="id" value="{{$template->id}}" >
                        </div> 
                        <div class="form-inner">
                            <label for="subject">
                                {{translate('Subject')}} <small class="text-danger">*</small>
                            </label>               
                            <input required type="text"  id="subject" name="subject" value="{{$template->subject}}"  >
                        </div> 
                        <div class="form-inner">
                            <label for="body">
                                {{translate('Email Body')}} <small class="text-danger">*</small>
                            </label>              
                            <textarea class="summernote" name="body" id="body" cols="30" rows="10">@php echo ($template->body) @endphp</textarea>
                        </div> 
                        <div class="form-inner">
                            <label for="smsBody">
                                {{translate('SMS Body')}} <small class="text-danger">*</small>
                            </label>
                            <textarea  name="sms_body" id="smsBody" cols="30" placeholder='{{translate("Type Here ...")}}' rows="10">@php echo ($template->sms_body) @endphp</textarea>
                        </div> 
                        <div>
                            <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                                {{translate("Submit")}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="i-card-md">
                <div class="card--header">
                    <h4 class="card-title">
                        {{translate('Template Key')}}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="text-center d-flex gap-2 flex-column">
                        @foreach(($template->template_key) as $key => $value)
                            <div class="d-flex  align-items-center justify-content-between">
                                <div class="me-2 ">
                                    <p>{{ucfirst($value)}}</p>
                                </div>
                                <p class="mb-0">@php echo ("{{". $key ."}}")  @endphp</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-include')
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/summernote.min.js')}}"></script>
    <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/editor.init.js')}}"></script>
@endpush





