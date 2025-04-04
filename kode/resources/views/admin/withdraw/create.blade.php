@extends('admin.layouts.master')
@section('content')
    <div class="i-card-md">
        <div class="card-body">
            <form action="{{route('admin.withdraw.store')}}" class="add-listing-form" enctype="multipart/form-data" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="name" class="form-label" >
                                {{translate('Name')}} <small class="text-danger">*</small>
                            </label>
                            <input required type="text" placeholder="{{translate('Enter Name')}}" id="name" name="name" value="{{old('name')}}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="duration" class="form-label">
                                {{translate('Duration')}} <small class="text-danger">* </small>
                            </label>
                            <div class="input-group mb-3">
                                <input required type="number"  placeholder="{{translate('Enter Processing Time')}}" id="duration" name="duration" value="{{old('duration')}}" class="form-control" >
                                <span class="input-group-text"> {{translate("Hours")}} </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="minimum_amount" class="form-label">
                                {{translate('Minimum Amount')}} <small class="text-danger">* </small>
                            </label>
                            <div class="input-group mb-3">
                                <input required type="number" step="any"  placeholder="{{translate('Enter Minimum Amount')}}" id="minimum_amount" name="minimum_amount" value="{{old('minimum_amount')}}" class="form-control" >
                                <span class="input-group-text"> {{(base_currency()->code)}} </span>
                            </div>
                        </div>
                    </div>
                     <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="maximum_amount" class="form-label">
                                {{translate('Maximum Amount')}} <small class="text-danger">* </small>
                            </label>
                            <div class="input-group mb-3">
                                 <input required type="number" step="any"  placeholder="{{translate('Enter Maximum Amount')}}" id="maximum_amount" name="maximum_amount" value="{{old('maximum_amount')}}" class="form-control" >
                                 <span class="input-group-text"> {{(base_currency()->code)}} </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="fixed_charge" class="form-label">
                                {{translate('Fixed Charge')}} <small class="text-danger">* </small>
                            </label>
                            <input required type="number" step="any"  placeholder="{{translate('Enter Amount')}}" id="fixed_charge" name="fixed_charge" value="{{old('fixed_charge')}}" class="form-control"  >
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-inner">
                            <label for="percent_charge" class="form-label">
                                {{translate('Percentage Charge')}} <small class="text-danger">* </small>
                            </label>
                            <input required type="number" step="0.0000001"  placeholder="{{translate('Enter Number')}}" id="percent_charge" name="percent_charge" value="{{old('percent_charge')}}"  class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-inner">
                            <label for="image">
                                {{translate('Image')}} <small class="text-danger">({{config("settings")['file_path']['withdraw_method']['size']}})</small>
                            </label>
                            <input data-size = "{{config('settings')['file_path']['withdraw_method']['size']}}" id="image" name="image" type="file" class="preview" >
                            <div class="mt-2 image-preview-section">
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-inner">
                            <label for="note">
                                {{translate('Notes')}}
                            </label>
                            <textarea  placeholder="{{translate('Enter Description Notes')}}" name="note" id="note" cols="3" rows="3">{{old("note")}}</textarea>
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
                                        <option value="password">{{translate('Encrypted Field')}}</option>
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






