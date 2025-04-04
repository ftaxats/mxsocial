
<div  class="content-section">

    @php
        $generateRoute = route('admin.ai.template.content.generate');
        $iconClass  = "las la-question-circle text--danger";

        if(request()->routeIs('user.*')){
            $generateRoute  =  route('user.ai.content.generate');
            $iconClass      = "bi bi-info-circle text--danger";
        }
    @endphp

    <div class="@if(!@$modal) @endif @if(@$modal) border-0 @endif" id="ai-form">

        @if(!@$modal)
            <div class="{{request()->routeIs('user.*') ? 'card-header' :'card--header' }} mb-4">
                <h4 class="card-title">
                    {{translate("Categories")}}
                </h4>
            </div>
        @endif

        <form data-route="{{$generateRoute}}" class="ai-content-form {{request()->routeIs('user.*') ? 'p-4 pt-0' :'' }}" >

            @csrf

            <input type="hidden" name="id" id="templateId">

            <div class="row g-4 template-selection-section">
                <div class="col-xl-4 col-lg-5">
                    <div class="template-sidebar" data-simplebar>

                        @include('admin.partials.card_loader',['customer_class' => "template-category-loader"])

                        <div class="template-categories category-section">
                            @include("partials.template.list",['categories' => $categories])
                        </div>


                    </div>
                </div>

                <div class="col-xl-8 col-lg-7 position-relative">

                     @include('admin.partials.card_loader' ,['customer_class' => 'input-section-loader'])

                    <div class="template-input-section">

                        <h4 class="lh-1 mb-3">
                            {{translate('Generate Content')}}
                        </h4>

                        <div class="ai-from-wrapper template-prompt">

                            <input type="hidden"
                                    value="{{App\Enums\StatusEnum::true->status()}}"
                                    name="custom_prompt" id="custom_prompt">

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

                                            <label for="language-input" class="form-label">
                                                {{translate('Output Language')}} <small class="text-danger">*</small>
                                            </label>

                                            <select name="language" class="form-select" id="language-input">


                                                <option value="">
                                                    {{translate("Select language")}}
                                                </option>

                                                @foreach (getAILanguages() as $code => $language )
                                                    <option {{session()->get('locale') == $code ? "selected" :"" }} value="{{$language}}">
                                                        {{$language}}
                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="col-xxl-3 col-md-6">
                                            <label for="max_result" class="form-label">
                                                {{translate("Results Length")}} <i  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{translate('Maximum words for each result')}}"  class="ms-1 pointer {{$iconClass}}"></i>
                                                @if(request()->routeIs('user.*'))
                                                    <span class="text--danger">*</span>
                                                @endif
                                            </label>
                                            <input @if(request()->routeIs('user.*')) required @endif   placeholder="{{translate('Enter number')}}" type="number" min="1"
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

                            <div class="d-flex gap-3 justify-content-start    w-100 mt-4">
                                <button type="submit" class="i-btn btn--primary {{(request()->routeIs('user.*')) ? "btn--lg capsuled" : "btn--md"}}   postSubmitButton ai-btn gap-2">
                                    {{translate('Generate')}}
                                    <i class="bi bi-send  generate-icon-btn"></i>
                                </button>                     
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    @if(!@$modal)
        <div class="d-none ai-content-div">
            <div class="{{request()->routeIs('user.*') ? 'card-header' :'card--header' }}">
                <h4 class="card-title">
                    {{translate("Content")}}
                </h4>
            </div>

            <div class="row {{request()->routeIs('user.*') ? 'p-4' :'' }}">
                <div class="col-lg-12 d-flex justify-content-end">
                    @if(request()->routeIs('admin.*'))
                        <div class="action">
                            <a href="{{route('admin.content.list')}}"    class="i-btn btn--sm success">
                                <i class="las la-arrow-left me-1"></i>  {{translate('Back')}}
                            </a>
                        </div>
                    @else
                        <a href="{{route('user.ai.content.list')}}" class="i-btn primary btn--sm capsuled">
                            <i class="bi bi-arrow-left"></i>
                            {{translate('Back')}}
                        </a>
                    @endif
                </div>

                <form action="{{$content_route}}" class="content-form" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="col-lg-12">
                        <div class="form-inner">
                            <label for="Name">
                                {{translate('Name')}} <small class="text-danger">*</small>
                            </label>
                            <input placeholder="Enter name" id="Name" required="" type="text" name="name" value="">
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-inner">
                            <label for="content">
                                {{translate("Content")}} <small class="text-danger">*</small>
                            </label>
                            <textarea placeholder="Enter Your Content" name="content" id="content" cols="30" rows="10"></textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class=" {{request()->routeIs('user.*') ? 'i-btn btn--lg btn--primary capsuled' : 'i-btn btn--md btn--primary'}}  " data-anim="ripple">
                            {{translate("Save")}}
                            @if(request()->routeIs('user.*'))
                                <span><i class="bi bi-arrow-up-right"></i></span>
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="d-none ai-content-div">
            <div class="content-form">
                <div class="form-inner mb-0">
                    <textarea placeholder="Enter Your Content" name="content" id="content" cols="30"
                        rows="10"></textarea>
                </div>

                <div class="text-end mt-4">
                    <div class="d-flex gap-2 align-items-center justify-content-end flex-wrap">
                        <button data-anim="ripple"
                        class="{{request()->routeIs('user.*') ? 'i-btn primary btn--sm gap-2 capsuled' : 'i-btn btn--primary-transparent btn--sm  gap-2'}}  insert-text">
                            <i class="bi bi-box-arrow-down"></i>
                            {{translate("Insert")}}
                       </button>

                        <button data-anim="ripple"
                            class="{{request()->routeIs('user.*') ? 'i-btn btn--success-transparent btn--sm gap-2 capsuled' : 'i-btn btn--success-transparent btn--sm  gap-2'}} copy-content">
                            <i class="bi bi-clipboard-check"></i>
                            {{translate("Copy")}}
                        </button>

                        <button data-anim="ripple"
                            class="{{request()->routeIs('user.*') ? 'i-btn btn--info-transparent btn--sm gap-2 capsuled' : 'i-btn btn--info-transparent btn--sm  gap-2'}} download-text">
                            <i class="bi bi-download"></i>
                            {{translate("Download")}}
                        </button>
                    </div>

                    <div class="mt-4 d-flex align-items-center justify-content-end">
                        <button
                            class="bg-transparent p-0 text-danger fw-normal resubmit-ai-form d-flex align-items-center lh-1">
                            {{translate("Not satisfy? Retry")}}
                        
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
