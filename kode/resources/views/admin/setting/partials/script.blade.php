<script nonce="{{ csp_nonce() }}">
    "use strict";
  
         $('.colorpicker').colorpicker();
  
          check_login_settings($('#loginWith').val())
  
  
          $(".select2").select2({
              laceholder:"{{translate('Select Option')}}",
          })
          $(".select2-multi").select2({
              laceholder:"{{translate('Select Option')}}",
          })
  
  
          $("#metaKeywords").select2({
              placeholder:"{{translate('Enter Keywords')}}",
              tags: true,
              tokenSeparators: [',']
          })
  
          $(document).on('click','.reset-color',function(e){
  

              e.preventDefault()
              $("[name='site_settings[primary_color]']").val("{{Arr::get(config('site_settings'),'primary_color','#673ab7')}}")
              $("[name='site_settings[secondary_color]']").val("{{Arr::get(config('site_settings'),'secondary_color','#ba6cff')}}")
              $("[name='site_settings[text_primary]']").val("{{Arr::get(config('site_settings'),'text_primary','#26152e')}}");
              $("[name='site_settings[text_secondary]']").val("{{Arr::get(config('site_settings'),'text_secondary','#777777')}}")
              $("[name='site_settings[btn_text_primary]']").val("{{Arr::get(config('site_settings'),'btn_text_primary','#fffff')}}");
              $("[name='site_settings[btn_text_secondary]']").val("{{Arr::get(config('site_settings'),'btn_text_secondary','#24282c')}}")
              toastr("{{translate('Successfully Reseted To Base Color')}}",'success')
  
          });
  
  
  
          // update seettings
          $(document).on('change','#loginWith',function(e){
  
              check_login_settings($(this).val())
              e.preventDefault();
          });
  
  
          function check_login_settings(loginAttribute){
              $('.otp-activation').addClass('d-none');
              if(Array.isArray(loginAttribute) && loginAttribute.length == 1 ){
  
                  if(loginAttribute.includes("phone")){
                      $('.otp-activation').removeClass('d-none')
                  }
              }
          }
  
  
  
          var count = "{{count($ticketSettings)-1}}";
  
          // add more ticket option
          $(document).on('click','#add-ticket-option',function(e){

              e.preventDefault()
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
                                          <option value="number">Number</option>
                                          <option value="date">Date</option>
                                          <option value="textarea">Textarea</option>
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
  
             
          })
  
          //delete ticket options
          $(document).on('click','.delete-option',function(e){

              e.preventDefault()
              $(this).closest("tr").remove()
              count--

          })
  
  </script>