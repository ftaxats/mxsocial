@extends('admin.layouts.master')

@section('content')

    <div class="i-card-md">
        <div class="card-body">
            <form action='{{route("admin.paymentMethod.store","manual")}}' class="add-listing-form" enctype="multipart/form-data" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="name">
                                {{translate('Name')}} <small class="text-danger">*</small>
                            </label>                           
                            <input required type="text"  placeholder="{{translate('Enter name')}}" id="name" name="name" value="{{old('name')}}"  >       
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="serialId">
                                {{translate('Serial Id')}} <small class="text-danger">*</small>
                            </label>  
                            <input required type="number" placeholder="{{translate('Enter Serial Id')}}" min="0" id="serialId" name="serial_id" value="{{old('serial_id')}}" >                     
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="currency">
                                {{translate('Currency')}} <small class="text-danger">*</small>
                            </label>  
                            <select class="select2 form-select currency-change" id="currency" name="currency_id" >
                                @foreach($currencies as $currency)
                                    <option data-rate ="{{exchange_rate($currency,4)}}" value="{{$currency->id}}">
                                         {{$currency->code}}
                                    </option>
                                @endforeach
                            </select>
                        </div>                       
                    </div>
                    <div class="col-lg-6"> 
                        <div class="form-inner">                   
                            <label for="convention_rate">
                                {{translate('Convertion/Exchange Rate')}} <small class="text-danger">*</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">  1  {{session()->get('currency')?->code}} =</span>
                                <input disabled type="number" min="0" step="any" id="convention_rate"  class="form-control "
                                name="exchange_rate">
                                <span class="input-group-text set-currency"></span>
                            </div>
                        </div>                
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="minimum_amount" class="form-label">
                                {{translate('Minimum Amount')}} <small class="text-danger">* ({{translate('In')}} {{(base_currency()->code)}}) </small>
                            </label>
                            <input required type="number" step="any"  placeholder="{{translate('Enter Minimum Amount')}}" id="minimum_amount" name="minimum_amount" value="{{old('minimum_amount')}}" >
                        </div>
                    </div>
                     <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="maximum_amount" class="form-label">
                                {{translate('Maximum Amount')}} <small class="text-danger">* ({{translate('In')}} {{(base_currency()->code)}})</small>
                            </label>
                            <input required type="number" step="any"  placeholder="{{translate('Enter Maximum Amount')}}" id="maximum_amount" name="maximum_amount" value="{{old('maximum_amount')}}" >
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="percentage_charge">
                                {{translate('Percentage Charge
                                ')}} <small class="text-danger">*</small>
                            </label>
                            <input required placeholder='{{translate("Enter Percentage Charge")}}' type="number" min="0" step="any"  id="percentage_charge" name="percentage_charge" value='{{old("percentage_charge")}}'  >                     
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="fixed_charge">
                                {{translate('Fixed Charge
                                ')}} <small class="text-danger">*</small>
                            </label>
                
                            <input required placeholder='{{translate("Enter Fixed Charge")}}' type="number" min="0" step="any"  id="fixed_charge" name="fixed_charge" value='{{old("fixed_charge")}}'  >                          
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-inner">
                            <label for="image">
                                {{translate('Image')}} <small class="text-danger">({{config("settings")['file_path']['payment_method']['size']}})</small>
                            </label>
                        
                            <input data-size = "{{config('settings')['file_path']['payment_method']['size']}}" id="image" name="image" type="file" class="preview" >
    
                            <div class="mt-2 image-preview-section">
                    
                            </div>
                        </div>                     
                    </div>
                    <div class="col-12">
                        <div class="form-inner">
                            <label for="note">
                                {{translate('Payment Notes')}}
                            </label>
                            <textarea  placeholder="{{translate('Enter Payment Notes')}}" name="note" id="note" cols="3" rows="3">{{old("note")}}</textarea> 
                        </div>
                    </div>
                    <div class="col-12 mb-20">
                        <a href="javascript:void(0)" class="i-btn btn--md success" id="addNew">  <i class="las la-plus me-1"></i> {{translate('Add New Field')}}</a>
                    </div>  
                    <div class="col-12">
                        <div class="addedField form-inner">  
                        </div>
                    </div>
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
        
            currency();

            $(document).on('change', '.currency-change', function (){
                currency();
            });
            function currency() {
                var currency = $('.currency-change').find("option:selected").text();
                $('.set-currency').text(currency);
                $('#convention_rate').val($('.currency-change :selected').attr('data-rate'));

            }

            $(".select2").select2({
			   placeholder:"{{translate('Select Currency')}}",
	     	})

            $(document).on('click','#addNew',function (e) {
                e.preventDefault()
                var form = `
                            <div class="form-group mb-10">
                                <div class="input-group">
                                    <input name="field_name[]" class="form-control" type="text" value="" required placeholder="{{translate('Field Name')}}">

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
                            </div>`;

                $('.addedField').append(form)
            });

            $(document).on('click', '.delete-option', function (e) {
                e.preventDefault()
                $(this).closest('.input-group').parent().remove();
            });

	})(jQuery);
</script>
@endpush
