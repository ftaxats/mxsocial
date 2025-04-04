@extends('admin.layouts.master')
@section('content')
	<div class="i-card-md">
		<div class="card-body">
			<div class="search-action-area">
				<div class="row g-4">
					<div class="col-md-4 d-flex ">
						<div class="search-area">
							<div class="form-inner">
								<input id="search-key" type="search" placeholder="{{translate('Search here !!')}}" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="table-container">
				<table id="transaltionTable">
					<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col">
								{{translate('Key')}}
							</th>
							<th scope="col">
								{{translate('Value')}}
							</th>

							<th scope="col">
								{{translate('Options')}}
							</th>
						</tr>
					</thead>
					<tbody>
						@forelse ($translations as $translate)
							<tr>
								<td data-label="#">
									{{$loop->iteration}}
								</td>
								<td data-label='{{translate("Key")}}'>
									{{limit_words($translate->value,20)}}
								</td>
								<td data-label='{{translate("Value")}}'>
									<div class="form-inner">
										<input  id="lang-key-value-{{ $loop->iteration }}" name='translate[{{$translate->key }}]' value="{{ $translate->value }}" type="text">
									</div>
								</td>
								<td data-label='{{translate("Options")}}'>
									<div class="table-action">
										<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Save')}}" href="javascript:void(0)" data-translate-id ="{{$translate->id}}" data-id ="{{$loop->iteration}}"  title="save" class="translate icon-btn success">
											<i class="las la-save"></i>
									   </a>
										@if(check_permission('delete_language'))
											<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Delete')}}"  href="javascript:void(0);" data-href="{{route('admin.language.destroy.key',$translate->uid)}}" class="delete-item icon-btn danger">
												<i class="las la-trash-alt"></i></a>
										@endif
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td class="border-bottom-0" colspan="4">
									@include('admin.partials.not_found')
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
			<div class="Paginations">
                {{ $translations->links() }}
			</div>
		</div>
	</div>
@endsection

@section('modal')
    @include('modal.delete_modal')
@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}" >
	(function($){
       	"use strict";
        //search result
        $(document).on('keyup','#search-key',function(e){
            var value = $(this).val().toLowerCase();
            if(value){
                $('.Paginations').addClass('d-none')
            }
            else{
                $('.Paginations').removeClass('d-none')
            }
            $("#transaltionTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        })

        // save translation
        $(document).on('click','.translate',function(e){
			e.preventDefault()
			const event  =  $(this);
            var id  = $(this).attr('data-id')
            var tranId  = $(this).attr('data-translate-id')
            var value = $(`#lang-key-value-${id}`).val()
            updateLangKeyValue(tranId,value ,event)
        })

        function updateLangKeyValue(tranId,value ,event){
          const data = {
            "id":tranId,
            "value":value,
          }
          $.ajax({
            method:'post',
            url: "{{ route('admin.language.tranlateKey') }}",
			beforeSend: function() {

							$(event).html(`<div class="spinner-border spinner-border-sm" role="status">
									<span class="visually-hidden">Loading...</span>
									</div>`);
                        },
            data:{
			  "_token" :"{{csrf_token()}}",
              data
            },
            dataType: 'json',

			complete: function() {
				               $(event).html(`<i class="las la-save"></i>`)
                        },
          }).then(response => {
                if(response.success){
                    toastr("{{translate('Successfully Translated')}}",'success')
                }
                else{
                    toastr("{{translate('Translation Failed')}}",'danger')
                }
          }).catch(e => {
			    handleAjaxError(e);
		      
		  })
        }
	})(jQuery);

</script>
@endpush

