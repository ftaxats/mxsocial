@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="i-card-md">
                <div class="card-header">
                    <h4 class="card-title">
                        {{translate(Arr::get($meta_data,'title'))}}
                    </h4>
                </div>

                @php
                    $custom_feild_counter = 0;
                    $custom_rules = [];
                    $kycFields =  json_decode(site_settings("kyc_settings"),true);
                @endphp


                <div class="card-body">

                    <form action="{{route('user.kyc.apply')}}" method="post" enctype="multipart/form-data">
                         @csrf
                        <div class="row">
                            @foreach($kycFields as $kycField)
                                @php
                                    if(isset($kycField['name']))           $field_name = $kycField['name'];
                                @endphp
                                <div class="col-lg-{{$kycField['type'] == 'textarea'  ? 12 :6}}">

                                        <div class="form-inner">
                                            <label for="{{$loop->index}}" class="form-label">
                                                {{$kycField['labels']}} @if($kycField['required'] == '1' || $kycField['type'] == 'file') <span class="text-danger">
                                                    {{$kycField['required'] == '1' ?  "*" :""}}

                                                </span>@endif
                                            </label>

                                            @if($kycField['type'] == 'textarea')
                                            <textarea id="{{$loop->index}}" {{$kycField['required'] == '1' ? "required" :""}} class="summernote"  name="kyc_data[{{ $field_name }}]" cols="30" rows="10" placeholder="{{$kycField['placeholder']}}">{{old('kyc_data.'.$field_name)}}</textarea>
                                            @elseif($kycField['type'] == 'file')
                                                <input id="{{$loop->index}}"  {{$kycField['required'] == '1' ? "required" :""}}     type="file" name="kyc_data[files][{{ $field_name }}]" >
                                            @else
                                                <input id="{{$loop->index}}" {{$kycField['required'] == '1' ? "required" :""}} type="{{$kycField['type']}}"   name="kyc_data[{{ $field_name }}]" value="{{old('kyc_data.'.$field_name)}}"  placeholder="{{$kycField['placeholder']}}">
                                            @endif
                                        </div>
                                </div>
                            @endforeach

                            <div class="col-12">
                                <button type="submit" class="i-btn btn--md btn--primary capsuled" data-anim="ripple">
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





