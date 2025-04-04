@extends('admin.layouts.master')

@section('content')

    @php
        $parameters     = ($method->parameters);
        $extraPrameters = ($method->extra_parameters);
    @endphp

    <div class="i-card-md">
        <div class="card-body">
            <form action="{{route('admin.paymentMethod.update',request()->route('type'))}}" class="add-listing-form" enctype="multipart/form-data" method="post">
                @csrf
                <input type="hidden" name="id" value="{{$method->id}}" >
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="name">
                                {{translate('Name')}} <small class="text-danger">*</small>
                            </label>                           
                            <input required type="text"  placeholder="{{translate('Enter name')}}" id="name" name="name" value="{{$method->name}}">                      
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="serialId">
                                {{translate('Serial Id')}} <small class="text-danger">*</small>
                            </label>                  
                            <input required type="number" min="0" id="serialId" name="serial_id" value="{{$method->serial_id}}" >
                        </div>
                    </div> 
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="currency">
                                {{translate('Currency')}} <small class="text-danger">*</small>
                            </label>
                            <select required class="select2 form-select currency-change" id="currency" name="currency_id" >
                                <option value="">
                                    {{translate("Select Currency")}}
                                </option>
                                @foreach($currencies as $currency)
                                    <option data-rate ="{{exchange_rate($currency,4)}}" {{$method->currency_id ==  $currency->id ? "selected" :""}} value="{{$currency->id}}">
                                         {{$currency->code}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div> 
                    <div class="col-lg-6">                           
                        <label for="convertion_rate">
                            {{translate('Convertion Rate')}} <small class="text-danger">*</small>
                        </label>
                        <div class="input-group mb-3">
                            <span class="input-group-text">  1 {{session()->get('currency')?->code}} =</span>
                            <input disabled id="convertion_rate" type="number" min="0" step="any"  class="form-control"
                            name="convention_rate"
                            value="{{exchange_rate($method->currency,4)}}"
                            required="">
                            <span class="input-group-text set-currency"></span>
                        </div>
                    </div> 
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="percentage_charge">
                                {{translate('Percentage Charge
                                ')}} <small class="text-danger">*</small>
                            </label>
                            <input required type="number" min="0" step="any"  id="percentage_charge" name="percentage_charge" value="{{round($method->percentage_charge, 2)}}"  >
                        </div>
                    </div> 
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="fixed_charge">
                                {{translate('Fixed Charge
                                ')}} <small class="text-danger">*</small>
                            </label>
                            <input required type="number" min="0" step="any"  id="fixed_charge" name="fixed_charge" value="{{round($method->fixed_charge, 2)}}" >                     
                        </div>
                    </div> 
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="minimum_amount" class="form-label">
                                {{translate('Minimum Amount')}} <small class="text-danger">*  </small>
                            </label>
                            <input required type="number" step="any"  placeholder="{{translate('Enter Minimum Amount')}}" id="minimum_amount" name="minimum_amount" value="{{$method->minimum_amount}}" >
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="maximum_amount" class="form-label">
                                {{translate('Maximum Amount')}} <small class="text-danger">* </small>
                            </label>
                            <input required type="number" step="any"  placeholder="{{translate('Enter Maximum Amount')}}" id="maximum_amount" name="maximum_amount" value="{{$method->maximum_amount}}" >
                        </div>
                    </div>
                    @if(request()->route('type')  == 'automatic')
                        @foreach ($parameters as $key => $parameter)
                            <div class="col-lg-6">
                                <div class="form-inner">
                                    <label for="{{ $key }}">{{ ucfirst(str_replace('_',' ', $key)) }}
                                        <small class="text-danger">*</small>
                                    </label>

                                    @if($key != 'sandbox')
                                        <input type="text" name="parameter[{{ $key }}]" value='{{ is_demo() ? "@@@": old($key, $parameter) }}'
                                        id="{{ $key }}">
                                    @else
                                        <select name="parameter[{{ $key }}]" class="select2" id="{{ $key }}">

                                            @foreach(App\Enums\StatusEnum::toArray() as $status=>$value)
                                                <option {{$parameter == $value ? "selected" :"" }} value="{{$value}}">
                                                    {{$status}}
                                                </option>
                                            @endforeach
                                        
                                        </select>
                                    @endif
                                </div>
                            </div> 
                        @endforeach
                    @endif
        
                    @if($extraPrameters)
                        @foreach($extraPrameters as $key => $param)
                            <div class="col-lg-6">                          
                                <label>{{ ucfirst(str_replace('_',' ', $key)) }}</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="{{ $key }}"
                                    value="{{ old($key, route($param, $method->code )) }}"
                                    class="form-control" disabled>
                                    <span data-text ="{{route($param, $method->code )}}" class="input-group-text pointer copy-text"><i class="las la-copy"></i></span>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="image"> 
                                {{translate('Image')}} <small class="text-danger">({{config("settings")['file_path']['payment_method']['size']}})</small>
                            </label>
                            <input data-size = "{{config('settings')['file_path']['payment_method']['size']}}" id="image" name="image" type="file" class="preview">
                            <div class="mt-2  payment-preview image-preview-section" >
                                <img src='{{imageURL(@$method->file,"payment_method",true)}}' alt="{{@$method->file->name}}" class="payment-image">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-inner">
                            <label for="note">
                                {{translate('Payment Notes')}}
                            </label>                          
                            <textarea  placeholder="{{translate('Enter Payment Notes')}}" name="note" id="note" cols="30" rows="3">{{$method->note}}</textarea>                         
                        </div>
                    </div>

                    @if(request()->route('type')  == 'manual')
                        
                        <div class="col-12 mb-20">
                            <a href="javascript:void(0)" class="i-btn btn--md success" id="addNew"> 
                                <i class="las la-plus me-1"></i> {{translate('Add New Field')}}                     
                            </a>
                        </div>
                        <div class="col-12">
                            <div class="addedField form-inner">
                                @foreach ($parameters as $k => $v)                         
                                    <div class="form-group mb-10">
                                        <div class="input-group">      
                                            <input name="field_name[]" class="form-control"
                                                type="text" value="{{$v->field_label}}" required
                                                placeholder="{{translate('Field Name')}}">
                                            <select name="type[]" class="form-control">
                                                <option value="text"
                                                        @if($v->type == 'text') selected @endif>{{translate('Input Text')}}</option>
                                                <option value="textarea"
                                                        @if($v->type == 'textarea') selected @endif>{{translate('Textarea')}}</option>
                                                <option value="file"
                                                        @if($v->type == 'file') selected @endif>{{translate('File upload')}}</option>
                                            </select>
                                            <select name="validation[]" class="form-control">
                                                <option value="required"
                                                        @if($v->validation == 'required') selected @endif>{{translate('Required')}}</option>
                                                <option value="nullable"
                                                        @if($v->validation == 'nullable') selected @endif>{{translate('Optional')}}</option>
                                            </select>
                                            <span class="input-group-text pointer delete-option  ">
                                                    <i class="las  la-times-circle"></i>
                                            </span>
                                        </div>
                                    </div>
                                @endforeach                           
                            </div>
                        </div> 
                    @endif
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

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";
        
           $(".select2").select2({
			   placeholder:"{{translate('Select Currency')}}",
	     	})
            currency();
            $(document).on('change', '.currency-change', function (){
                $('#convention_rate').val($('.currency-change :selected').attr('data-rate'));
                currency();
            });
            function currency() {
                var currency = $('.currency-change').find("option:selected").text();
                $('.set-currency').text(currency);
            }

            $(document).on('click','#addNew',function (e) {
                e.preventDefault()
                var form = `
                            <div class="form-group mb-10">
                                <div class="input-group">
                                    <input name="field_name[]" class="form-control " type="text" value="" required placeholder="{{translate('Field Name')}}">

                                    <select name="type[]"  class="form-control ">
                                        <option value="text">{{translate('Input Text')}}</option>
                                        <option value="textarea">{{translate('Textarea')}}</option>
                                        <option value="file">{{translate('File upload')}}</option>
                                    </select>

                                    <select name="validation[]"  class="form-control ">
                                        <option value="required">{{translate('Required')}}</option>
                                        <option value="nullable">{{translate('Optional')}}</option>
                                    </select>

                                    <span class="input-group-text pointer delete-option  ">
                                            <i class="las  la-times-circle"></i>
                                    </span>
                                </div>
                            </div>
                            `;

                $('.addedField').append(form)
            });

            $(document).on('click', '.delete-option', function (e) {
                e.preventDefault()
                $(this).closest('.input-group').parent().remove();
            });
            
	})(jQuery);
</script>
@endpush


