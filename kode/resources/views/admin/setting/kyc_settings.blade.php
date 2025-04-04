@extends('admin.layouts.master')

@section('content')

@php
	$kycSettings    = !is_array(site_settings('kyc_settings',[])) ?  json_decode(site_settings('kyc_settings',[]),true) : [];
@endphp

<form data-route="{{route('admin.setting.kyc.store')}}"  class="settingsForm"  method="POST" enctype="multipart/form-data">
    @csrf
    <div class="i-card-md">
        <div class="card--header">
            <div class="action">
                <button id="add-kyc-option" class="i-btn btn--sm success">
                    <i class="las la-plus me-1"></i>   {{translate('Add More')}}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-12">
                    <div class="table-container">
                        <table class="align-middle">
                            <thead class="table-light ">
                                <tr>
                                    <th scope="col">
                                        {{translate('Labels')}}
                                    </th>

                                    <th scope="col">
                                        {{translate('Type')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Mandatory/Required')}}
                                    </th>

                                    <th scope="col">
                                        {{translate('Placeholder')}}
                                    </th>

                                    <th scope="col">
                                        {{translate('Action')}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="ticketField">
                                @foreach ($kycSettings as $input)
                                    <tr>
                                        <td data-label='{{translate("Label")}}'>
                                            <div class="form-inner mb-0">
                                                <input type="text" name="custom_inputs[{{$loop->index}}][labels]"  value="{{$input['labels']}}">
                                            </div>
                                        </td>
                                        <td data-label='{{translate("Type")}}'>
                                            <div class="form-inner mb-0">

                                                @if($input['default'] == App\Enums\StatusEnum::true->status())
                                                    <input disabled type="text" name="custom_inputs[type]"  value="{{$input['type']}}">
                                                    <input type="hidden" name="custom_inputs[{{$loop->index}}][type]"  value="{{$input['type']}}">
                                                @else
                                                <select  class="form-select" name="custom_inputs[{{$loop->index}}][type]" >
                                                    @foreach(['file','textarea','text','date','email'] as $type)
                                                        <option {{$input['type'] == $type ?'selected' :""}} value="{{$type}}">
                                                            {{ucfirst($type)}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @endif

                                            </div>
                                        </td>
                                        <td  data-label='{{translate("Required")}}' >
                                            <div class="form-inner mb-0">
                                                @if($input['default'] == App\Enums\StatusEnum::true->status() && $input['type'] != 'file' )
                                                    <input disabled  type="text" name="custom_inputs[required]"  value="{{$input['required'] == App\Enums\StatusEnum::true->status()? 'Yes' :'No'}}">
                                                    <input hidden  type="text" name="custom_inputs[{{$loop->index}}][required]"  value="{{$input['required']}}">
                                                @else
                                                    <select class="form-select" name="custom_inputs[{{$loop->index}}][required]" >
                                                        <option {{$input['required'] == App\Enums\StatusEnum::true->status() ?'selected' :""}} value="{{App\Enums\StatusEnum::true->status()}}">
                                                            {{translate('Yes')}}
                                                        </option>
                                                        <option {{$input['required'] == App\Enums\StatusEnum::false->status() ?'selected' :""}} value="{{App\Enums\StatusEnum::false->status()}}">
                                                            {{translate('No')}}
                                                        </option>
                                                    </select>
                                                @endif
                                            </div>
                                        </td>
                                        <td  data-label='{{translate("Placeholder")}}'>
                                            <div class="form-inner mb-0">
                                                <input type="text" name="custom_inputs[{{$loop->index}}][placeholder]"  value="{{$input['placeholder']}}">
                                            </div>
                                            <input   type="hidden" name="custom_inputs[{{$loop->index}}][default]"  value="{{$input['default']}}">
                                            <input   type="hidden" name="custom_inputs[{{$loop->index}}][multiple]"  value="{{$input['multiple']}}">
                                            <input   type="hidden" name="custom_inputs[{{$loop->index}}][name]"  value="{{$input['name']}}">
                                        </td>
                                        <td data-label='{{translate("Option")}}'>
                                            @if($input['default'] == App\Enums\StatusEnum::true->status())
                                                --
                                            @else
                                                <div>
                                                    <a href="javascript:void(0);" class="pointer icon-btn danger delete-option">
                                                        <i class="las la-trash-alt"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-20">
                        <button type="submit" class="i-btn ai-btn btn--md btn--primary" data-anim="ripple">
                            {{translate("Submit")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
  "use strict";

        var count = "{{count($kycSettings)-1}}";
		// add more kyc option
		$(document).on('click','#add-kyc-option',function(e){
			count++
			var html = `<tr>
							<td data-label="{{translate("label")}}">
                                <div class="form-inner mb-0">
								  <input placeholder="{{translate("Enter Label")}}" type="text" name="custom_inputs[${count}][labels]" >
                                </div>
							</td>

							<td data-label="{{translate("Type")}}">
                                <div class="form-inner mb-0">
                                    <select class="form-select" name="custom_inputs[${count}][type]" >
                                        <option value="text">Text</option>
                                        <option value="email">Email</option>
                                        <option value="date">Date</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="file">File</option>
                                    </select>
                                </div>
							</td>
							<td data-label="{{translate("Required")}}">
                                <div class="form-inner mb-0">
                                    <select class="form-select" name="custom_inputs[${count}][required]" >
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
							</td>
							<td data-label="{{translate("placeholder")}}">
                                <div class="form-inner mb-0">
                                    <input placeholder="{{translate("Enter Placeholder")}}"  type="text" name="custom_inputs[${count}][placeholder]" >
                                    <input  type="hidden" name="custom_inputs[${count}][default]"  value="0">
                                    <input  type="hidden" name="custom_inputs[${count}][multiple]"  value="0">
                                    <input  type="hidden" name="custom_inputs[${count}][name]"  value="">
                                </div>
							</td>
							<td data-label='{{translate("Option")}}'>
							   <div >
                                    <a href="javascript:void(0);" class="pointer icon-btn danger delete-option">
                                         <i class="las la-trash-alt"></i>
                                    </a>
                                </div>
							</td>

						</tr>`;
				$('#ticketField').append(html)

			e.preventDefault()
		})
        //delete ticket options
		$(document).on('click','.delete-option',function(e){
            e.preventDefault()
			$(this).closest("tr").remove()
			count--

		})
</script>
@endpush
