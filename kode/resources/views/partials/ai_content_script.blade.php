<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";
        /** ai content generation event */
        $(document).on('click','.update',function(e){
            e.preventDefault()
            var content = JSON.parse($(this).attr('data-content').replace(/<script.*?>.*?<\/script>/gi, ''));
            var modal = $('#content-form')
            modal.find('#contentForm').attr('action','{{request()->routeIs("user.*") ? route("user.ai.content.update") :route("admin.content.update")}}')
            modal.find('.modal-title').html("{{translate('Update Content')}}")

            modal.find('input[name="name"]').val(content.name)
            modal.find('input[name="id"]').attr('disabled',false)
            modal.find('input[name="id"]').val(content.id)
            modal.find('textarea[name="content"]').val(content.content)
            modal.modal('show')
        })


        $(document).on('click','.template-category',function(e){
            e.preventDefault()
      
            var category = $(this).attr('data-category-id');
            var parent = $(this).attr('data-parent-id');

            $.ajax({
                method:'post',
                url:"{{route('get.template.category')}}",
                dataType: 'json',
                beforeSend: function() {
                    $('.template-category-loader').removeClass('d-none');
                },

                data: {
                    "category_id"     :category,
                    "parent_id"       :parent,
                    "user_id"         :"{{request()->routeIs('user.*') ? $user->id : null}}",
                    "_token"          :"{{csrf_token()}}",
                },
                success: function(response){
                    if(response.status){
                        var cleanContent = DOMPurify.sanitize(response.html);
                        $('.category-section').html(cleanContent)
                    }
                    else{
                        toastr(response.message,'danger')
                    }
                },
                error: function (error){

                    handleAjaxError(error);
                },

                complete: function() {
                    $('.template-category-loader').addClass('d-none');
                },
            })
        })

        $(document).on('click','.select-template',function(e){

            e.preventDefault()
            var id = $(this).attr('data-template-id')
            var url = '{{ route("template.config", ["id" => ":id"]) }}';
            if(id){
                $('#templateId').val(id)
                $('.select-template').removeClass('active');
                $(this).addClass('active')
            }else{
                $('.select-template').removeClass('active');
            }

                url = url.replace(':id', id).replace(':html', true);

                $.ajax({
                    method:'get',
                    url:url,

                    data: {
                      "user_id"            :"{{request()->routeIs('user.*') ? $user->id : null}}",
                      "template_id"         :id,
                    },
                    dataType: 'json',

                    beforeSend: function() {
                        $('.input-section-loader').removeClass('d-none');
                    },

                    success: function(response){
                        if(response.status){
                            var cleanContent = DOMPurify.sanitize(response.html);
                            $(".template-input-section").html(cleanContent);
                        }else{
                            toastr( "Template not found!!",'danger')
                        }
                    },
                    error: function (error){

                        handleAjaxError(error);
                                    
                    },
                    complete: function() {
                        $('.input-section-loader').addClass('d-none');
                    },

                })
        })

       var inputObj = {};
        $(document).on('keyup',".prompt-input",function(e){
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


        $(document).on('click','.resubmit-ai-form',function(e){
             $('.ai-content-form').submit()

             $(this).prop("disabled",true);

             $(this).html(`Generating <span class='ms-1' id="regenarate-loading"><span>&bull;</span><span>&bull;</span><span>&bull;</span></span>`)


        });


        $(document).on('submit','.ai-content-form',function(e){
            var data =   new FormData(this)
            var route =  $(this).attr('data-route')

         

            $.ajax({
            method:'post',
            url: route,
            dataType: 'json',
            beforeSend: function() {

                $('.ai-btn').prop("disabled",true);

                if("{{request()->routeIs('user.*')}}"){

                    $('.ai-btn').html(`{{translate('Generate')}} <i class="bi bi-send  generate-icon-btn"></i>`)
                    $('.ai-btn').html(`{{translate('Generate')}}<div class="spinner-border spinner-border-sm text-white" role="status">
                                        <span class="visually-hidden"></span>
                                    </div>`)
                }
                else{
                        $('.generate-icon-btn').addClass('d-none');
                        $('.ai-btn').addClass('btn__dots--loading');

                        $('.ai-btn').append('<span class="btn__dots"><i></i><i></i><i></i></span>');
                }

            },
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            success: function(response){

                $('.ai-btn').prop("disabled",false);
                $('.resubmit-ai-form').html(`Not satisfy? Retry`);
                $('.resubmit-ai-form').prop("disabled",false);

                if(response.status){


                   
                    $('.ai-modal-title').html('Result')

                    var cleanContent = DOMPurify.sanitize(response.message);


                    $('#content').html(cleanContent)
                    $('#ai-form').hide()
                    $('.ai-content-div').removeClass('d-none')
                }
                else{
                    toastr(response.message,"danger")
                }

            },
            error: function (error){
                $('.resubmit-ai-form').html(`Not satisfy? Retry`);
                $('.ai-btn').prop("disabled",false);
                $('.resubmit-ai-form').prop("disabled",false);

                handleAjaxError(error);


            },
            complete: function() {

                $('.ai-btn').prop("disabled",false);
                $('.resubmit-ai-form').html(`Not satisfy? Retry`);
                $('.resubmit-ai-form').prop("disabled",false);

                if("{{request()->routeIs('user.*')}}"){
                    $('.ai-btn').html(`{{translate('Generate')}}<i class="bi bi-send  generate-icon-btn"></i>`)
                }
                else{
                    $('.generate-icon-btn').removeClass('d-none');
                    $('.ai-btn').removeClass('btn__dots--loading');
                    $('.ai-btn').find('.btn__dots').remove();
                }

            },
            })

            e.preventDefault();
        });

	})(jQuery);
</script>
