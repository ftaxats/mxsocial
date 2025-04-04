@extends('admin.layouts.master')

@push('style-include')
    <link nonce="{{ csp_nonce() }}"   rel="stylesheet" href="{{asset('assets/global/css/summnernote.css')}}">
@endpush
@section('content')

    @php
        $custom_feild_counter = 0;
        $custom_rules = [];
        $ticket_fields =  json_decode(site_settings("ticket_settings"),true);
    @endphp

   <form action="{{route('admin.ticket.store')}}" class="add-listing-form" enctype="multipart/form-data" method="post">
        @csrf
        <div class="row g-4">        
            <div class="col-xl-12">
                <div class="i-card-md">
                    <div class="card--header">
                        <h4 class="card-title">
                            {{translate('Basic Information')}}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="user" class="form-label">
                                        {{translate("User")}} <span class="text-danger">*</span>
                                    </label>
                                    <select name="user_id" class="select-user" id="user">
                                        <option value="">
                                            {{translate("Select User")}}
                                        </option>
                                        @foreach(system_users() as $user)
                                            <option  {{Arr::get($user,"id",null) ==   request()->input('user_id') ? 'selected' :""}} value="{{Arr::get($user,"id",null)}}"> {{Arr::get($user,"name",null)}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @foreach($ticket_fields as $ticket_field)
                                @php
                                   if(isset($ticket_field['name']))
                                    {
                                        $field_name = $ticket_field['name'];
                                    }
                                @endphp
                                <div class="col-lg-{{$ticket_field['type'] == 'textarea'   ? 12 :6}}">
                                    <div class="form-inner">
                                        <label for="{{$loop->index}}" class="form-label">
                                            {{$ticket_field['labels']}} @if($ticket_field['required'] == '1' || $ticket_field['type'] == 'file') <span class="text-danger">
                                                {{$ticket_field['required'] == '1' ?  "*" :""}}

                                                    @if($ticket_field['type'] == 'file')
                                                    ({{$ticket_field['placeholder']}} !! {{translate('Max-'). site_settings("max_file_upload")}}  )
                                                @endif
                                            </span>@endif
                                        </label>
                                        @if($ticket_field['type'] == 'textarea')
                                        <textarea id="{{$loop->index}}" {{$ticket_field['required'] == '1' ? "required" :""}} class="summernote"  name="ticket_data[{{ $field_name }}]" cols="30" rows="10" placeholder="{{$ticket_field['placeholder']}}">{{old('ticket_data.'.$field_name)}}</textarea>
                                        @elseif($ticket_field['type'] == 'file')
                                            <input id="{{$loop->index}}"  {{$ticket_field['required'] == '1' ? "required" :""}}   multiple  type="file" name="ticket_data[{{ $field_name }}][]" >
                                        @else
                                            <input id="{{$loop->index}}" {{$ticket_field['required'] == '1' ? "required" :""}} type="{{$ticket_field['type']}}"   name="ticket_data[{{ $field_name }}]" value="{{old('ticket_data.'.$field_name)}}"  placeholder="{{$ticket_field['placeholder']}}">
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-lg-6">
                                <div class="form-inner">
                                  <label for="priority" class="form-label">
                                      {{translate("Priority")}} <span class="text-danger">*</span>
                                  </label>
                                  <select name="priority" class="selec2" id="priority">
                                    <option value="">
                                        {{translate("Select Priority")}}
                                    </option>
                                      @foreach(App\Enums\PriorityStatus::toArray() as $k => $v)
                                          <option {{old('priority')  ==  $v ? "selected" :""}} value="{{$v}}">
                                              {{ucfirst(t2k($k))}}
                                          </option>
                                      @endforeach
                                  </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                                    {{translate("Submit")}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </form>
@endsection

@push('script-include')
  <script   nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/summernote.min.js')}}"></script>
  <script nonce="{{ csp_nonce() }}" src="{{asset('assets/global/js/editor.init.js')}}"></script>
@endpush


@push('script-push')


<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";

        
        $(".select-user").select2({})
        $(".selec2").select2({
            placeholder:"{{translate('Select Priority')}}",

        })
	})(jQuery);
</script>
@endpush
