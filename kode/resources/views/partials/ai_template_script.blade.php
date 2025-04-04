<script nonce="{{ csp_nonce() }}">
	(function($){
       	"use strict";
            var counter =  0;
            $(document).on('click','#addNew',function (e) {

                e.preventDefault()
                counter+=1;
                var form = `
                            <div class="form-group mb-10">
                                <div class="input-group">

                                    <input name="field_name[]" data-counter = ${counter} class="form-control field-name" type="text" value="" required placeholder="{{translate('Enter Field Name')}}">

                                    <input name="field_label[]" data-counter = ${counter} class="form-control field-label" type="text" value="" required placeholder="{{translate('Enter Field Label')}}">


                                <input name="instraction[]" data-counter = ${counter} class="form-control field-label" type="text" value="" required placeholder="{{translate('Enter Instruction')}}">

                                    <select name="type[]"  class="form-control ">
                                        <option value="text">{{translate('Input Text')}}</option>
                                        <option value="textarea">{{translate('Textarea')}}</option>
                                    </select>

                                    <select name="validation[]"  class="form-control ">
                                        <option value="required">{{translate('Required')}}</option>
                                        <option value="nullable">{{translate('Optional')}}</option>
                                    </select>

                                    <span data-counter = ${counter} class="input-group-text pointer delete-option  ">
                                            <i class="las  la-times-circle"></i>
                                    </span>
                                </div>
                            </div>
                            `;

                $('.addedField').append(form)
            });

            $(document).on('click', '.delete-option', function (e) {
                e.preventDefault()
                var index  = $(this).attr('data-counter');
                if (inputObj.hasOwnProperty(index)) {
                        delete inputObj[index];
                }
                $(this).closest('.input-group').parent().remove();

                set_hint();
            });

            var inputObj = {};
            $(document).on('change', '.field-name', function () {
                var value = $(this).val().toLowerCase().trim().replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '_').replace(
                /^-+|-+$/g, '');
                var index  = $(this).attr('data-counter');
                if(value == ""){
                    if (inputObj.hasOwnProperty(index)) {
                        delete inputObj[index];
                    }
                }
                else{
                    inputObj[index] = value;
                }
                set_hint();
            });

            function set_hint(){
                var len = Object.keys(inputObj).length
                if(len > 0){
                    $('.input-hint').removeClass('d-none');

                    var html = '';
                    for (var index in inputObj) {
                        var name = `{${inputObj[index]}}`;
                        html += `<span data-name = "${name}" class='custom-key i-badge capsuled success pointer me-3'>${name}</span>`;
                    }
                    if(html != ""){
                        $(".input-var").html(html);
                    }
                    else{
                        $(".input-var").html('');
                    }
                }
                else{
                    $(".input-var").html('');
                    $('.input-hint').addClass('d-none');
                }
            }

            $(document).on('click', '.custom-key', function (e) {

                e.preventDefault()
                var key = $(this).attr("data-name");
                var custom_prompt = $('textarea[name="custom_prompt"]').val();
                $('textarea[name="custom_prompt"]').val(custom_prompt + key);
            });

	})(jQuery);
</script>
