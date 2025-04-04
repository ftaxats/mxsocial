<div class="row g-3">
    @forelse ($templates as $template)
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="template-single ai-template-item cursor-pointer" data-id="{{$template->id}}">
                <div class="icon">
                    <i class="bi bi-magic"></i>
                </div>
                <h5>  {{$template->name}} </h5>
                <p> {{$template->description}}</p>
            </div>
        </div>
   @empty
        <div class="custom-template-wrapper default-template-section">
            <h4 class="mb-3">
                {{translate('No templates found')}}
            </h4>
            <div class="image">
                <img src="{{asset('assets/images/default/no-template.jpg')}}"  alt="{{translate('No templates image')}}">
            </div>
            <a href="javascript:void(0)" class="custom-prompt-generate ai-btn i-btn btn--primary {{(request()->routeIs('user.*')) ? "btn--lg capsuled" : "btn--md"}} mx-auto">
                {{translate('Generate content')}}
            </a>
        </div>
   @endforelse
</div>
