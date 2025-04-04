@php
    $iconClass  = "las la-question-circle text--danger";
    if(@$user)  $iconClass      = "bi bi-info-circle text--danger";
@endphp

    <input type="hidden" value="{{App\Enums\StatusEnum::false->status()}}" name="custom_prompt" id="custom_prompt">
    <div class="template-input-section">
         <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="lh-1">{{translate('Generate Content')}}</h4>
           <a href="javascript:void(0)" class="i-btn btn--sm danger select-template">
               <i class="bi bi-arrow-repeat"></i>
            </a>
         </div>

        @if (@$template)
            <ul class="ai-post-meta-list">
                @php
                    $category =  $template->category;
                    $subCategory =  $template->subCategory;
                    if( $subCategory){
                        $category =    $template->subCategory;
                    }
                @endphp
                <li><span> {{translate('Category')}} :</span> <i class="{{$category->icon}}"></i> {{$category->title}}  </li>
                <li><span> {{translate('Template')}}  :</span> <i class="{{$template->icon}}"></i> {{$template->name}} </li>
            </ul>
        @endif

        <div class="ai-from-wrapper template-prompt">
            <input type="hidden" value="{{App\Enums\StatusEnum::true->status()}}" name="custom_prompt" id="custom_prompt">
            @if(@$template && @$template->prompt_fields)
                @foreach($template->prompt_fields as $key => $input)

                    <div class="mb-3">
                        <label for="{{$key }}">
                            {{@$input->field_label}}
                            @if(@$input->validation == 'required') <small class="text-danger">*</small> @endif
                            @if(@$input->instraction)
                                <span class="custom--tooltip">
                                    <i  class="bi bi-info-circle-fill text--info"></i>
                                    <span class="tooltip-text">
                                        {{@$input->instraction}}
                                    </span>
                                </span>
                            @endif
                        </label>

                        @if ($input->type == "text")
                            <input data-name="{{ '{'.@$input->field_name.'}'}}"     placeholder="{{@$input->field_label}}" name="custom[{{@$input->field_name}}]" {{@$input->validation == 'required' ? 'required' : ''}} type="text" id="{{ $key }}"  class="prompt-input"  value="{{old('custom'.@$input->field_name)}}">
                        @else
                            <textarea data-name=" '{' . {{ @$input->field_name }} . '}' "     placeholder="{{@$input->field_label}}" name="custom[{{@$input->field_name}}]" {{@$input->validation == 'required' ? 'required' : ''}} type="text" id="{{ $key }}"  class="prompt-input">{{old('custom'.@$input->field_name)}}</textarea>
                        @endif
                    </div>

                @endforeach

            @endif

            <div class="mb-3">
                <label for="promptPreview">
                    {{ translate('Prompt')}}  <span class="text--danger" >*</span>
                </label>
                <textarea required  @if (@$template) data-prompt_input="{{$template->custom_prompt}}" readonly @else placeholder="{{translate('Enter your prompt')}}" @endif name="custom_prompt_input"   id="promptPreview" cols="5" rows="5">@if(@$template){{$template->custom_prompt}} @endif</textarea>
            </div>

            <div class="content-gen-right">
                <h6 class="mb-2">
                    {{translate('Advanced Option')}}
                </h6>

                <div class="advnced-option-card">

                    <div class="row g-3">
                        <div class="col-xxl-3 col-md-6">

                            <label for="language-change">
                                {{translate('Output language')}} <small class="text-danger">*</small>
                            </label>

                            <select name="language" class="form-select" id="language-change">

                                @foreach (getAILanguages() as $code => $language )
                                    <option {{session()->get('locale') == $code ? "selected" :"" }} value="{{$language}}">
                                        {{$language}}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-xxl-3 col-md-6">
                            <label for="max_result">
                                {{translate("Results Length")}}
                                @if(request()->routeIs('user.*') || @$user || $is_user_request)
                                    <span class="text--danger">*</span>
                                @endif

                                <span class="custom--tooltip">
                                    <i  class="bi bi-info-circle-fill text--info"></i>

                                    <span class="tooltip-text">
                                        {{translate('Maximum words for each result')}}
                                    </span>
                                </span>
                            </label>
                            <input @if(request()->routeIs('user.*') || @$user || $is_user_request) required @endif   placeholder="{{translate('Enter number')}}" type="number" min="1"
                            id="max_result" name="max_result"  value='500' >
                        </div>

                        <div class="col-xxl-3 col-md-6">
                            <label for="content_tone" class="form-label">{{ translate('Content Tone') }} </label>
                            <select  class="form-select" id="content_tone" name="content_tone">
                                    <option value="">
                                        {{translate("Select Tone")}}
                                    </option>
                                    @foreach (Arr::get(config('settings'),'ai_default_tone',[]) as $v )
                                            <option {{old("content_tone") == $v ? 'selected' :""}} value="{{$v}}">
                                                {{ $v }}
                                            </option>
                                    @endforeach
                            </select>
                        </div>

                        <div class="col-xxl-3 col-md-6">
                            <label for="ai_creativity" class="form-label">
                                {{ translate('AI Creativity Level') }}
                            </label>
                            <select class="form-select" id="ai_creativity" name="ai_creativity" >
                                <option  value="">
                                    {{translate("Select Creativity")}}
                                </option>
                                @foreach (Arr::get(config('settings'),'default_creativity',[]) as $k => $v )
                                    <option  value="{{$v}}" >
                                        {{ $k }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3  w-100 mt-4">
                 
                 
                <button type="submit" class="i-btn btn--primary {{ @$user || $is_user_request  ? "btn--lg capsuled" : "btn--md"}} postSubmitButton ai-btn me-2">
                    {{translate('Generate')}}
                    <i class="bi bi-send ms-2 generate-icon-btn"></i>
                </button>
            </div>
        </div>
    </div>

