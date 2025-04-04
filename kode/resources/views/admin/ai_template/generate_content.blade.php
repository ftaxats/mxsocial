@extends('admin.layouts.master')
@section('content')
    <div class="row g-4 position-relative">
        @include('admin.partials.loader')
        <div class="col-xl-7">
            <form data-route="{{route('admin.ai.template.content.generate')}}" class="ai-content-form" enctype="multipart/form-data" method="post">
                @csrf
                <input type="text" hidden name="id" value="{{$template->id}}">
                <div class="i-card-md">
                    <div class="card--header">
                        <h4 class="card-title">
                            {{translate($template->name)}}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @include('partials.prompt_content')
                            <div class="col-12">
                                <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                                    {{translate("Generate")}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xl-5">
            <div class="i-card-md">
                <div class="card--header">
                    <h4 class="card-title">
                        {{translate("Content Section")}}
                    </h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <form data-route="{{route('admin.content.store')}}" class="content-form" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="Name">
                                        {{translate('Name')}} <small class="text-danger">*</small>
                                    </label>
                                    <input placeholder="{{translate('Enter name')}}" id="Name"  required type="text" name="name" value="">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-inner">
                                    <label for="Content">
                                        {{translate('Content')}} <small class="text-danger">*</small>
                                    </label>
                                    <textarea placeholder='{{translate("Enter Your Content")}}' name="content" id="content" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="i-btn btn--md btn--primary" data-anim="ripple">
                                    {{translate("Save")}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-push')
<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";

        $(".select2").select2({})
        $(document).on('submit','.ai-content-form',function(e){
			 var data =   new FormData(this)
			 var route =  $(this).attr('data-route')
			 $.ajax({
				method:'post',
				url: route,
				dataType: 'json',
                beforeSend: function() {
                     $('.stop-btn').removeClass('d-none');
                     $(".content-loader").removeClass('d-none');
                },
				cache: false,
				processData: false,
				contentType: false,
				data: data,
				success: function(response){

                    if(response.status){

                        var cleanContent = DOMPurify.sanitize(response.message);

                        $('#content').html(cleanContent)
                    }
                    else{
                        toastr(response.message,"danger")
                    }

				},
				error: function (error){
                    handleAjaxError(e);
				},
                complete: function() {
                    $(".content-loader").addClass('d-none');

                },
			 })

			e.preventDefault();
		});

        $(document).on('submit','.content-form',function(e){

            var data =   new FormData(this)
            var route =  $(this).attr('data-route')
            $.ajax({
            method:'post',
            url: route,
            dataType: 'json',
            beforeSend: function() {
                    $('.stop-btn').removeClass('d-none');
                    $(".content-loader").removeClass('d-none');
            },
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            success: function(response){

                if(response.status){

                    toastr(response.message,"success")
                }
                else{
                    toastr(response.message,"danger")
                }

            },
            error: function (error){
                handleAjaxError(e);
            },
            complete: function() {
                $(".content-loader").addClass('d-none');

                $("#Name").val("");
            },
            })

            e.preventDefault();
        });


        var inputObj = {};

        $(document).on('change',".prompt-input",function(e){
            var value = $(this).val();
            var index  = $(this).attr('data-name');
            if(value == ""){
                if (inputObj.hasOwnProperty(index)) {
                    delete inputObj[index];
                }
            }
            else{
                inputObj[index] = value;
            }

            replace_prompt();
        })


        function replace_prompt(){
            var originalPrompt      = $('#promptPreview').attr('data-prompt_input');
            var prompt              = originalPrompt;

            var len = Object.keys(inputObj).length

            if(len > 0){
                for (var index in inputObj) {
                    prompt    = prompt.replace(index,inputObj[index]);
                }
                $('#promptPreview').html(prompt);
            }
            else{
                $('#promptPreview').html($('#promptPreview').attr('data-prompt_input'));
            }
        }

	})(jQuery);
</script>
@endpush
